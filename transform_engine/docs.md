# Phase 2 — Content Transformation Engine

## Overview

Node.js service that:
- Fetches latest article from Laravel API
- Searches Google for the article title (top 2 results)
- Scrapes main content from those results
- Uses an LLM to rewrite the original article to match competitor style/format
- Publishes updated article via API
- Appends a References section with scraped URLs

## Structure

phase-2-engine/

- src/

  - clients/articleApiClient.js
  - services/searchService.js
  - services/scrapingService.js
  - services/llmService.js
  - orchestrator.js
  - index.js
- config.js
- package.json

## Requirements

- Node 18+
- Environment variables:
  - API_BASE_URL (optional, default http://localhost:8000/api)
  - OPENAI_API_KEY (recommended)
  - SERPAPI_API_KEY or (GOOGLE_API_KEY and GOOGLE_CX) for search (optional)
- Install: npm install

## Config

Edit config.js or set env vars:
- API_BASE_URL
- GOOGLE_RESULTS_LIMIT (default 2)
- SERPAPI_API_KEY / GOOGLE_API_KEY / GOOGLE_CX
- OPENAI_API_KEY

## Endpoints used (assumptions)

- GET /articles?sort=latest&limit=1 — fetch latest article
- POST /articles — create/publish updated article
Adjust articleApiClient if your API differs.

## Running

1. Install dependencies: npm install
2. Set environment variables
3. Start: npm start

## Behavior

1. Fetch latest article from Laravel API.
2. Search Google (SerpAPI -> Custom Search -> HTML scrape fallback) for article title; return top 2 links.
3. Scrape article main content using Readability + cheerio fallback.
4. Call LLM (OpenAI-compatible) to rewrite original content to match competitors.
5. Append "References" with competitor URLs.
6. Publish via POST /articles.

## Notes & Caveats

- Scraping can be fragile; respect robots.txt and site ToS.
- Provide API keys for reliable search and LLM calls.
- LLM prompt and model are configurable in llmService.
- Error handling includes sensible fallbacks; adapt retry/rate-limit for production.

## Extensibility

- Swap LLM provider in src/services/llmService.js
- Add rate limiting, retries, and logging middleware
- Add unit/integration tests around clients and services

## Contact

For changes to API routes or behavior, update src/clients/articleApiClient.js and config.js.