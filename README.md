# 📝 Blog API - Complete Documentation

## 🌟 Project Overview
A blog API built with Laravel, featuring CRUD operations for posts/comments and **activity tracking** with revert functionality.

---

## 🧰 Tech Stack
**Core**
- Laravel 12
- PHP 8.2+
- MySQL 10+

**Key Packages**
- `darkaonline/l5-swagger` (API Docs)
- `zircote/swagger-php` (Swagger annotation and parsing library)
---

## 🚀 Features

### Core Functionality
| Feature             | Endpoint        | Method |
|---------------------|-----------------|--------|
| Post Management     | `/api/post`     | CRUD   |
| Category Management | `/api/category` | CRUD   |

### ✨ Activity Log System
- Automatic change tracking for all models
- Before/after snapshots stored as JSON
- Revert endpoint:
  ```bash
  POST /api/logs/revert/{logId}
  ```
- Filterable logs:
  ```bash
  GET /api/activities
  ```

---

## 🛠️ Installation
1. Clone repo:
   ```bash
   git clone https://github.com/halkawtabdulilah/blog-api.git
   ```
2. Install dependencies:
   ```bash
   composer install
   npm install
   ```
3. Configure `.env`:
   ```ini
   DB_DATABASE=your_db
   DB_USERNAME=your_user
   ```
4. Run migrations:
   ```bash
   php artisan migrate --seed
   ```

---

## 🧪 Testing
Run test suite:
```bash
php artisan test
```

---

## 📄 License

This project is licensed under the [MIT License](LICENSE).
You are free to use, modify, and distribute this software as permitted under the terms of the license.

---
