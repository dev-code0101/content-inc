import requests
from bs4 import BeautifulSoup
import random
import argparse
import importlib


def scrape_blog_articles():
    all_articles = []

    # Scrape blog list pages from 1 to 15
    for page_num in range(15, 16):
        list_url = f'https://beyondchats.com/blogs/page/{page_num}/'

        try:
            # Get the blog list page
            list_response = requests.get(list_url)
            list_soup = BeautifulSoup(list_response.text, 'html.parser')

            # Find all article cards
            articles = list_soup.find_all('article', class_='entry-card')

            # Process each article card
            for article in articles:
                # Extract title
                title_elem = article.find('h2', class_='entry-title')
                title = title_elem.text.strip() if title_elem else 'Untitled'

                # Extract link
                link_elem = title_elem.find('a')
                blog_url = (
                    link_elem['href']
                    if link_elem and 'href' in link_elem.attrs
                    else None
                )

                # Extract tags
                tags_elem = article.find('li', class_='meta-categories')
                tags = (
                    [tag.text.strip() for tag in tags_elem.find_all('a')]
                    if tags_elem
                    else []
                )

                # If no link, skip this article
                if not blog_url:
                    continue

                # Fetch individual blog post content
                try:
                    blog_response = requests.get(blog_url)
                    blog_soup = BeautifulSoup(blog_response.text, 'html.parser')

                    # Find content container
                    content_container = blog_soup.find(
                        'div', class_='elementor-element-b2a436b'
                    )

                    # Extract text from p and h3 tags
                    if content_container:
                        content_elements = content_container.find_all(
                            ['p', 'h3']
                        )
                        content = '\n'.join(
                            [
                                elem.get_text(strip=True)
                                for elem in content_elements
                            ]
                        )
                    else:
                        content = ''

                    # Prepare article data for API
                    article_data = {
                        'user_id': random.randint(1, 10),
                        'category_id': random.randint(1, 5),
                        'title': title,
                        'content': content,
                        'status': random.choice([True, False]),
                        'slug': title.lower().replace(' ', '-'),
                        'tags': ', '.join(tags),
                        'image': None,  # You can add image URL if needed
                    }

                    all_articles.append(article_data)

                except requests.RequestException as blog_error:
                    print(
                        f"Error fetching blog content for {title}: {blog_error}"
                    )

        except requests.RequestException as list_error:
            print(f"Error scraping page {page_num}: {list_error}")

    return all_articles


def send_articles_to_api(articles, api_url='http://127.0.0.1:8000/api/posts'):
    successful_posts = []
    failed_posts = []

    for article in articles:
        try:
            # Send POST request to the API
            response = requests.post(api_url, json=article)

            # Check if the request was successful
            if response.status_code in [200, 201]:
                successful_posts.append(article['title'])
                print(f"Successfully posted: {article['title']}")
            else:
                failed_posts.append(article['title'])
                print(
                    f"Failed to post: {article['title']} - Status code: {response.status_code}"
                )

        except requests.RequestException as e:
            failed_posts.append(article['title'])
            print(f"Error posting article {article['title']}: {e}")

    print(f"\nSuccessful posts: {len(successful_posts)}")
    print(f"Failed posts: {len(failed_posts)}")


if __name__ == '__main__':
    articles = scrape_blog_articles()
    send_articles_to_api(articles)

    parser = argparse.ArgumentParser(description='Blog Scraper')
    parser.add_argument(
        '--test', action='store_true', help='Run tests from tests.py'
    )
    args = parser.parse_args()

    if args.test:
        # Import tests from tests.py and run them
        tests = importlib.import_module('tests')
        tests.run_tests()
