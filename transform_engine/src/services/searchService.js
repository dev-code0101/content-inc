const fetch = require('node-fetch');
const { SERPAPI_API_KEY, GOOGLE_API_KEY, GOOGLE_CX, GOOGLE_RESULTS_LIMIT } = require('../../config');

class SearchService {
  async findCompetitorArticles(title) {
    console.log(`Searching for: "${title}"`);
    if (SERPAPI_API_KEY) {
      return this._searchSerpApi(title);
    } else if (GOOGLE_API_KEY && GOOGLE_CX) {
      return this._searchGoogleCustomSearch(title);
    } else {
      return this._searchViaHtmlScrape(title);
    }
  }

  async _searchSerpApi(title) {
    const params = new URLSearchParams({
      q: title,
      engine: 'google',
      api_key: SERPAPI_API_KEY,
      num: GOOGLE_RESULTS_LIMIT
    });
    const url = `https://serpapi.com/search.json?${params.toString()}`;
    const resp = await fetch(url);
    const json = await resp.json();
    const results = (json.organic_results || []).slice(0, GOOGLE_RESULTS_LIMIT).map(r => ({
      url: r.link || r.url,
      title: r.title,
      source: r.source || r.domain
    }));
    return results;
  }

  async _searchGoogleCustomSearch(title) {
    const params = new URLSearchParams({
      key: GOOGLE_API_KEY,
      cx: GOOGLE_CX,
      q: title,
      num: GOOGLE_RESULTS_LIMIT
    });
    const url = `https://www.googleapis.com/customsearch/v1?${params.toString()}`;
    const resp = await fetch(url);
    const json = await resp.json();
    const items = json.items || [];
    return items.slice(0, GOOGLE_RESULTS_LIMIT).map(i => ({
      url: i.link,
      title: i.title,
      source: i.displayLink
    }));
  }

  async _searchViaHtmlScrape(title) {
    // Simple approach: use Google "I'm feeling lucky" fallback via search query HTML scraping (fragile)
    const query = encodeURIComponent(title);
    const url = `https://www.google.com/search?q=${query}&num=${GOOGLE_RESULTS_LIMIT}`;
    const res = await fetch(url, { headers: { 'User-Agent': 'Mozilla/5.0' } });
    const text = await res.text();
    // naive extraction of links
    const linkRegex = /<a href="\/url\?q=([^&"]+)[^"]*"[^>]*>/g;
    const results = [];
    let m;
    while (results.length < GOOGLE_RESULTS_LIMIT && (m = linkRegex.exec(text)) !== null) {
      try {
        const decoded = decodeURIComponent(m[1]);
        if (!decoded.startsWith('http')) continue;
        results.push({ url: decoded, title: '', source: new URL(decoded).hostname });
      } catch (e) { /* ignore */ }
    }
    return results;
  }
}

module.exports = new SearchService();
