# 📦 Fashion Shop (Symfony 7.4 LTS)

Demo e-commerce project built with **Symfony 7.4 (LTS)**, **PHP 8.2**, **Twig**, **MySQL/MariaDB** and **Stripe Checkout (test mode)**.

The project demonstrates a clean backend architecture, real-world e-commerce features, and a production-oriented mindset.

---

## 🧱 Tech Stack

- Symfony 7.4 (LTS)
- PHP 8.2
- Twig
- Doctrine ORM
- MySQL / MariaDB
- EasyAdmin
- Stripe Checkout (test mode)
- Docker (Nginx + PHP-FPM + MariaDB)

---

## 🚀 Features

### Public
- Product catalog
- Filters by gender and category
- Product details page
- Session-based shopping cart
- Checkout flow

### Authentication
- User registration & login
- Role-based access (admin / user)

### Admin Panel
- Admin dashboard (EasyAdmin)
- Create / edit / delete products
- Product image upload
- Automatic image cleanup on update/delete

### Orders & Payments
- Order creation (status: `pending`)
- Snapshot pricing (prices stored in cents for orders)
- Stripe Checkout integration (test mode)
- Redirect to Stripe payment page
- Success and cancel pages

> ⚠️ **Important:**  
> Payment confirmation is finalized via Stripe Webhooks  
> (`checkout.session.completed`).  
> The checkout redirect flow is implemented and testable.

---

### Stripe setup (optional)
To test payments locally, create your own Stripe account and add your test keys to `.env.local`.
The repository contains only placeholder keys in `.env`.

## 🛒 Stripe (Test Mode)

This project uses **Stripe Checkout** in **test mode**.

### Test Card
4242 4242 4242 4242  
Any future date  
CVC: 123

## 🐳 Run with Docker (Recommended)

### Requirements
- Docker
- Docker Compose

### Setup
```bash
docker compose up -d --build
docker compose exec php composer install
docker compose exec php php bin/console doctrine:migrations:migrate

# Optional demo data
docker compose exec php php bin/console doctrine:fixtures:load
```

Open http://localhost:8080
