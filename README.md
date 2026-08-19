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
- **Demo data included**: the seeders populate 5 categories, 11+ products (a mix of regular, discounted, and "on offer" items, each with a generated placeholder image) and 3 combos, so the store looks fully stocked right after setup — no manual data entry needed.

## Tech Stack

- **Framework**: Laravel 12
- **Database**: SQLite
- **Frontend**: Blade Templates, Tailwind CSS (CDN), Flowbite (CDN)
- **Admin Theme**: AdminLTE 3 (CDN)

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
