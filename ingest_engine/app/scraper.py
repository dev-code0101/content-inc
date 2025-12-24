import random
import requests
from bs4 import BeautifulSoup
from .logger import get_logger

log = get_logger(__name__)


def _fetch_page(url: str) -> BeautifulSoup | None:
    try:
        resp = requests.get(url, timeout=10)
        resp.raise_for_status()
        return BeautifulSoup(resp.text, "html.parser")
    except requests.RequestException as exc:
        log.error(f"Request failed for {url}: {exc}")
        return None


def scrape_blog_articles(start_page: int = 1, end_page: int = 15) -> list[dict]:
    all_articles = []

    for page_num in range(start_page, end_page + 1):
        list_url = f"https://beyondchats.com/blogs/page/{page_num}/"
        log.info(f"Scraping list page {list_url}")

        list_soup = _fetch_page(list_url)
        if not list_soup:
            continue

        for article in list_soup.find_all("article", class_="entry-card"):
            title_elem = article.find("h2", class_="entry-title")
            title = title_elem.text.strip() if title_elem else "Untitled"

            link_elem = title_elem.find("a") if title_elem else None
            blog_url = (
                link_elem["href"]
                if link_elem and "href" in link_elem.attrs
                else None
            )
            if not blog_url:
                continue

            tags_elem = article.find("li", class_="meta-categories")
            tags = (
                [t.text.strip() for t in tags_elem.find_all("a")]
                if tags_elem
                else []
            )

            # ---- fetch individual post ----
            blog_soup = _fetch_page(blog_url)
            if not blog_soup:
                continue

            content_container = blog_soup.find(
                "div", class_="elementor-element-b2a436b"
            )
            if content_container:
                parts = [
                    el.get_text(strip=True)
                    for el in content_container.find_all(["p", "h3"])
                ]
                content = "\n".join(parts)
            else:
                content = ""

            article_data = {
                "user_id": random.randint(1, 10),
                "category_id": random.randint(1, 5),
                "title": title,
                "content": content,
                "status": random.choice([True, False]),
                "slug": title.lower().replace(" ", "-"),
                "tags": ", ".join(tags),
                "image": None,
            }
            all_articles.append(article_data)

    log.info(f"Scraping finished – collected {len(all_articles)} articles")
    return all_articles
