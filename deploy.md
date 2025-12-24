# Deployment Guide  

This guide covers **local development** and **container‑based production** for the four sub‑projects that make up the system:

| Service | Language / Framework | Primary entry point |
|---------|----------------------|---------------------|
| **Laravel API** | PHP 8 + Laravel | `php artisan serve` (or via Nginx + PHP‑FPM) |
| **Ingest Engine** | Python 3 (FastAPI + Celery) | `uvicorn app.main:app` |
| **Transform Engine** | Node 20 (Express) | `node src/index.js` |
| **Presentation** | React + Vite (static) | Served by Nginx |

All services can be run **individually** (useful for debugging) or **together** with the provided `docker‑compose.yml` (recommended for production‑like environments).

---  

## 1️⃣ Laravel API  

### 1.1 Prerequisites (bare‑metal)  

| Tool | Minimum version |
|------|-----------------|
| PHP | 8.2 |
| Composer | 2.x |
| Node / npm | 18+ |
| MySQL / MariaDB | 5.7+ (or any DB supported by Laravel) |

### 1.2 Local installation  

```bash
# 1. Clone the repo (or cd into the existing monorepo)
cd blog-cms-api/blog-cms-main

# 2. Install PHP dependencies
composer install --no-interaction --prefer-dist

# 3. Install front‑end assets
npm install
npm run build   # optional – only needed if you serve compiled assets via Nginx

# 4. Environment
cp .env.example .env
php artisan key:generate

# 5. Edit .env
#   - DB_CONNECTION=mysql
#   - DB_HOST=127.0.0.1
#   - DB_PORT=3306
#   - DB_DATABASE=blog_cms
#   - DB_USERNAME=blog_user
#   - DB_PASSWORD=blog_pass
#   - APP_URL=http://localhost:8000

# 6. Database
php artisan migrate
php artisan db:seed   # optional – creates sample posts with Faker

# 7. Run (development)
php artisan serve --port=8000
```

The API is now reachable at **`http://localhost:8000/api`**.

### 1.3 Docker deployment  

The Dockerfile lives in `blog-cms-api/blog-cms-main/Dockerfile`.  
When the whole stack is started with `docker compose up`, the Laravel service is built automatically and exposed through the **nginx** container on port **8000**.

If you want to run Laravel alone:

```bash
docker build -t blog-cms-api ./blog-cms-api/blog-cms-main
docker run -d \
  --name laravel_app \
  -p 8000:80 \
  -e APP_KEY=$(php -r "echo bin2hex(random_bytes(32));") \
  -e DB_CONNECTION=mysql \
  -e DB_HOST=host.docker.internal \
  -e DB_PORT=3306 \
  -e DB_DATABASE=blog_cms \
  -e DB_USERNAME=blog_user \
  -e DB_PASSWORD=blog_pass \
  blog-cms-api
```

> **Note** – the container expects a MySQL instance reachable at the host address (`host.docker.internal` on macOS/Windows, `172.17.0.1` on Linux).

---  

## 2️⃣ Ingest Engine (Python – FastAPI + Celery)  

### 2.1 Prerequisites  

| Tool | Minimum version |
|------|-----------------|
| Python | 3.12 |
| Poetry *(optional)* | 1.8+ |
| Redis | 7.x (used as Celery broker & lock store) |

### 2.2 Local installation  

```bash
cd ingest_engine

# If you use Poetry
poetry install          # installs dependencies from pyproject.toml
poetry shell           # activate virtualenv

# Or with pip
pip install -r requirements.txt
```

Create a `.env` file (example):

```dotenv
# FastAPI
LOG_LEVEL=info
API_PORT=8000

# Celery
REDIS_URL=redis://localhost:6379/0
```

Run the API **and** a worker in separate terminals:

```bash
# Terminal 1 – FastAPI
uvicorn app.main:app --host 0.0.0.0 --port 8000

# Terminal 2 – Celery worker
celery -A app.tasks.celery_app worker --loglevel=info
```

The scrape endpoint is **`POST http://localhost:8000/scrape`**. It returns a Celery `task_id`; the worker performs the scrape, logs to `logs/app.log`, and posts each article to the Laravel API.

### 2.3 Docker deployment  

Dockerfile is at `ingest_engine/Dockerfile`.  
When the full `docker compose up` runs, two containers are created:

* `ingest` – the FastAPI service (exposed on host **8001**).  
* `celery_worker` – the Celery worker that consumes the same image.

If you need to start only the ingest service:

```bash
docker compose up -d ingest
```

Or manually:

```bash
docker build -t ingest_engine ./ingest_engine
docker run -d \
  --name ingest_engine \
  -p 8001:8000 \
  -e REDIS_URL=redis://host.docker.internal:6379/0 \
  ingest_engine
```

---  

## 3️⃣ Transform Engine (Node Express)  

### 3.1 Prerequisites  

| Tool | Minimum version |
|------|-----------------|
| Node | 20.x |
| npm | 9.x |
| (Optional) Redis – for BullMQ queueing |

### 3.2 Local installation  

```bash
cd transform_engine
npm ci          # installs exact versions from package-lock.json
```

Create a `.env` (example):

```dotenv
PORT=3000
API_BASE_URL=http://localhost:8000/api
REDIS_URL=redis://localhost:6379/0   # omit if using in‑memory queue
ORCHESTRATOR_API_KEY=your-secret-key   # optional auth header
```

Run:

```bash
npm run start   # or: node src/index.js
```

The service listens on **`http://localhost:3000`** and exposes the `/api/transform/*` endpoints described in the earlier docs.

### 3.3 Docker deployment  

Dockerfile lives in `transform_engine/Dockerfile`.  
In the compose file it appears as the **`transform`** service, reachable on host **8002**.

Start it alone:

```bash
docker compose up -d transform
```

Or manually:

```bash
docker build -t transform_engine ./transform_engine
docker run -d \
  --name transform_engine \
  -p 8002:3000 \
  -e REDIS_URL=redis://host.docker.internal:6379/0 \
  transform_engine
```

---  

## 4️⃣ Presentation (React + Vite)  

### 4.1 Prerequisites  

| Tool | Minimum version |
|------|-----------------|
| Node | 18+ |
| npm | 9+ |

### 4.2 Local installation  

```bash
cd _presentation
npm ci
npm run build          # creates ./dist (static assets)
```

Create a `.env` (Vite expects the `VITE_` prefix):

```dotenv
VITE_API_BASE_URL=http://localhost:8000/api
```

Serve the built files with any static server (e.g., `npx serve -s dist`) or let the Docker Nginx container do it.

### 4.3 Docker deployment  

Dockerfile in `_presentation/Dockerfile` builds the assets and copies them into an Nginx image.  
In the compose file the service is called **`presentation`** and is exposed on host **8080**.

Start it alone:

```bash
docker compose up -d presentation
```

Or manually:

```bash
docker build -t presentation_static ./_presentation
docker run -d \
  --name presentation_static \
  -p 8080:80 \
  presentation_static
```

---  

## 5️⃣ Full‑stack deployment with Docker Compose  

### 5.1 Prerequisite  

* Docker Engine ≥ 24  
* Docker Compose (v2 plugin)  

### 5.2 Steps  

```bash
# 1️⃣ Clone the repository (if not already)
git clone <repo‑url>
cd <repo‑root>

# 2️⃣ Create .env files for each service (optional – defaults are fine for local dev)
#    - blog-cms-api/.env
#    - ingest_engine/.env
#    - transform_engine/.env
#    - _presentation/.env   (VITE_API_BASE_URL)

# 3️⃣ Build all images
docker compose build

# 4️⃣ Bring the stack up
docker compose up -d
```

The stack will start the following containers:

| Container          | Host port | Purpose |
|--------------------|----------|---------|
| `nginx`            | **8000** | Terminates HTTPS (if you add certs) and forwards HTTP requests to the Laravel PHP‑FPM container (`laravel`). |
| `laravel`          | internal 9000 | Runs the Laravel API (`/api/*`). |
| `db` (MySQL)       | **3306** | Database for Laravel. |
| `redis`            | **6379** | Broker & lock store for Celery and optional BullMQ queues. |
| `ingest`           | **8001** | FastAPI entry point (`POST /scrape`). |
| `celery_worker`    | internal | Executes the scraper, posts results to Laravel. |
| `transform`        | **8002** | Node‑Express transformation service (`/api/transform/*`). |
| `presentation`     | **8080** | Static React UI served by Nginx. |

### 5.3 Verifying the deployment  

```bash
# Laravel API health
curl http://localhost:8000/api/health   # should return JSON with status OK

# Trigger a scrape (will be queued)
curl -X POST http://localhost:8001/scrape -H "Content-Type: application/json"

# Check Celery worker logs
docker compose logs -f celery_worker

# Run a transformation job
curl -X POST http://localhost:8002/api/transform/run \
     -H "Content-Type: application/json" \
     -d '{"async":true}'

# View the React UI
open http://localhost:8080   # or navigate in a browser
```

### 5.4 Stopping / cleaning up  

```bash
docker compose down          # stops containers
docker compose down -v       # also removes named volumes (db, redis, logs)
docker compose rm -f         # force‑remove any leftover containers
```

### 5.5 Production considerations  

| Aspect | Recommendation |
|--------|----------------|
| **HTTPS** | Terminate TLS at the front‑end Nginx (`nginx` service). Mount your cert/key files via a bind‑mount and update the Nginx config (`/etc/nginx/conf.d/default.conf`). |
| **Scaling workers** | Increase `replicas` for `celery_worker` or `transform` in the compose file (or switch to Kubernetes). The Redis lock guarantees only one scraper runs at a time. |
| **Persistent logs** | The named volumes `laravel_logs`, `ingest_logs`, `transform_logs` keep log files across restarts. Rotate them with `logrotate` inside the containers or mount a host directory and run logrotate on the host. |
| **Secrets** | Do **not** commit `.env` files. In production inject environment variables via your orchestrator (Docker Swarm, Kubernetes Secrets, or a CI/CD pipeline). |
| **Database backups** | Schedule `mysqldump` backups of the `db_data` volume. |
| **Monitoring** | Export Prometheus metrics from each service (Laravel → `spatie/laravel-prometheus`, FastAPI → `prometheus_fastapi_instrumentator`, Celery → built‑in stats, Node → `express-prometheus-middleware`). |
| **Health checks** | Docker Compose already defines `depends_on`; you can add `healthcheck` sections for each service to let orchestrators restart unhealthy containers automatically. |

---  

## 6️⃣ Quick‑start cheat sheet  

```bash
# ---- ONE‑TIME SETUP -------------------------------------------------
git clone <repo>
cd <repo>

# create minimal .env files (example values)
cat > blog-cms-api/.env <<EOF
APP_KEY=$(php -r "echo bin2hex(random_bytes(32));")
APP_URL=http://localhost:8000
DB_CONNECTION=mysql
DB_HOST=db
DB_PORT=3306
DB_DATABASE=blog_cms
DB_USERNAME=blog_user
DB_PASSWORD=blog_pass
EOF

cat > ingest_engine/.env <<EOF
REDIS_URL=redis://redis:6379/0
EOF

cat > transform_engine/.env <<EOF
PORT=3000
API_BASE_URL=http://localhost:8000/api
REDIS_URL=redis://redis:6379/0
EOF

cat > _presentation/.env <<EOF
VITE_API_BASE_URL=http://localhost:8000/api
EOF

# ---- BUILD & RUN ----------------------------------------------------
docker compose build
docker compose up -d

# ---- Verify ---------------------------------------------------------
curl http://localhost:8000/api/health
curl -X POST http://localhost:8001/scrape
curl -X POST http://localhost:8002/api/transform/run -d '{"async":true}'
open http://localhost:8080
```
