const llm = require('../../src/services/llmService');

describe('LLMService', () => {
  test('truncate and fallback behavior', async () => {
    const original = 'a'.repeat(3000);
    const competitors = [{ url: 'u1', content: 'c1'.repeat(1000) }, { url: 'u2', content: 'c2'.repeat(1000) }];
    const out = await llm.transformArticle(original, competitors);
    expect(out).toMatch(/References|Enhanced/);
  });
});
