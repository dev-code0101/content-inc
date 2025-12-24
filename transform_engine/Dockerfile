# -------------------------------------------------
# 3️⃣ Node 20 (Alpine)
# -------------------------------------------------
FROM node:20-alpine AS builder

# Create app directory
WORKDIR /usr/src/app

# Install deps (cache layer)
COPY transform_engine/package*.json ./
RUN npm ci --omit=dev

# Copy source
COPY transform_engine/ .

# Build step (if you have a build, e.g., TypeScript)
# RUN npm run build   # uncomment if needed

# -------------------------------------------------
# 4️⃣ Runtime image
# -------------------------------------------------
FROM node:20-alpine AS runtime

WORKDIR /usr/src/app
COPY --from=builder /usr/src/app/node_modules ./node_modules
COPY --from=builder /usr/src/app ./

# Non‑root user
RUN addgroup -S appgroup && adduser -S appuser -G appgroup
USER appuser

EXPOSE 3000
CMD ["node", "src/index.js"]
