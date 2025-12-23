const searchService = require('../../src/services/searchService');
const fetch = require('node-fetch');

jest.mock('node-fetch', () => jest.fn());
const { Response } = jest.requireActual('node-fetch');

describe('SearchService', () => {
  afterEach(() => jest.resetAllMocks());

  test('chooses serpapi when key present', async () => {
    process.env.SERPAPI_API_KEY = 'key';
    fetch.mockResolvedValue(new Response(JSON.stringify({ organic_results: [{ link: 'https://a' }, { link: 'https://b' }] })));
    const res = await searchService.findCompetitorArticles('q');
    expect(res.length).toBeGreaterThan(0);
    delete process.env.SERPAPI_API_KEY;
  });

  test('html scrape fallback returns links', async () => {
    fetch.mockResolvedValue(new Response('<a href="/url?q=https%3A%2F%2Fexample.com">x</a>'));
    const res = await searchService.findCompetitorArticles('q');
    expect(res[0].url).toContain('https://example.com');
  });
});
