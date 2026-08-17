# 🌟 Showmora (Pocket Showroom) API — Live Production Backend

[![API Status](https://img.shields.io/badge/API_Status-LIVE_ONLINE-success?style=for-the-badge&logo=render)](https://pocket-showroom-api.onrender.com/api/health)
[![Laravel](https://img.shields.io/badge/Laravel-11.x-FF2D20?style=for-the-badge&logo=laravel)](https://laravel.com)
[![PostgreSQL](https://img.shields.io/badge/PostgreSQL-Cloud_DB-336791?style=for-the-badge&logo=postgresql)](https://www.postgresql.org)
[![PHP](https://img.shields.io/badge/PHP-8.4-777BB4?style=for-the-badge&logo=php)](https://www.php.net)

---

## 🚀 Live Production Links

| Service | Live URL | Description |
|---|---|---|
| **🌐 API Base URL** | [https://pocket-showroom-api.onrender.com](https://pocket-showroom-api.onrender.com) | Live Production API Server |
| **🩺 Health Check** | [https://pocket-showroom-api.onrender.com/api/health](https://pocket-showroom-api.onrender.com/api/health) | API Status & Health Check |
| **🛍️ Marketplace API** | [https://pocket-showroom-api.onrender.com/api/marketplace/home](https://pocket-showroom-api.onrender.com/api/marketplace/home) | Live Marketplace Feed & Categories |
| **👑 Super Admin Portal** | [https://pocket-showroom-api.onrender.com/admin](https://pocket-showroom-api.onrender.com/admin) | Live Web Admin Dashboard |
| **✨ Customer Showroom** | [https://pocket-showroom-api.onrender.com/showrooms/demo-jewelry-lounge](https://pocket-showroom-api.onrender.com/showrooms/demo-jewelry-lounge) | Responsive Web Showroom View |

---

## ⚡ Key Features

1. **Multi-Tenant Business & Showroom Architecture:**
   - Isolated catalog, categories, products, inquiries, and staff RBAC per business.
   - Dynamic branding: logo, banner, gallery, themes, contact details, and social links.

2. **Full-Featured Marketplace:**
   - Discover shops by category, location, and search keywords.
   - Featured & verified shop badges with organic search ranking.

3. **Super Admin Web Dashboard (`/admin`):**
   - Manage all shop owners, shops, products, and customers.
   - One-click shop verification, featured status, and account activation/suspension.
   - Comprehensive audit logging and activity metrics.

4. **Analytics & Marketing Attribution:**
   - Short tracked share links (`/s/{code}`).
   - Real-time visitor attribution, inquiry conversion tracking, and shop performance metrics.

5. **Authentication & Security:**
   - Firebase Email/Password & Google Sign-In with Laravel Sanctum tokens.
   - Permission-based middleware for owner and staff roles.

---

## 📡 API Endpoints Overview

### 🟢 Public Endpoints
- `GET /api/health` — Check server status
- `POST /api/auth/firebase-login` — Exchange Firebase ID Token for Sanctum bearer token
- `GET /api/marketplace/home` — Marketplace feed (featured shops, categories, recent products)
- `GET /api/marketplace/categories` — List all marketplace categories
- `GET /api/marketplace/shops` — Browse verified and active shops
- `GET /api/marketplace/search` — Search marketplace shops and items
- `GET /api/public/showrooms/{slug}` — Public business profile and catalog
- `GET /s/{code}` — Resolve and track short share links

### 🔵 Business Owner / Staff Endpoints (Bearer Token)
- `GET /api/me` — Current authenticated user profile
- `GET /api/dashboard` — Business dashboard summary and metrics
- `GET /api/business` — Retrieve business details
- `POST /api/business` — Create or update business profile
- `POST /api/business/logo` — Upload business logo
- `POST /api/business/banner` — Upload business banner
- `GET /api/products` — Manage products catalog
- `POST /api/products` — Create new product with multiple images
- `GET /api/analytics` — Business analytics, views, and inquiries

### 🟣 Super Admin Endpoints (`/api/super-admin/*`)
- `GET /api/super-admin/dashboard` — Platform-wide metrics
- `GET /api/super-admin/owners` — All registered shop owners
- `GET /api/super-admin/shops` — All registered shops with toggle controls
- `POST /api/super-admin/shops/{id}/verify` — Toggle verified badge
- `POST /api/super-admin/shops/{id}/feature` — Toggle featured badge
- `POST /api/super-admin/shops/{id}/toggle-active` — Enable/disable shop
- `GET /api/super-admin/audit-logs` — System audit logs

---

## 🛠️ Tech Stack & Deployment

- **Framework:** Laravel 11.x (PHP 8.4)
- **Database:** PostgreSQL (Cloud Managed)
- **Deployment Platform:** Render (Dockerized Web Service)
- **Authentication:** Firebase Auth + Laravel Sanctum
