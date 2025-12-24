const orchestrator = require('./orchestrator');

async function run(context = {}) {
  // context: { jobId, logger }
  const logger = context.logger || console;
  logger.info?.('Orchestrator run started', { jobId: context.jobId });
  try {
    await orchestrator.run({ jobId: context.jobId, logger });
    logger.info?.('Orchestrator run finished', { jobId: context.jobId });
    return { success: true };
  } catch (err) {
    logger.error?.('Orchestrator run failed', err);
    throw err;
  }
}

if (require.main === module) {
  (async () => {
    try {
      await run();
      console.log('Executed directly');
    } catch (err) {
      console.error(err);
      process.exit(1);
    }
  })();
}

module.exports = { run };
