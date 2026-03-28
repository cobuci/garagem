# Garagem

Management system for auto parts and repair shops. Covers sales, inventory, customers, purchases, reports, and financials.

## Stack

- PHP 8.4 + Laravel 12
- Livewire 4 + Alpine.js
- Tailwind CSS 4
- MySQL
- Redis (queues via Horizon)
- Node.js (invoice PNG generation via Browsershot/Puppeteer)

---

## Requirements

### Local development

| Dependency | Min version |
|---|---|
| PHP | 8.4 |
| Composer | 2.x |
| Node.js | 18+ |
| npm | 9+ |
| MySQL | 8.0+ |
| Redis | 6+ |

**Required PHP extensions:** `pdo_mysql`, `redis`, `pcntl`, `bcmath`, `mbstring`, `xml`, `zip`, `gd`

---

## Local setup

```bash
# 1. Clone the repository
git clone <repo-url>
cd garagem

# 2. Install dependencies, configure .env, run migrations, and build assets
composer run setup
```

`composer run setup` runs the following steps:
- `composer install`
- Copies `.env.example` to `.env` (if not already present)
- Generates `APP_KEY`
- Runs database migrations
- `npm install`
- `npm run build`

### Configure .env

Edit `.env` with your database credentials, Redis connection, and any required variables:

```dotenv
APP_NAME="Garagem"
APP_URL=http://localhost
APP_LOCALE=pt_BR

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=garagem
DB_USERNAME=root
DB_PASSWORD=your_password

QUEUE_CONNECTION=redis
REDIS_HOST=127.0.0.1
REDIS_PORT=6379

# Absolute paths to Node binaries — required for invoice PNG generation
BROWSERSHOT_NODE_BINARY=/usr/bin/node   # macOS: /opt/homebrew/bin/node
BROWSERSHOT_NPM_BINARY=/usr/bin/npm     # macOS: /opt/homebrew/bin/npm
```

### Seed initial data (optional)

```bash
# Roles and permissions — required for authentication to work correctly
php artisan db:seed --class=RolesAndPermissionsSeeder

# Full sample dataset
php artisan db:seed
```

### Start the development server

```bash
composer run dev
```

Runs concurrently: PHP server (`localhost:8000`), queue worker, log viewer (Pail), and Vite (HMR).

---

## Docker / Laravel Sail

```bash
# Start containers (MySQL is not included — use a local MySQL or add it to compose.yaml)
./vendor/bin/sail up -d

# Run migrations inside the container
./vendor/bin/sail artisan migrate

# Install JS dependencies and build assets
./vendor/bin/sail npm install && ./vendor/bin/sail npm run build
```

The `compose.yaml` includes **Redis** and **Mailhog**. Add a MySQL service if needed.

---

## Queues and Horizon

This project uses **Laravel Horizon** to manage Redis queues. Background jobs such as invoice generation (PDF/PNG) run asynchronously.

```bash
# Development
php artisan horizon

# Check status
php artisan horizon:status

# Web dashboard — http://localhost/horizon
```

> Production access to the Horizon dashboard is controlled by the `viewHorizon` gate in `App\Providers\HorizonServiceProvider`.

---

## Server requirements (production)

### Software

| Component | Version | Notes |
|---|---|---|
| PHP | 8.4+ | Extensions: `pdo_mysql`, `redis`, `pcntl`, `bcmath`, `mbstring`, `xml`, `zip`, `gd` |
| MySQL | 8.0+ | Primary database |
| Redis | 6+ | Queues (Horizon) and cache |
| Node.js | 18+ | **Required** — Browsershot uses Puppeteer to generate invoice PNGs |
| npm | 9+ | Used to install Puppeteer (`npm install` in the project directory) |
| Supervisor | any | Keeps the Horizon process running in the background |

### Supervisor configuration (Horizon)

Create `/etc/supervisor/conf.d/horizon.conf`:

```ini
[program:horizon]
process_name=%(program_name)s
command=php /path/to/project/artisan horizon
autostart=true
autorestart=true
user=www-data
redirect_stderr=true
stdout_logfile=/var/log/horizon.log
stopwaitsecs=3600
```

```bash
sudo supervisorctl reread
sudo supervisorctl update
sudo supervisorctl start horizon
```

### Production environment variables

```dotenv
APP_ENV=production
APP_DEBUG=false
APP_URL=https://your-domain.com

QUEUE_CONNECTION=redis
SESSION_DRIVER=database
CACHE_STORE=database

# Absolute paths to Node binaries — required for PNG generation
BROWSERSHOT_NODE_BINARY=/usr/bin/node
BROWSERSHOT_NPM_BINARY=/usr/bin/npm
```

### Post-deploy checklist

```bash
composer install --no-dev --optimize-autoloader
npm install           # installs Puppeteer
npm run build
php artisan migrate --force
php artisan db:seed --class=RolesAndPermissionsSeeder
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan horizon:publish
```

### Directory permissions

```bash
chmod -R 775 storage bootstrap/cache
chown -R www-data:www-data storage bootstrap/cache
```

---

## Testing

```bash
# Run all tests
php artisan test

# Filter by name
php artisan test --filter=SalesByPaymentMethodTest

# Static analysis
./vendor/bin/phpstan analyse

# Code formatting
./vendor/bin/pint --dirty
```

---

## Environment variables reference

| Variable | Description | Default |
|---|---|---|
| `APP_NAME` | Application name | `Laravel` |
| `APP_LOCALE` | Language (`pt_BR` or `en`) | `en` |
| `DB_*` | MySQL credentials | — |
| `REDIS_HOST` | Redis host | `127.0.0.1` |
| `REDIS_PORT` | Redis port | `6379` |
| `REDIS_PASSWORD` | Redis password | `null` |
| `QUEUE_CONNECTION` | Queue driver | `redis` |
| `HORIZON_PREFIX` | Redis key prefix for Horizon | `horizon:` |
| `BROWSERSHOT_NODE_BINARY` | Absolute path to the `node` binary | `/usr/bin/node` |
| `BROWSERSHOT_NPM_BINARY` | Absolute path to the `npm` binary | `/usr/bin/npm` |
