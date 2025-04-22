# 🌐 Translation Management Service

A **Laravel-based API** service for managing multilingual translations with tagging capabilities. Built for performance and scalability, this service enables teams to efficiently manage translation keys across multiple locales.

---

## 🚀 Features

- ✅ CRUD operations for translations
- 🌍 Multi-language support (locale management)
- 🏷️ Tagging system for contextual organization
- 🔍 Search and filter translations
- 🔒 Token-based authentication (Sanctum)
- ⚡ Handles 100k+ records efficiently
- 📦 JSON export endpoint for frontend consumption

---

## ⚙️ Technical Specifications

| Component   | Specification           |
| ----------- | ----------------------- |
| Framework   | Laravel 12.x            |
| PHP Version | 8.1+                    |
| Database    | MySQL 8.0+              |
| Cache       | Redis (recommended)     |
| Auth        | Laravel Sanctum         |
| Standards   | PSR-12 compliant        |
| Testing     | PHPUnit (>95% coverage) |

---

## 💪 Installation

### 🗓️ Prerequisites

- PHP 8.1+
- Composer 2.0+
- MySQL 8.0+

### 🧪 Setup Instructions

Clone the repository:

```bash
git clone https://github.com/yourusername/translation-service.git
cd translation-service
```

Install dependencies:

```bash
composer install
```

Copy and update the environment configuration:

```bash
cp .env.example .env
php artisan key:generate
```

Edit `.env` and set your database credentials:

```ini
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=translation_service
DB_USERNAME=root
DB_PASSWORD=
```

Run database migrations:

```bash
php artisan migrate
```

(Optional) Generate test data:

```bash
php artisan translations:generate 10000
```
---

## 📘 API Documentation

### 🔐 Authentication

| Endpoint           | Method | Description         |
| ------------------ | ------ | ------------------- |
| /api/auth/register | POST   | Register new user   |
| /api/auth/login    | POST   | Login and get token |
| /api/auth/logout   | POST   | Invalidate token    |

### 🌍 Translations

| Endpoint                 | Method | Description                     |
| ------------------------ | ------ | ------------------------------- |
| /api/translations        | GET    | List translations (paginated)   |
| /api/translations        | POST   | Create new translation          |
| /api/translations/{id}   | GET    | Get single translation          |
| /api/translations/{id}   | PUT    | Update translation              |
| /api/translations/{id}   | DELETE | Delete translation              |
| /api/translations/export | GET    | Export all translations as JSON |

### 📟 Query Parameters

- `?locale=en` – Filter by locale
- `?tags=web,mobile` – Filter by tags
- `?search=welcome` – Search in keys/values
- `?per_page=50` – Items per page (default: 20)

---

## 💪 Testing

Run the full test suite:

```bash
composer test
```

This includes:

- ✅ Unit tests
- ✅ Feature tests
- ✅ Code style (PSR-12)
- ✅ Static analysis

### 🧱 Performance Testing

Test with large datasets:

```bash
php artisan translations:generate 100000
php artisan test --filter=PerformanceTest
```

---

## 🚀 Deployment

### Production Recommendations

- Use Redis for cache and queues:

```ini
CACHE_DRIVER=redis
QUEUE_CONNECTION=redis
```

- Optimize Laravel:

```bash
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

- Set up queue workers for heavy background tasks

---

## 🧐 Design Decisions

### 📦 Database Schema

- Many-to-many tagging via a separate tags table
- Composite unique index on `(key, locale)`
- Proper indexing on searchable/filterable fields

### 📡 API Design

- RESTful JSON-based endpoints
- Resource classes for consistent responses
- Pagination support
- Export endpoint cached with Redis

### ⚡ Performance

- Chunked data generation
- Eager loading relationships
- Index-optimized queries
- caching for high-load endpoints

### 🔒 Security

- Sanctum token authentication
- Request validation


