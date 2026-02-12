# Beauty Shop E-commerce

A modern, elegant e-commerce application built with Laravel 11, Tailwind CSS, and Flowbite. Designed for beauty products with WhatsApp integration for sales.

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

## Tech Stack

- **Framework**: Laravel 11
- **Database**: SQLite
- **Frontend**: Blade Templates, Tailwind CSS (CDN), Flowbite (CDN)
- **Admin Theme**: AdminLTE 3 (CDN)

## Installation

1.  **Clone the repository**:
    ```bash
    git clone <repository_url>
    cd E-commerce
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
    Ensure `DB_CONNECTION=sqlite` is set in `.env`.

4.  **Database & Migrations**:
    ```bash
    touch database/database.sqlite
    php artisan migrate --seed
    ```

5.  **Storage Link**:
    ```bash
    php artisan storage:link
    ```

6.  **Run Development Server**:
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
