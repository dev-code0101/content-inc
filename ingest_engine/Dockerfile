# -------------------------------------------------
# 2️⃣ Python 3.12 (Alpine) + Poetry (optional)
# -------------------------------------------------
FROM python:3.12-slim AS builder

# Install build deps (needed for some wheels)
RUN apt-get update && apt-get install -y --no-install-recommends \
		build-essential gcc libpq-dev curl && \
	rm -rf /var/lib/apt/lists/*

# Create a non‑root user
RUN useradd -m appuser
WORKDIR /app

# Install Poetry (optional, you can use pip)
ENV POETRY_VERSION=1.8.2
RUN pip install "poetry==$POETRY_VERSION"

# Copy only pyproject/requirements for caching
COPY ingest_engine/pyproject.toml ingest_engine/poetry.lock* ./
RUN poetry config virtualenvs.create false && \
	poetry install --no-interaction --no-ansi --only main

# -------------------------------------------------
# 3️⃣ Runtime image
# -------------------------------------------------
FROM python:3.12-slim AS runtime
COPY --from=builder /usr/local/lib/python3.12/site-packages /usr/local/lib/python3.12/site-packages
COPY --from=builder /usr/local/bin/poetry /usr/local/bin/poetry

# Create non‑root user
RUN useradd -m appuser
USER appuser
WORKDIR /app

# Copy application code
COPY ingest_engine/app ./app
COPY ingest_engine/*.py .   # any entry‑point scripts

# Environment defaults
ENV PYTHONUNBUFFERED=1
ENV LOG_LEVEL=info

EXPOSE 8000
CMD ["uvicorn", "app.main:app", "--host", "0.0.0.0", "--port", "8000"]
