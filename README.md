# E-Commerce API

A RESTful e-commerce API built with Laravel 11, featuring authentication, product management, cart, orders, Stripe payments, and real-time order updates via Laravel Reverb (WebSockets).

## Tech Stack

- **Framework:** Laravel 11
- **Database:** PostgreSQL (production) / MySQL (local)
- **Authentication:** Laravel Sanctum (Bearer tokens)
- **Payments:** Stripe
- **Real-time:** Laravel Reverb (WebSockets via Pusher protocol)
- **Deployment:** Render

---

## Local Setup

### Requirements
- PHP 8.2+
- Composer
- MySQL (via XAMPP)

### Install

```bash
git clone <repo-url>
cd ecommerce-api
composer install
cp .env.example .env
php artisan key:generate
php artisan migrate --seed
```

### Run

```bash
php artisan serve          # API on http://localhost:8000
php artisan reverb:start   # WebSocket server on ws://localhost:8081
```

---

## Authentication

All protected routes require a Bearer token in the `Authorization` header:

```
Authorization: Bearer <token>
```

---

## API Endpoints

Base URL: `/api/v1`

### Auth

| Method | Endpoint | Description | Auth |
|--------|----------|-------------|------|
| POST | `/register` | Register a new user | No |
| POST | `/login` | Login and get token | No |
| POST | `/logout` | Logout | Yes |

### Products

| Method | Endpoint | Description | Auth |
|--------|----------|-------------|------|
| GET | `/products` | List all products | No |
| GET | `/products/{id}` | Get product details | No |
| POST | `/products` | Create product | Admin |
| PUT | `/products/{id}` | Update product | Admin |
| DELETE | `/products/{id}` | Delete product | Admin |

### Categories

| Method | Endpoint | Description | Auth |
|--------|----------|-------------|------|
| GET | `/categories` | List all categories | No |
| POST | `/categories` | Create category | Admin |
| PUT | `/categories/{id}` | Update category | Admin |
| DELETE | `/categories/{id}` | Delete category | Admin |

### Cart

| Method | Endpoint | Description | Auth |
|--------|----------|-------------|------|
| GET | `/carts` | View cart | Yes |
| POST | `/carts` | Add item to cart | Yes |
| PUT | `/carts/{product}` | Update item quantity | Yes |
| DELETE | `/carts/{product}` | Remove item | Yes |
| DELETE | `/carts` | Clear cart | Yes |

### Orders

| Method | Endpoint | Description | Auth |
|--------|----------|-------------|------|
| GET | `/orders` | List user orders | Yes |
| GET | `/orders/{id}` | Get order details | Yes |
| POST | `/orders` | Create order from cart | Yes |
| POST | `/orders/{id}/checkout` | Stripe checkout | Yes |

### Admin

| Method | Endpoint | Description | Auth |
|--------|----------|-------------|------|
| GET | `/admin/dashboard` | Dashboard stats | Admin |
| GET | `/admin/orders` | List all orders | Admin |
| PUT | `/admin/orders/{id}/status` | Update order status | Admin |

---

## Real-time Events

Uses Laravel Reverb (WebSocket). Connect via Pusher JS to listen for order updates:

- **Channel:** `private-orders.{userId}`
- **Event:** `order.status.updated`
- **Payload:** `{ order_id, order_number, status, updated_at }`

---

## Stripe Webhooks

```
POST /api/v1/stripe/webhook
```

Configure this URL in your Stripe dashboard under **Webhooks**.

---

## Environment Variables

Key variables required in production (Render):

```
APP_KEY=
APP_ENV=production
DB_CONNECTION=pgsql
DB_URL=postgresql://user:password@host/database
STRIPE_PUBLISHABLE_KEY=
STRIPE_SECRET_KEY=
STRIPE_WEBHOOK_SECRET=
REVERB_APP_ID=
REVERB_APP_KEY=
REVERB_APP_SECRET=
REVERB_HOST=
REVERB_PORT=
```
