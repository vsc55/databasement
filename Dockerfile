FROM davidcrty/backup-manager-php:latest AS backend-build

COPY --chown=1000:1000 . /app

RUN composer install --dev --no-interaction --no-progress --no-suggest --optimize-autoloader
RUN php artisan vendor:publish --force --tag=livewire:assets


FROM node:22-slim AS frontend-build

WORKDIR /app
COPY package.json .
RUN npm install

COPY --from=backend-build /app /app
RUN npm run build


FROM davidcrty/backup-manager-php:latest

COPY --from=backend-build /app /app
COPY --from=frontend-build /app/public/build /app/public/build
