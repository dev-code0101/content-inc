# API Routes — MicroService Services

Note: two services are involved

- Laravel Posts API (primary source of truth) — runs at `http://localhost:8000`
- - Ingestion / Scraper Service (job runner + scheduler + logs) — runs at `http://localhost:5000`
- Orchestrator / Transformation Service (job runner + scheduler + logs) — runs at `http://localhost:4000`

---

## 1. Laravel Posts API (Content Store)

| Method | Endpoint                     | Description                                   | Example |
|--------|------------------------------|-----------------------------------------------|---------|
| GET    | `/api/categories`            | List all categories                           | |
| GET    | `/api/categories/{id}`       | List posts in a category                      | |
| GET    | `/api/posts`                 | List all posts (original + updated)           | `?q=search&page=2` |
| GET    | `/api/posts/{id}`            | Get a specific post                           | |
| POST   | `/api/posts`                 | Create a new (transformed) post               | See payload below |
| GET    | `/api/posts/{id}/versions`   | List transformed versions for a post          | |
| GET    | `/api/posts/{id}/versions/{v}`| Get a specific transformed version            | `v` = version id or slug |

**Publish payload (sent by the Scraper Microservice):**

```json
{
  "user_id": 3,
  "category_id": 2,
  "title": "Sample Article Title (Updated)",
  "content": "<h1>…</h1>\n\n## References\n- https://example.com/article-1\n- https://example.com/article-2",
  "status": true,
  "slug": "sample-article-title-updated",
  "image": null
}
```

*Security:* protect `POST /api/posts` with an API token so only the orchestrator/scraper can publish.

---

## 2. Blog Scraper Microservice (FastAPI + Celery)

Base URL: **`http://localhost:4000`**

| Method | Endpoint      | Description & Behavior                                                                 | Notes |
|--------|---------------|------------------------------------------------------------------------------------------|-------|
| **POST** | `/scrape` | Enqueue a scrape‑and‑publish job. Returns a Celery `task_id`. Rejects with **409** if a job is already running. | Returns `{ "task_id": "...", "status": "queued" }` |
| GET    | `/health`    | Simple health check – returns 200 if the API and Redis are reachable.                  | Useful for orchestration / Kubernetes liveness probes |

**Important behavior**

* **Single‑run policy** – a Redis lock (`scrape_job_lock`) guarantees that only one scrape job can be active at a time. Subsequent `POST /scrape` calls while the lock exists receive **409 Conflict**.  
* **Background processing** – the request returns immediately; the heavy work runs in a Celery worker (`celery -A app.tasks.celery_app worker`).  
* **Logging** – all steps (page fetch, article extraction, API post) are written to `logs/app.log` (rotating file) and to stdout.  
* **Result** – after the worker finishes, the lock is released automatically.

---

## 3. Orchestrator / Transformation Service (Runner, Scheduler, Queue, Logs)

Base URL: **`http://localhost:5000`**

| Method | Endpoint                     | Description / Behavior                                            | Notes |
|--------|------------------------------|--------------------------------------------------------------------|-------|
| POST   | `/api/transform/run`         | Enqueue & run a transformation job (calls the Scraper service).  | Body: `{ "async": true, "post_id"?: number }` – returns `jobId`. Rejects with **409** if another job is running. |
| POST   | `/api/transform/schedule`    | Create a recurring cron job to run transformations.               | Body: `{ "cron": "*/30 * * * *", "post_id"?: number }` |
| GET    | `/api/transform/jobs`        | List recent transform jobs and status.                             | Returns job metadata (`id`, `status`, timestamps, `error`). |
| GET    | `/api/transform/jobs/{id}`   | Get job detail + logs.                                             | Logs include timestamped lines from that run. |
| POST   | `/api/transform/cancel/{id}`| Cancel a scheduled job (if implemented).                          | Optional – requires scheduler support. |
| GET    | `/health`                    | Health check for orchestrator service.                             | Reports queue status, active‑job flag. |

**Interaction flow**

1. **Client** → `POST /api/transform/run` (or a scheduled cron triggers it).  
2. Orchestrator → `POST http://localhost:4000/scrape` (scraper microservice).  
3. Scraper microservice runs the Celery task, logs progress, and **POSTs** each article to the Laravel Posts API (`/api/posts`).  
4. Orchestrator records the Celery `task_id` as its own `jobId` and updates job status/logs accordingly.

---

## 4. Service Responsibility Matrix (Updated)

| Concern                      | Laravel API (posts) | Orchestrator Service | **Blog Scraper Microservice** |
|-----------------------------:|:-------------------:|:--------------------:|:-----------------------------:|
| Store original posts         | ✅                  |                      | |
| Store transformed versions   | ✅ (via POST)       |                      | |
| Trigger transformations      |                     | ✅                   | |
| Search & scrape competitors  |                     | ✅ (calls)           | **✅ (actual scraper)** |
| LLM transformation           |                     | ✅                   | |
| Queueing & scheduling        |                     | ✅ (cron/queue)      | **✅ (Celery + Redis)** |
| Job logs & status            |                     | ✅ (per‑job logs)    | **✅ (rotating file + Redis lock)** |
| Publish transformed article  | ✅ (receives POST)  | ✅ (calls POST)      | **✅ (POST to Laravel)** |

---
