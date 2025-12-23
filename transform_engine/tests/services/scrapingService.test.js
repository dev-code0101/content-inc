const scraping = require('../../src/services/scrapingService');
const fetch = require('node-fetch');

jest.mock('node-fetch', () => jest.fn());
const { Response } = jest.requireActual('node-fetch');

describe('ScrapingService', () => {
  afterEach(() => jest.resetAllMocks());

  test('scrapeArticleContent uses Readability when available', async () => {
    const html = '<html><head><title>Title</title></head><body><article><p>Hello world</p></article></body></html>';
    fetch.mockResolvedValue(new Response(html));
    const res = await scraping.scrapeArticleContent('https://x');
    expect(res.title).toBe('Title');
    expect(res.content).toContain('Hello world');
  });

  test('scrapeArticleContent falls back to body text', async () => {
    const html = '<html><body><p>short</p></body></html>';
    fetch.mockResolvedValue(new Response(html));
    const res = await scraping.scrapeArticleContent('https://x');
    expect(res.content.length).toBeGreaterThan(0);
  });
});
