const orchestrator = require('./orchestrator');

(async () => {
  try {
    await orchestrator.run();
    console.log('Phase 2 pipeline executed successfully —', new Date().toISOString());
  } catch (err) {
    console.error('Pipeline failed:', err);
    process.exit(1);
  }
})();
