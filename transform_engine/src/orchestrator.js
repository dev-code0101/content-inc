const articleApi = require('./clients/articleApiClient');
const searchService = require('./services/searchService');
const scrapingService = require('./services/scrapingService');
const llmService = require('./services/llmService');
const slugify = require('slugify');

class ContentOrchestrator {
  async run() {
    // 1. Fetch latest article
    const article = await articleApi.fetchLatestArticle();
    if (!article || !article.title) throw new Error('No article found to process');

    // 2. Find competitor articles
    const competitors = await searchService.findCompetitorArticles(article.title);

    // 3. Scrape competitor content (top N)
    const scraped = await Promise.all(competitors.slice(0, 2).map(c => scrapingService.scrapeArticleContent(c.url)));

    // 4. Transform content using LLM
    const updatedContent = await llmService.transformArticle(article.content, scraped);

    // 5. Add explicit References block (ensuring scraped urls included)
    const refUrls = scraped.map(s => s.url).filter(Boolean);
    const referencesBlock = '\n\n## References\n' + refUrls.map(u => `- ${u}`).join('\n');
    const finalContent = (updatedContent || '') + referencesBlock;

    // 6. Publish updated article
    const updatedArticlePayload = {
      user_id: article.user_id,
      category_id: article.category_id,
      title: `${article.title} (Updated)`,
      status: article.status,
      slug: `${slugify(article.title || 'article')}-updated`,
      image: article.image,
      content: finalContent
    };

    await articleApi.publishUpdatedArticle(updatedArticlePayload);

    console.log('Pipeline completed. Published article:', updatedArticlePayload.title);
  }
}

module.exports = new ContentOrchestrator();
