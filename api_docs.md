
# API Routes — MicroService Services

Note: two services are involved

- Laravel Posts API (primary source of truth) — runs at `http://localhost:8000` (example)
- Orchestrator / Transformation Service (job runner + scheduler + logs) — runs at `http://localhost:4000` (example)

---

## Laravel Posts API (Content store)

Responsible for storing original posts and published transformed versions.

| Method | Endpoint                    | Description                                 | Notes / Example |
|--------|-----------------------------|---------------------------------------------|-----------------|
| GET    | /api/categories             | List all categories                         | |
| GET    | /api/categories/{id}        | List posts in a category                    | |
| GET    | /api/posts                  | List all posts (original + updated items)   | Supports query params: ?q=search, ?page= |
| GET    | /api/posts/{id}             | Get a specific post                         | Returns original or a wrapper with versions |
| POST   | /api/posts                  | Create a new post                           | Body: { user_id, category_id, title, content, status, slug, image } — used by orchestrator to publish transformed articles |
| GET    | /api/posts/{id}/versions    | List transformed versions for a post        | Optional: implement relation versions() |
| GET    | /api/posts/{id}/versions/{v}| Get a specific transformed version          | v = version id or slug |

Example publish payload (from orchestrator):

{
  "user_id": 3,
  "category_id": 2,
  "title": "Sample Article Title (Updated)",
  "content": `"<h1>…</h1>…\n\n## References\n- https://example.com/article-1\n- https://example.com/article-2"`,
  "status": true,
  "slug": "sample-article-title-updated",
  "image": null
}

Security: protect POST /api/posts with authentication (API token) so only orchestrator can publish.

---

## Orchestrator / Transformation Service (Runner, Scheduler, Queue, Logs)

Responsible for searching, scraping, LLM transformation, and orchestrating publish actions. Exposes control endpoints and job logs.

Base: http://localhost:4000 (example)

| Method | Endpoint                          | Description / Behavior                                           | Notes / Example |
|--------|-----------------------------------|------------------------------------------------------------------|-----------------|
| POST   | /api/transform/run                | Enqueue & run a transformation job                              | Body: { async: true/false, post_id?: number } — returns jobId. Rejects if another job is running. |
| POST   | /api/transform/schedule           | Create a recurring cron job to run transformations              | Body: { cron: "*/30 * * * *", post_id?: number } |
| GET    | /api/transform/jobs               | List recent transform jobs and status                           | Returns job metadata (id, status, createdAt, startedAt, finishedAt, error) |
| GET    | /api/transform/jobs/{id}          | Get job detail + logs                                            | Logs include timestamped lines from that run |
| POST   | /api/transform/cancel/{id}        | Cancel a scheduled job (if implemented)                         | Optional — requires scheduler support |
| GET    | /health                           | Health check for orchestrator service                           | Reports queue status, active job flag |

Important behavior

- Single-run policy: POST /api/transform/run will reject with 409 if another job is running or queued.
- Queue: in-memory queue by default; optional Redis/BullMQ when REDIS_URL is provided.
- Logs: per-job in-memory log store; persist to DB or files for production.
- Publish: orchestrator calls Laravel POST /api/posts to save transformed article. Provide API credentials for that call via env vars.

Example Run request:
POST /api/transform/run
Body: { "async": true }
Response (202):
{ "jobId": "d9f8-....", "status": "queued" }

Example Job detail response:
{
  "id": "d9f8-....",
  "status": "running",
  "createdAt": "2025-12-24T12:00:00Z",
  "startedAt": "2025-12-24T12:00:05Z",
  "logs": [
    { "ts":"...","level":"info","msg":"Orchestrator run started" },
    { "ts":"...","level":"info","msg":"Fetched latest article: Sample Article Title" },
    ...
  ]
}

Security & Integration

- Authentication: protect orchestrator endpoints using an API key, JWT, or network-level restrictions.
- Secrets: store Laravel API token, OpenAI/SerpAPI keys, and Redis URL in environment variables.
- Persistence: for multi-instance reliability, use Redis for queue and job store, and persisted logs (Elasticsearch, S3, or DB).

Service Responsibility Matrix (at-a-glance)

| Concern                      | Laravel API (posts) | Orchestrator Service |
|-----------------------------:|:-------------------:|:--------------------:|
| Store original posts         | ✅                  |                      |
| Store transformed versions   | ✅ (via POST)       |                      |
| Trigger transformations      |                     | ✅                   |
| Search & scrape competitors  |                     | ✅                   |
| LLM transformation           |                     | ✅                   |
| Queueing & scheduling        |                     | ✅ (cron/queue)      |
| Job logs & status            |                     | ✅ (per-job logs)    |
| Publish transformed article  | ✅ (receives POST)  | ✅ (calls POST)      |

Deployment Notes

- Run Laravel app (API) and Orchestrator separately. Provide orchestrator with API_BASE_URL pointing to Laravel (e.g., `http://localhost:8000/api`).
- Use HTTPS and secure network between services in production.
- Use persistent queue (Redis/BullMQ) and persistent logs for scale.

Demo Admin (Laravel)

- Admin URL: `http://127.0.0.1:8000/admin`
- Email: `admin@example.com`
- Password: password

---
