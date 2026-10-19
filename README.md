# ux-native-scanner

## Prerequisites
- Docker (optional if you use the built‑in DB)
- PHP 8.5+, Composer
- Node / npm (only if you manage additional JS, but assets are handled by Symfony AssetMapper)

## Commands

### 1. Start the database with Docker
```bash
docker compose up -d   # starts PostgreSQL defined in compose.yaml
```
*(If you prefer SQLite or a local DB, set `DATABASE_URL` accordingly and skip this step.)*

### 2. Install PHP dependencies
```bash
composer install
```

### 3. Install and compile front‑end assets (AssetMapper)
```bash
# AssetMapper is already required in composer.json
php bin/console asset-map:compile   # compile assets for production
# In dev mode assets are served automatically.
```

### 4. Run the Symfony server
```bash
symfony serve -d   # detached (or `php -S 127.0.0.1:8000 -t public/`)
```
Open http://127.0.0.1:8000 in your browser.

### 5. (Optional) Stop Docker services
```bash
docker compose down
```

## Quick start (all in one)
```bash
docker compose up -d && composer install && php bin/console asset-map:compile && symfony serve -d
```
