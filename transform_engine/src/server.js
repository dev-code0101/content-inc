// src/server.js
const express = require('express');
const { v4: uuid } = require('uuid');
const jobQueue = require('./jobQueue');
const { createJobLogger, listJobs } = require('./logger');
const nodeCron = require('node-cron');

const app = express();
app.use(express.json());

const JOBS = new Map(); // jobId -> { id, status, createdAt, finishedAt, error, cron }

app.post('/api/run', async (req, res) => {
	const asyncRun = req.body?.async !== false; // default async true
  
	// If any job is currently running or queued, reject new job
	const active = Array.from(JOBS.values()).some(j => ['running', 'queued', 'scheduled'].includes(j.status));
	if (active) {
	  return res.status(409).json({ error: 'A job is already running or queued. Try again later.' });
	}
  
	const jobId = uuid();
	const logger = createJobLogger(jobId);
	const context = { jobId, logger };
  
	JOBS.set(jobId, { id: jobId, status: 'queued', createdAt: new Date().toISOString() });
  
	try {
	  const enqueuePromise = jobQueue.enqueue(context);
  
	  // mark as running when worker starts (best-effort)
	  JOBS.set(jobId, { ...JOBS.get(jobId), status: 'running', startedAt: new Date().toISOString() });
  
	  enqueuePromise.then(() => {
		JOBS.set(jobId, { ...JOBS.get(jobId), status: 'completed', finishedAt: new Date().toISOString() });
	  }).catch(err => {
		JOBS.set(jobId, { ...JOBS.get(jobId), status: 'failed', finishedAt: new Date().toISOString(), error: String(err) });
		logger.error('Job failed', err);
	  });
  
	  if (asyncRun) {
		res.status(202).json({ jobId, status: 'queued' });
	  } else {
		await enqueuePromise;
		const job = JOBS.get(jobId);
		res.json({ jobId, status: job.status });
	  }
	} catch (err) {
	  JOBS.set(jobId, { ...JOBS.get(jobId), status: 'failed', finishedAt: new Date().toISOString(), error: String(err) });
	  res.status(500).json({ error: String(err) });
	}
});
  

app.get('/api/jobs', (req, res) => {
  const out = Array.from(JOBS.values()).map(j => j);
  res.json(out);
});

app.get('/api/jobs/:id', (req, res) => {
  const id = req.params.id;
  const job = JOBS.get(id);
  if (!job) return res.status(404).json({ error: 'not found' });
  const logger = createJobLogger(id);
  res.json({ ...job, logs: logger.getLines() });
});

app.post('/api/schedule', (req, res) => {
  const { cron } = req.body || {};
  if (!cron || !nodeCron.validate(cron)) return res.status(400).json({ error: 'invalid cron' });
  const id = uuid();
  const task = nodeCron.schedule(cron, () => {
    const jobId = uuid();
    const logger = createJobLogger(jobId);
    const context = { jobId, logger };
    jobQueue.enqueue(context).catch(err => logger.error('scheduled job failed', err));
    JOBS.set(jobId, { id: jobId, status: 'queued', createdAt: new Date().toISOString(), scheduled: true });
  });
  JOBS.set(id, { id, status: 'scheduled', cron, createdAt: new Date().toISOString() });
  res.json({ id, status: 'scheduled', cron });
});

const PORT = process.env.PORT || 4000;
app.listen(PORT, () => console.log(`Orchestrator API listening on ${PORT}`));
