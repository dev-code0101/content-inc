# Deployment Guide

## Laravel API Installation

1. Clone the project  
2. Run `composer install` and `npm install`  
3. Copy `.env.example` to `.env`  
4. Run `php artisan key:generate`  
5. Update database credentials in `.env`  
6. Run migrations: `php artisan migrate`  
   - Optionally, run `php artisan db:seed` to populate sample data with Faker  
7. Start the server: `php artisan serve`  

---

## Orchestrator (Node) Server Installation & Deployment

Prereqs:

- Node.js 18+
- npm
- (Optional) Redis if using BullMQ for persistent queueing
- Environment variables configured (see below)

1. Clone the Phase-2 engine repo (or copy into your mono-repo)
2. Install dependencies:
   - npm install

3. Environment variables

   - Create a .env file or set in your environment:
     - API_BASE_URL=`http://localhost:8000/api`        # Laravel API base
     - PORT=4000                                     # Orchestrator HTTP port
     - OPENAI_API_KEY=sk_...                          # LLM key (optional)
     - SERPAPI_API_KEY=...                            # SerpAPI (optional)
     - GOOGLE_API_KEY=..., GOOGLE_CX=...              # Google CSE (optional)
     - REDIS_URL=redis://user:pass@host:6379/0        # Optional — enables BullMQ
     - QUEUE_CONCURRENCY=1                            # In-memory concurrency
     - LOG_MAX_LINES=1000                             # Optional log retention per job

4. Start in development:
   - npm run serve
   - (Or) node src/server.js

5. Production
   - Use a process manager (pm2, systemd, Docker) to run `node src/server.js` or `npm run serve`.
   - If using Redis/BullMQ: ensure REDIS_URL is reachable by the orchestrator service.
   - Configure reverse proxy (NGINX) to expose /api/transform/* endpoints. Use HTTPS and restrict access via firewall or API key.

6. Health & readiness
   - GET /health should return basic status (queue pending count, active job flag).
   - GET /api/transform/jobs lists recent jobs.

7. Logging & persistence
   - By default job logs and job metadata are in-memory (JOBS map and logger store). For production:
     - Enable Redis/BullMQ to persist queue and job state.
     - Persist logs to a centralized store (files, CloudWatch, Elasticsearch).
     - Rotate or trim logs (LOG_MAX_LINES).

8. Securing endpoints
   - Protect orchestrator endpoints with an API key, JWT, or network-only access.
   - Example: set ORCHESTRATOR_API_KEY and validate in middleware.

9. Scaling

   - Single-run policy: the orchestrator rejects new runs while a job is active. For scale:
     - Use a shared job store (Redis) and coordinate worker concurrency via BullMQ.
     - Persist JOBS state in Redis/DB and use distributed locks to enforce single-run policy.

10. CI / Deployment

- Add a CI job to run unit tests (npm test) and linting.
- Deploy orchestrator with environment variables injected via your CI/CD pipeline or secrets manager.

---

## Quick Start (Local)

1. Start Laravel:
   - cd laravel-app
   - composer install && npm install
   - cp .env.example .env && php artisan key:generate
   - update DB in .env, php artisan migrate --seed
   - php artisan serve --port=8000

2. Start Orchestrator:
   - cd phase-2-engine
   - npm install
   - create .env with API_BASE_URL=`http://localhost:8000/api`
   - npm run serve

3. Trigger a transformation:
   - curl -X POST http://localhost:4000/api/transform/run -H "Content-Type: application/json" -d '{"async":true}'

Notes:

- Ensure the orchestrator has permission to POST to Laravel /api/posts (provide API token in env and add Authorization header in articleApiClient).
- Monitor logs at GET /api/transform/jobs/{id}.
