# Pocket Showroom - Complete Laravel API Backend

Target: Laravel 11/12 + PHP 8.2+ + MySQL + Sanctum

This backend matches the Flutter owner app flow:

Welcome -> Mobile Login -> OTP -> Business Setup -> Dashboard ->
Categories -> Products -> Enquiries -> Public Showroom -> Profile -> Logout

## 1. Create fresh Laravel project

```bash
composer create-project laravel/laravel pocket-showroom-api
cd pocket-showroom-api
php artisan install:api
```

`install:api` installs Laravel Sanctum and creates the API setup.

## 2. Merge this ZIP into Laravel root

Copy/replace:

- app/
- database/
- routes/api.php
- config/pocket_showroom.php

## 3. Configure `.env`

Example:

```env
APP_NAME="Pocket Showroom"
APP_ENV=local
APP_KEY=
APP_DEBUG=true
APP_URL=http://127.0.0.1:8000

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=pocket_showroom
DB_USERNAME=root
DB_PASSWORD=

FILESYSTEM_DISK=public

# Local testing: fixed OTP is allowed.
POCKET_SHOWROOM_FIXED_OTP=1234
POCKET_SHOWROOM_OTP_EXPIRES_MINUTES=10
```

Then:

```bash
php artisan key:generate
php artisan migrate
php artisan storage:link
php artisan db:seed --class=PocketShowroomSeeder
php artisan serve --host=0.0.0.0 --port=8000
```

## 4. Demo login

Request OTP:

```http
POST /api/auth/request-otp
Content-Type: application/json

{
  "phone": "9999999999"
}
```

Local response contains `debug_otp` when `APP_ENV=local`.

Verify:

```http
POST /api/auth/verify-otp
Content-Type: application/json

{
  "phone": "9999999999",
  "otp": "1234",
  "name": "Demo Owner"
}
```

Response gives Sanctum bearer token.

Use:

```http
Authorization: Bearer YOUR_TOKEN
Accept: application/json
```

## 5. Main API list

### Auth
- POST `/api/auth/request-otp`
- POST `/api/auth/verify-otp`
- GET `/api/me`
- POST `/api/auth/logout`

### Business
- GET `/api/business`
- POST `/api/business`
- POST `/api/business/logo`
- POST `/api/business/banner`
- POST `/api/business/theme`

### Dashboard
- GET `/api/dashboard`

### Categories
- GET `/api/categories`
- POST `/api/categories`
- PUT/PATCH `/api/categories/{category}`
- DELETE `/api/categories/{category}`

### Products
- GET `/api/products`
- POST `/api/products`
- GET `/api/products/{product}`
- POST `/api/products/{product}`  (multipart update; supports `_method=PUT`)
- PUT/PATCH `/api/products/{product}` (JSON update)
- DELETE `/api/products/{product}`
- POST `/api/products/{product}/toggle-stock`
- POST `/api/products/{product}/toggle-featured`
- DELETE `/api/products/{product}/images/{image}`

### Enquiries (owner)
- GET `/api/inquiries`
- GET `/api/inquiries/{inquiry}`
- POST `/api/inquiries/{inquiry}/handled`
- POST `/api/inquiries/{inquiry}/pending`
- DELETE `/api/inquiries/{inquiry}`

### Public customer showroom
- GET `/api/public/showrooms/{slug}`
- GET `/api/public/showrooms/{slug}/products`
- GET `/api/public/showrooms/{slug}/products/{product}`
- POST `/api/public/showrooms/{slug}/inquiries`

## 6. Multipart product create example

```http
POST /api/products
Authorization: Bearer TOKEN
Content-Type: multipart/form-data

name=Classic Diamond Ring
category_id=1
price=45999
offer_price=42999
description=Premium ring
in_stock=1
featured=1
images[]=<file>
images[]=<file>
```

## 7. Flutter base URL

Android emulator:

```dart
const baseUrl = 'http://10.0.2.2:8000/api';
```

Physical phone on same Wi-Fi:

```dart
const baseUrl = 'http://YOUR_PC_LOCAL_IP:8000/api';
```

For local HTTP Android testing set:

```xml
<application
    android:usesCleartextTraffic="true"
    ... >
```

## Important production notes

- The included OTP is intentionally suitable for development/testing.
- In production connect an SMS provider and do NOT return OTP in API responses.
- Use HTTPS.
- Set `APP_ENV=production`, `APP_DEBUG=false`.
- Add rate limiting/WAF rules appropriate to your deployment.
