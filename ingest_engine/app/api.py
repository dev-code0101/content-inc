from fastapi import FastAPI, HTTPException, BackgroundTasks
from redis import Redis
from .tasks import run_scrape_and_post
from .logger import get_logger

log = get_logger("api")
app = FastAPI(title="Blog Scraper Microservice")

# Simple Redis lock helper
redis_client = Redis(host="redis", port=6379, db=1, decode_responses=True)
LOCK_KEY = "scrape_job_lock"
LOCK_TTL = 60 * 30  # 30 minutes – longer than any expected run


def acquire_lock() -> bool:
    return redis_client.set(LOCK_KEY, "locked", nx=True, ex=LOCK_TTL)


def release_lock():
    redis_client.delete(LOCK_KEY)


@app.post("/scrape")
def start_scrape(background_tasks: BackgroundTasks):
    if not acquire_lock():
        log.warning("Scrape request rejected – job already running")
        raise HTTPException(
            status_code=409, detail="A scrape job is already in progress."
        )

    # Enqueue Celery task; FastAPI returns immediately
    task = run_scrape_and_post.delay()

    # Ensure lock is released when the Celery task finishes
    def cleanup():
        release_lock()
        log.info("Lock released after job completion")

    background_tasks.add_task(cleanup)

    log.info(f"Scrape job queued – task_id={task.id}")
    return {"task_id": task.id, "status": "queued"}
