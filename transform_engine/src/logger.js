// src/logger.js
const MAX_LINES = 1000;
const store = new Map(); // jobId -> array of lines

function createJobLogger(jobId) {
  if (!store.has(jobId)) store.set(jobId, []);
  const push = (level, ...args) => {
    const line = { ts: new Date().toISOString(), level, msg: args.map(a => (typeof a==='object'?JSON.stringify(a):String(a))).join(' ') };
    const arr = store.get(jobId);
    arr.push(line);
    if (arr.length > MAX_LINES) arr.shift();
    // also emit to console
    console[level === 'error' ? 'error' : 'log'](`[${jobId}] ${line.ts} ${level.toUpperCase()} ${line.msg}`);
  };
  return {
    info: (...a) => push('info', ...a),
    warn: (...a) => push('warn', ...a),
    error: (...a) => push('error', ...a),
    getLines: () => (store.get(jobId) || []).slice(),
  };
}

function listJobs() {
  return Array.from(store.keys());
}

module.exports = { createJobLogger, listJobs };
