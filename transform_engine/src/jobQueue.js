// src/jobQueue.js
const { createClient } = require('redis');
let useRedis = !!process.env.REDIS_URL;
let QueueImpl;

if (useRedis) {
  // use bullmq
  const { Queue, Worker, QueueScheduler } = require('bullmq');
  const connection = { connection: { url: process.env.REDIS_URL } };
  const queue = new Queue('orchestrator', { connection: connection.connection });
  const scheduler = new QueueScheduler('orchestrator', connection);
  const worker = new Worker('orchestrator', async job => {
    const { runFn, context } = job.data;
    // cannot serialize functions; worker will call orchestrator via require
    const orchestratorRunner = require('./index');
    return orchestratorRunner.run(context);
  }, connection);

  module.exports = {
    enqueue: async (context) => {
      return queue.add('run', { context });
    },
    close: async () => { await worker.close(); await queue.close(); await scheduler.close(); }
  };
} else {
  // lightweight in-memory queue
  const pQueue = [];
  let running = 0;
  const CONCURRENCY = parseInt(process.env.QUEUE_CONCURRENCY || '1', 10);

  async function workerLoop() {
    if (running >= CONCURRENCY) return;
    const job = pQueue.shift();
    if (!job) return;
    running++;
    const { context, resolve, reject } = job;
    try {
      const orchestratorRunner = require('./index');
      const res = await orchestratorRunner.run(context);
      resolve(res);
    } catch (err) {
      reject(err);
    } finally {
      running--;
      setImmediate(workerLoop);
    }
  }

  module.exports = {
    enqueue: (context) => new Promise((resolve, reject) => {
      pQueue.push({ context, resolve, reject });
      setImmediate(workerLoop);
    }),
    // expose status helpers
    pendingCount: () => pQueue.length,
  };
}
