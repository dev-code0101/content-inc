const axios = require('axios');
const { API_BASE_URL, REQUEST_TIMEOUT_MS } = require('../../config');

class ArticleApiClient {
  constructor() {
    this.client = axios.create({
      baseURL: API_BASE_URL,
      timeout: REQUEST_TIMEOUT_MS
    });
  }

  async fetchLatestArticle() {
    // GET /posts?sort=latest&limit=1  (adapt if your API differs)
    try {
      const res = await this.client.get('/posts', {
        params: { sort: 'latest', limit: 1 }
      });
      const data = res.data;
      // Handle if API returns list or single object
      const article = Array.isArray(data) ? data[0] : data;
      if (!article) throw new Error('No articles returned from API');
      return {
        id: article.id,
        user_id: article.user_id ?? article.userId ?? 1,
        category_id: article.category_id ?? article.categoryId ?? 1,
        title: article.title,
        status: article.status ?? true,
        slug: article.slug ?? slugify(article.title || 'article'),
        image: article.image ?? null,
        content: article.content ?? ''
      };
    } catch (err) {
      // Fallback sample if API unavailable - keeps pipeline runnable
      console.warn('fetchLatestArticle failed, using fallback sample. Error:', err.message);
      return {
        id: 1,
        user_id: 3,
        category_id: 2,
        title: 'Sample Article Title',
        status: true,
        slug: 'sample-article-title',
        image: null,
        content: 'Original article content...'
      };
    }
  }

  async publishUpdatedArticle(articlePayload) {
    try {
      const res = await this.client.post('/posts', articlePayload);
      return res.data;
    } catch (err) {
      console.error('publishUpdatedArticle failed, logging to console. Error:', err.message);
      console.log('Would publish payload:', articlePayload);
      return { success: false, error: err.message };
    }
  }
}

module.exports = new ArticleApiClient();

function slugify(input) {
  return (input || '')
    .toString()
    .toLowerCase()
    .replace(/\s+/g, '-')
    .replace(/[^\w\-]+/g, '')
    .replace(/\-\-+/g, '-')
    .replace(/^-+/, '')
    .replace(/-+$/, '');
}
