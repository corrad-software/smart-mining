FROM node:22-alpine AS build

WORKDIR /src
ENV PLAYWRIGHT_SKIP_BROWSER_DOWNLOAD=1

COPY frontend/package.json frontend/package-lock.json ./
RUN npm ci

COPY frontend/index.html frontend/vite.config.js ./
COPY frontend/src ./src
COPY frontend/public ./public

RUN npm run build

FROM nginx:1.27-alpine

COPY docker/web/nginx.conf /etc/nginx/conf.d/default.conf
COPY docker/web/40-api-url.sh /docker-entrypoint.d/40-api-url.sh
COPY --from=build /src/dist /usr/share/nginx/html

RUN chmod 755 /docker-entrypoint.d/40-api-url.sh \
 && sed -i 's/\r$//' /docker-entrypoint.d/40-api-url.sh

EXPOSE 80
