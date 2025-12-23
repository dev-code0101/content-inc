const articleApi = require('../../src/clients/articleApiClient');
const axios = require('axios');

jest.mock('axios');

describe('ArticleApiClient', () => {
  afterEach(() => jest.resetAllMocks());

  test('fetchLatestArticle - uses API and returns mapped article', async () => {
    axios.create.mockReturnValue(axios);
    axios.get.mockResolvedValue({ data: [{ id: 7, user_id: 2, category_id: 3, title: 'T', status: true, slug: 't', content: 'c' }] });
    const a = await articleApi.fetchLatestArticle();
    expect(a.title).toBe('T');
    expect(a.content).toBe('c');
  });

  test('fetchLatestArticle - fallback on error', async () => {
    axios.create.mockReturnValue(axios);
    axios.get.mockRejectedValue(new Error('down'));
    const a = await articleApi.fetchLatestArticle();
    expect(a.title).toBeDefined();
    expect(a.content).toContain('Original article content');
  });

  test('publishUpdatedArticle - posts payload', async () => {
    axios.create.mockReturnValue(axios);
    axios.post.mockResolvedValue({ data: { success: true } });
    const res = await articleApi.publishUpdatedArticle({ title: 'x' });
    expect(res).toEqual({ success: true });
    expect(axios.post).toHaveBeenCalledWith('/articles', { title: 'x' });
  });
});
