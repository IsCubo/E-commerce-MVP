# Beauty Shop E-commerce

A modern, elegant e-commerce application built with Laravel 12, Tailwind CSS, and Flowbite. Designed for beauty products with WhatsApp integration for sales.

## Features

- **Storefront**:
    - Modern and responsive design.
    - Featured products, Offers, and Combos.
    - Product filtering by Category.
    - Search functionality.
    - "Buy on WhatsApp" integration with dynamic message generation.
- **Admin Panel**:
    - Dashboard with statistics.
    - Full CRUD for Products (with multi-image upload).
    - CRUD for Categories.
    - CRUD for Combos (Bundles).
    - Settings management (Brand Name, WhatsApp Number, Welcome Message, Logo).
    - Secure Authentication.
- **Automatic image compression**: every image uploaded from the admin (product photos, combo image, logo) is resized and converted to WebP before it's stored — smaller files, faster page loads, without any manual step.
- **Pluggable storage**: images are saved through a single disk setting (`public` locally, or an S3-compatible bucket like Cloudflare R2 in production) — no code changes needed to switch.
- **Demo data included**: the seeders populate 5 categories, 11+ products (a mix of regular, discounted, and "on offer" items, each with a generated placeholder image) and 3 combos, so the store looks fully stocked right after setup — no manual data entry needed. Re-seeding also prunes any leftover placeholder images that no longer belong to a product/combo.

## Tech Stack

- **Framework**: Laravel 12
- **Database**: SQLite locally by default; MySQL (tested against [Aiven](https://aiven.io)) supported out of the box for production.
- **Image processing**: [Intervention Image](https://image.intervention.io/) v3 (requires the PHP `gd` or `imagick` extension — see [Troubleshooting](#troubleshooting)).
- **Object storage**: local disk by default; Cloudflare R2 (or any S3-compatible service) supported via `league/flysystem-aws-s3-v3`.
- **Frontend**: Blade Templates, Tailwind CSS 4 (via Vite), Flowbite (CDN)
- **Admin Theme**: AdminLTE 3 (CDN)
- **Deployment**: multi-stage `Dockerfile` (Nginx + PHP-FPM on Alpine) — see [Production Deployment](#production-deployment-docker).

## Installation

### Option A — one command (recommended)

```bash
composer install
composer run setup
```

`composer run setup` copies `.env`, generates the app key, creates the SQLite database file, runs migrations **with the demo seed data**, links storage, and builds the frontend assets. After it finishes, skip to [Run Development Server](#run-development-server).

### Option B — step by step

1.  **Clone the repository**:
    ```bash
    git clone <repository_url>
    cd E-commerce-MVP
    ```

2.  **Install PHP dependencies**:
    ```bash
    composer install
    ```

3.  **Environment Setup**:
    ```bash
    cp .env.example .env
    php artisan key:generate
    ```
    Ensure `DB_CONNECTION=sqlite` is set in `.env` (it is, by default).

4.  **Create the SQLite database file**:

    Laravel does **not** create this file for you — `php artisan migrate` fails with
    `Database file at path [...] does not exist` if it's missing. Create it with a
    plain PHP command, which works the same on Windows (PowerShell/cmd), macOS, and
    Linux — unlike the Unix `touch` command, which PowerShell/cmd don't have:
    ```bash
    php -r "touch('database/database.sqlite');"
    ```

5.  **Run migrations and seed the demo data**:
    ```bash
    php artisan migrate --seed
    ```

6.  **Storage Link** (serves uploaded/seeded images at `/storage/...`):
    ```bash
    php artisan storage:link
    ```

7.  **Frontend assets**:
    ```bash
    npm install
    npm run build
    ```

## Run Development Server

```bash
php artisan serve
```

## Access

- **Public Store**: `http://127.0.0.1:8000`
- **Admin Panel**: `http://127.0.0.1:8000/login`

### Default Credentials
- **Email**: `admin@admin.com`
- **Password**: `password`

## Customization

Log in to the Admin Panel to update:
- Brand Name
- WhatsApp Number
- Welcome Message
- Logo

## Image Uploads & Compression

All image uploads (product photos, combo image, logo) go through `App\Services\ImageUploadService`, which:

- Resizes the image down if it's wider than 1600px (never upscales).
- Converts it to **WebP** (quality 75) before saving.
- Saves it to the disk configured in `UPLOADS_DISK` (`.env`) — `public` (local `storage/app/public`) by default, or `r2` for an S3-compatible bucket.

**Requires PHP's `gd` or `imagick` extension.** If neither is installed, uploads still work (the service falls back to saving the original file untouched) but images won't be compressed — check with `php -m | grep -i "gd\|imagick"`. If it's missing, install/enable the `gd` extension for your PHP installation and re-run `composer install` so `intervention/image` is present in `vendor/`.

### Using Cloudflare R2 (or any S3-compatible storage) instead of local disk

1. Create a bucket and an API token (Account API Token, scoped to that bucket) in the Cloudflare dashboard.
2. Enable public access on the bucket (Settings → Public Access) to get a public URL.
3. Set these in `.env`:
    ```env
    UPLOADS_DISK=r2
    R2_ACCESS_KEY_ID=...
    R2_SECRET_ACCESS_KEY=...
    R2_BUCKET=...
    R2_ENDPOINT=https://<account_id>.r2.cloudflarestorage.com
    R2_URL=https://<your-public-bucket-url>.r2.dev
    R2_REGION=auto
    ```
Leave `UPLOADS_DISK=public` (the default) to keep using local storage.

## Using a managed MySQL database (e.g. Aiven)

The default `DB_CONNECTION=sqlite` works out of the box for local development. For production, point to any MySQL 8+ instance:

```env
DB_CONNECTION=mysql
DB_HOST=your-service.aivencloud.com
DB_PORT=your-port
DB_DATABASE=defaultdb
DB_USERNAME=avnadmin
DB_PASSWORD=...
MYSQL_ATTR_SSL_CA=storage/certs/aiven-ca.pem
```

Managed providers like Aiven require SSL — download the CA certificate from your provider's dashboard, place it anywhere under the project (a good spot is `storage/certs/`, already excluded from git via `.gitignore`), and point `MYSQL_ATTR_SSL_CA` at it (relative or absolute path both work — see `config/database.php`).

## Caching & Performance

With a remote database (like Aiven), every uncached query pays real network round-trip time. This app caches the things that don't need to be fresh on every request:

- **Persistent DB connections** (`config/database.php`, `PDO::ATTR_PERSISTENT`): avoids repeating the SSL handshake with the database on every request. Disable with `DB_PERSISTENT=false` if it ever causes issues.
- **`CACHE_STORE=file`** and **`SESSION_DRIVER=file`** by default: cache and sessions stay local instead of round-tripping to the database. ⚠️ If you scale to more than one app instance, move these to a shared store (e.g. Redis) — file-based cache/sessions aren't shared across instances.
- **Global settings** (brand name, WhatsApp number, logo, etc.) are cached indefinitely via `Setting::cached()` and invalidated automatically whenever they're saved from the admin (`SettingController`).
- **Home page data** (offers, latest products, combos, categories) is cached for 10 minutes and invalidated automatically when a product, combo, or category is created/updated/deleted from the admin.

If you add new data that should be cached, invalidate it explicitly from wherever it's written — there's no automatic cache tagging (the `file` driver doesn't support tags).

## Production Deployment (Docker)

The included `Dockerfile` is a 3-stage build: **frontend** (Node/Vite/Tailwind) → **vendor** (Composer, no dev deps) → **runtime** (PHP 8.4-FPM + Nginx on Alpine, with Nginx and PHP-FPM run together via Supervisor).

### Build

```bash
docker build -t ecommerce-mvp:latest .
```

### Run

```bash
docker run -d --name ecommerce-mvp \
  --env-file .env \
  -e PORT=80 \
  -v /path/to/storage/certs:/var/www/html/storage/certs:ro \
  -p 8080:80 \
  ecommerce-mvp:latest
```

Notes:
- `PORT` controls what Nginx listens on inside the container — most PaaS providers (Render, etc.) inject this automatically.
- The container image does **not** include `storage/app/public/*` or `storage/certs` (see `.dockerignore`) — mount the Aiven CA certificate as shown above, or set `AIVEN_CA_CERT_BASE64` (base64-encoded cert content) if your host doesn't support mounting files. In production, use `UPLOADS_DISK=r2` so uploaded images don't depend on the container's local (ephemeral) disk at all.
- Set `RUN_MIGRATIONS=true` to run `php artisan migrate --force` automatically on container start (fine for a single instance; for multiple replicas, migrate as a separate deploy step instead).
- The entrypoint caches config/routes/views on every start using the container's real environment variables — never bake `.env` values into the image at build time.

## Environment Variables Reference

Beyond Laravel's defaults, this project adds:

| Variable | Default | Purpose |
|---|---|---|
| `UPLOADS_DISK` | `public` | Disk used for product/combo/logo images. Set to `r2` to use Cloudflare R2. |
| `R2_ACCESS_KEY_ID`, `R2_SECRET_ACCESS_KEY`, `R2_BUCKET`, `R2_ENDPOINT`, `R2_URL`, `R2_REGION` | — | Cloudflare R2 (S3-compatible) credentials, used when `UPLOADS_DISK=r2`. |
| `MYSQL_ATTR_SSL_CA` | — | Path (relative or absolute) to the CA certificate required by managed MySQL providers like Aiven. |
| `DB_PERSISTENT` | `true` | Whether to reuse DB connections across requests within the same PHP-FPM worker. |
| `RUN_MIGRATIONS` | — | Docker only: set to `true` to run migrations automatically on container start. |
| `AIVEN_CA_CERT_BASE64` | — | Docker only: base64-encoded CA cert content, written to `MYSQL_ATTR_SSL_CA` at container start (alternative to mounting the file). |

## Troubleshooting

- **`Database file at path [...] does not exist`**: you skipped step 4 above (or the
  `touch database/database.sqlite` command silently did nothing because your shell
  doesn't have `touch`, which is the case on native Windows PowerShell/cmd). Run
  `php -r "touch('database/database.sqlite');"` instead — it works on every platform.
- **Re-seeding an existing database**: `php artisan db:seed` (or `migrate --seed`) is
  safe to run more than once — it updates existing rows by slug instead of creating
  duplicates.
- **Reset the database completely** (drops all tables and re-seeds from scratch):
    ```bash
    php artisan migrate:fresh --seed
    ```
- **Just (re)populate the demo catalog** (categories, products with images, combos)
  without touching the admin user or settings — one specific command:
    ```bash
    php artisan catalog:seed
    ```
  Also safe to run repeatedly; it updates the existing catalog instead of duplicating it.
- **Uploaded images aren't compressed / `Class "Intervention\Image\ImageManager" not found`**: the `gd` (or `imagick`) PHP extension isn't installed, so `composer install` couldn't pull in `intervention/image` properly, or the extension isn't loaded. Check with `php -m | grep -i "gd\|imagick"`; enable the extension in your `php.ini`, then re-run `composer install`.
- **`SQLSTATE[HY000] [2002] Cannot connect to MySQL using SSL`**: `MYSQL_ATTR_SSL_CA` isn't pointing at a real, readable certificate file. Double-check the path (it's resolved relative to the project root if it isn't absolute — see `config/database.php`).
- **`composer install` fails with a certificate/SSL error downloading packages**: this is a network/TLS issue between Composer and GitHub (often caused by a corporate proxy that intercepts HTTPS), not a problem with this project. Try a different network, or ask your network/IT team to allow `github.com` / `api.github.com`. Avoid disabling Composer's TLS verification as a "fix."
