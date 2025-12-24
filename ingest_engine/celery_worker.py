from app.tasks import celery_app  # noqa: F401  (imports the Celery app)

if __name__ == "__main__":
    celery_app.worker_main()
