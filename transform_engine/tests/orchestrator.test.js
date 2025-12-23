jest.mock('../../src/clients/articleApiClient');
jest.mock('../../src/services/searchService');
jest.mock('../../src/services/scrapingService');
jest.mock('../../src/services/llmService');

const articleApi = require('../../src/clients/articleApiClient');
const searchService = require('../../src/services/searchService');
const scraping = require('../../src/services/scrapingService');
const llm = require('../../src/services/llmService');
const orchestrator = require('../../src/orchestrator');

describe('Orchestrator', () => {
  afterEach(() => jest.resetAllMocks());

  test('runs end-to-end using mocks', async () => {
    articleApi.fetchLatestArticle.mockResolvedValue({ id:1,user_id:1,category_id:1,title:'T',status:true,slug:'t',content:'orig' });
    searchService.findCompetitorArticles.mockResolvedValue([{ url: 'https://a' }, { url: 'https://b' }]);
    scraping.scrapeArticleContent.mockResolvedValueOnce({ url:'https://a', content:'c1' }).mockResolvedValueOnce({ url:'https://b', content:'c2' });
    llm.transformArticle.mockResolvedValue('updated content');
    articleApi.publishUpdatedArticle.mockResolvedValue({ success: true });

    await orchestrator.run();

    expect(articleApi.publishUpdatedArticle).toHaveBeenCalled();
  });
});
