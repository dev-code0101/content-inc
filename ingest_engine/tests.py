import unittest
from unittest.mock import patch, Mock
from run import scrape_blog_articles, send_articles_to_api


class TestBlogScraper(unittest.TestCase):

    @patch('run.requests.get')
    def test_scrape_blog_articles(self, mock_get):
        # Mock the response of the requests.get call
        mock_get.return_value = Mock(
            status_code=200,
            text='<html><body><article class="entry-card">'
            '<h2 class="entry-title"><a href="http://example.com">Test Article</a></h2>'
            '<li class="meta-categories"><a>test</a></li>'
            '</article></body></html>',
        )

        articles = scrape_blog_articles()

        # Check if the article was scraped correctly
        self.assertEqual(len(articles), 1)
        self.assertEqual(articles[0]['title'], 'Test Article')
        self.assertEqual(articles[0]['slug'], 'test-article')
        self.assertIn('test', articles[0]['tags'])

    @patch('run.requests.post')
    def test_send_articles_to_api(self, mock_post):
        # Mock a successful response from the POST request
        mock_post.return_value = Mock(status_code=201)
        articles = [{'title': 'Test Article', 'content': 'Test content'}]

        # Send articles to API
        successful_posts, failed_posts = send_articles_to_api(articles)

        # Check the results
        self.assertEqual(len(successful_posts), 1)
        self.assertEqual(successful_posts[0], 'Test Article')
        self.assertEqual(len(failed_posts), 0)

    @patch('run.requests.post')
    def test_send_articles_to_api_failure(self, mock_post):
        # Mock a failed response from the POST request
        mock_post.return_value = Mock(status_code=400)
        articles = [{'title': 'Failed Article', 'content': 'Fail content'}]

        successful_posts, failed_posts = send_articles_to_api(articles)

        # Check the results
        self.assertEqual(len(successful_posts), 0)
        self.assertEqual(len(failed_posts), 1)
        self.assertEqual(failed_posts[0], 'Failed Article')


def run_tests():
    unittest.main()


if __name__ == '__main__':
    run_tests()
