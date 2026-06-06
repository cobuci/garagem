# Garagem

Management platform for sales, inventory, customers, purchases, reports, and financial operations.

| | |
|---|---|
| **Backend** | PHP 8.4, Laravel 13, Livewire 4 |
| **Frontend** | Tailwind CSS 4, Vite |
| **Data** | MySQL 8+ |
| **Queues** | Redis, Laravel Horizon |
| **PDF/PNG** | Browsershot, Puppeteer (Node.js) |

---

## Local development

`composer run setup` is intended for a **native local environment** (PHP, MySQL, and Redis installed on your machine). It does not start Docker containers.

### 1. Prerequisites

Install and run the following before setup:

| Tool | Version |
|---|---|
| PHP | 8.4+ |
| Composer | 2.x |
| Node.js | 18+ |
| MySQL | 8.0+ |
| Redis | 6+ |

Required PHP extensions: `pdo_mysql`, `redis`, `pcntl`, `bcmath`, `mbstring`, `xml`, `zip`, `gd`

Create the database:

```sql
CREATE DATABASE garagem CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
```

### 2. Install

```bash
composer run setup
```

This command:

1. Installs Composer dependencies  
2. Copies `.env.example` to `.env` (if missing), with local defaults (`APP_URL`, `DB_DATABASE=garagem`, `REDIS_HOST=127.0.0.1`, `QUEUE_CONNECTION=redis`, etc.)  
3. Generates the application key  
4. Creates the storage symlink  
5. Runs database migrations  
6. Seeds roles and permissions  
7. Installs npm packages and builds frontend assets  

After setup, review `.env` only if you need to change `DB_PASSWORD` or Browsershot paths (`BROWSERSHOT_NODE_BINARY`, `BROWSERSHOT_NPM_BINARY`).

Optional sample data:

```bash
php artisan db:seed
```

### 3. Run

```bash
composer run dev
```

Starts the HTTP server, queue worker, log stream (Pail), and Vite with hot reload.

Open [http://localhost:8000](http://localhost:8000).

---

## Commands

| Command | Description |
|---|---|
| `composer run setup` | First-time local install |
| `composer run dev` | Local development stack |
| `php artisan horizon` | Queue dashboard and worker supervisor |
| `php artisan test` | Test suite |
| `./vendor/bin/pint --dirty` | Code style (changed files) |
| `./vendor/bin/phpstan analyse` | Static analysis |

---

## Docker (optional)

[Laravel Sail](https://laravel.com/docs/sail) is available for containerized services. **Do not use `composer run setup` inside Sail** for the initial workflow—configure `.env` for Docker, start containers, then run Artisan through Sail.

```bash
cp .env.example .env
# Set REDIS_HOST=redis and database host for your compose setup
./vendor/bin/sail up -d
./vendor/bin/sail composer install
./vendor/bin/sail artisan key:generate
./vendor/bin/sail artisan storage:link
./vendor/bin/sail artisan migrate
./vendor/bin/sail artisan db:seed --class=RolesAndPermissionsSeeder
./vendor/bin/sail npm install && ./vendor/bin/sail npm run build
```

`compose.yaml` provides Redis and Mailhog. MySQL is expected on the host unless you add it to Compose. Host Redis port defaults to `6380` (`FORWARD_REDIS_PORT`) to avoid conflicting with a local Redis instance on `6379`.

---

## Production

```bash
composer install --no-dev --optimize-autoloader
cp .env.example .env   # configure for production
php artisan key:generate --force
php artisan storage:link
php artisan migrate --force
php artisan db:seed --class=RolesAndPermissionsSeeder --force
npm ci && npm run build
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

Run Horizon under process supervision (e.g. Supervisor) and ensure `storage/` and `bootstrap/cache/` are writable by the web server.

---

## License

MIT
