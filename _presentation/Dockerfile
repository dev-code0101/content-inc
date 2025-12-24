# -------------------------------------------------
# 4️⃣ Front‑end (Node 20) – build static assets
# -------------------------------------------------
FROM node:20-alpine AS builder

WORKDIR /app
COPY _presentation/package*.json ./
RUN npm ci --omit=dev

COPY _presentation/ .
RUN npm run build   # produces ./dist (or ./build)

# -------------------------------------------------
# 5️⃣ Serve with nginx (static)
# -------------------------------------------------
FROM nginx:alpine AS prod

# Remove default nginx static files
RUN rm -rf /usr/share/nginx/html/*

# Copy built assets
COPY --from=builder /app/dist /usr/share/nginx/html

# Optional: custom nginx config
# COPY _presentation/nginx.conf /etc/nginx/conf.d/default.conf

EXPOSE 80
CMD ["nginx", "-g", "daemon off;"]
