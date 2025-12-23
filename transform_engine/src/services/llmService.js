const { OPENAI_API_KEY } = require('../../config');
const OpenAI = require('openai');

class LLMService {
  constructor() {
    if (OPENAI_API_KEY) {
      this.client = new OpenAI({ apiKey: OPENAI_API_KEY });
    } else {
      this.client = null;
    }
  }

  async transformArticle(originalContent, competitors) {
    console.log('Transforming article using LLM...');
    // Build prompt: include original and competitor excerpts and structured instructions.
    const competitorSummaries = competitors.map((c, i) => `Reference ${i+1}: URL: ${c.url}\nContent:\n${this._truncate(c.content, 2500)}\n`).join('\n\n');

    const systemPrompt = `You are an expert content writer and editor. Take the ORIGINAL article and rewrite it to improve formatting, clarity, and structure so its style, headings, and flow are similar to the TOP COMPETITOR ARTICLES. Preserve factual meaning from ORIGINAL. At the bottom, include an inline "References" section listing the competitor URLs. Write in clear, neutral tone and use sections with headings.`;

    const userPrompt = `ORIGINAL ARTICLE:\n${this._truncate(originalContent, 2000)}\n\nCOMPETITOR ARTICLES:\n${competitorSummaries}\n\nINSTRUCTIONS:\n- Keep original facts but improve structure and readability.\n- Use headings, short paragraphs, bullets where appropriate.\n- Match tone and depth of competitor articles.\n- Add "References" section at the bottom listing competitor URLs.\nReturn the full article content only.`;

    if (this.client) {
      try {
        const response = await this.client.chat.completions.create({
          model: 'gpt-4o-mini', // replace with actual available model as desired
          messages: [
            { role: 'system', content: systemPrompt },
            { role: 'user', content: userPrompt }
          ],
          max_tokens: 2000,
          temperature: 0.7
        });
        // Depending on OpenAI SDK version: adjust to response structure
        const content = response.choices?.[0]?.message?.content ?? response.choices?.[0]?.text ?? '';
        return content.trim();
      } catch (err) {
        console.warn('LLM call failed, falling back to simple merge. Error:', err.message);
      }
    }
    // Fallback: simple merge + citations
    let merged = `# ${this._shortHeadingFrom(originalContent)}\n\n${originalContent}\n\nEnhanced using insights from:\n`;
    for (const c of competitors) merged += `- ${c.url}\n`;
    merged += `\nReferences:\n` + competitors.map(c => c.url).join('\n');
    return merged;
  }

  _truncate(s, n) {
    if (!s) return '';
    return s.length > n ? s.slice(0, n - 3) + '...' : s;
  }

  _shortHeadingFrom(text) {
    if (!text) return 'Updated Article';
    const firstLine = text.split('\n').find(l => l.trim().length > 10) || text.slice(0, 50);
    return firstLine.trim().slice(0, 60);
  }
}

module.exports = new LLMService();
