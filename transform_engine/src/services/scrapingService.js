const fetch = require('node-fetch');
const cheerio = require('cheerio');
const { JSDOM } = require('jsdom');

class ScrapingService {
  async scrapeArticleContent(url) {
    console.log(`Scraping content from: ${url}`);
    try {
      const res = await fetch(url, { headers: { 'User-Agent': 'Mozilla/5.0' } });
      const html = await res.text();
      // Attempt to use Readability via JSDOM if possible
      try {
        const dom = new JSDOM(html, { url });
        // eslint-disable-next-line global-require
        const { Readability } = require('@mozilla/readability');
        const doc = new Readability(dom.window.document).parse();
        if (doc && doc.textContent) {
          return { url, title: doc.title || '', content: doc.textContent.trim() };
        }
      } catch (e) {
        // fallback to cheerio
      }
      const $ = cheerio.load(html);
      // Try common article selectors
      const articleSelectors = [
        'article',
        'main',
        '.article-content',
        '.post-content',
        '#content',
        '.entry-content'
      ];
      let extracted = '';
      for (const sel of articleSelectors) {
        const el = $(sel);
        if (el && el.text().trim().length > 200) {
          extracted = el.text();
          break;
        }
      }
      if (!extracted) {
        // fallback: largest <p> block group
        const paragraphs = $('p').toArray();
        let best = '';
        let cur = '';
        for (let i = 0; i < paragraphs.length; i++) {
          const text = $(paragraphs[i]).text();
          if (text.length > 50) {
            cur += '\n\n' + text;
          } else {
            if (cur.length > best.length) best = cur;
            cur = '';
          }
        }
        if (cur.length > best.length) best = cur;
        extracted = best || $('body').text().slice(0, 5000);
      }
      return { url, title: $('title').first().text() || '', content: extracted.trim() };
    } catch (err) {
      console.warn('Scrape failed for', url, err.message);
      return { url, title: '', content: '' };
    }
  }
}

module.exports = new ScrapingService();
