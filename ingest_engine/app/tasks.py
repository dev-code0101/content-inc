from celery import Celery, states
from celery.exceptions import Ignore
from .scraper import scrape_blog_articles
from .logger import get_logger
import requests

log = get_logger("tasks")

celery_app = Celery(
    "blog_tasks",
    broker="redis://redis:6379/0",
    backend="redis://redis:6379/0",
)

API_URL = "http://127.0.0.1:8000/api/posts"  # adjust as needed


@celery_app.task(bind=True)
def run_scrape_and_post(self):
    try:
        articles = scrape_blog_articles(start_page=15, end_page=15)
        successful, failed = 0, 0

        for article in articles:
            try:
                resp = requests.post(API_URL, json=article, timeout=10)
                if resp.status_code in (200, 201):
                    successful += 1
                    log.info(f"Posted: {article['title']}")
                else:
                    failed += 1
                    log.warning(
                        f"Failed ({resp.status_code}) for {article['title']}"
                    )
            except requests.RequestException as exc:
                failed += 1
                log.error(f"Error posting {article['title']}: {exc}")

        log.info(f"Job finished – {successful} succeeded, {failed} failed")
        return {"success": successful, "failed": failed}
    except Exception as exc:
        log.exception("Unexpected error in scrape task")
        self.update_state(state=states.FAILURE, meta=str(exc))
        raise Ignore()
