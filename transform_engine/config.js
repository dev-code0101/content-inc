module.exports = {
	API_BASE_URL: process.env.API_BASE_URL || 'http://localhost:8000/api',
	GOOGLE_RESULTS_LIMIT: 2,
	// Search provider env keys:
	SERPAPI_API_KEY: process.env.SERPAPI_API_KEY || null,
	GOOGLE_API_KEY: process.env.GOOGLE_API_KEY || null,
	GOOGLE_CX: process.env.GOOGLE_CX || null,
	// OpenAI-compatible key
	OPENAI_API_KEY: process.env.OPENAI_API_KEY || null,
	// Timeout / misc
	REQUEST_TIMEOUT_MS: 15000
  };
  