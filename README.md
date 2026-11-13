# Buyora

Buyora is an e-commerce store built with Laravel. It was built as a university field-work project. Customers browse products by category, search, add items to a cart or wishlist, and place orders. The admin panel manages the catalog, orders, users, site settings, and contact messages.

## Stack

PHP 8.1+, Laravel 10, Livewire 3, Blade, MySQL or MariaDB, Bootstrap 5, Intervention Image.

## Setup

1. Clone the repository.
2. Run `composer install`.
3. Copy `.env.example` to `.env` and run `php artisan key:generate`.
4. Create an empty MySQL database named `buyora` and check the database settings in `.env`.
5. Run `php artisan migrate`.
6. Run `php artisan storage:link`.
7. Start the server with `php artisan serve`.

The database seeder is empty, so a fresh install has no catalog. Add categories, brands, and products from the admin panel. Product images are stored in `public/uploads`.

## Admin access

Register an account on the site, then set that user's `roll_as` to `1` in the `users` table. That account can open the admin dashboard.

## Payments

Cash on Delivery is the main payment method. SSLCommerz and aamarPay are also available in sandbox mode. Their test credentials are the defaults in `config/services.php`; set real credentials in `.env` before taking live payments. No real money moves while the gateways stay in sandbox mode.

## Notes

The shop runs locally. Search matches the product name, brand, category, description, and a per-product search keywords field. A product can have more than one image, shown as a thumbnail gallery on the product page. Admin lists have a one-click Hide/Show toggle. Orders carry a tracking number starting with `buyora-`.
