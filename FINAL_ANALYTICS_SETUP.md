# Pocket Showroom – Analytics / Admin / Tracking Setup

## Super Admin
The configured master admin email is `akkiftp1@gmail.com`.
Recommended production environment variables:

```env
FIREBASE_PROJECT_ID=pocket-showroom-307ef
POCKET_SHOWROOM_FREE_MODE=true
POCKET_SHOWROOM_MASTER_ADMIN_EMAIL=akkiftp1@gmail.com
```

Login once with that exact Firebase email. Laravel will promote that account to `is_admin=true` and Flutter routes it directly to the Super Admin Control Center.

## New database tables
Run after deployment:

```bash
php artisan migrate --force
php artisan optimize:clear
php artisan config:cache
```

This creates persisted customer contacts, anonymous/identified visitor sessions, activity events, orders and order items.

## What is tracked
- showroom view
- product view
- share / owner share
- favorite intent
- add to cart
- buy / order intent
- WhatsApp button click
- inquiry
- order
- QR share/open action

`visitor_token` is anonymous and stored in the visitor browser/app. IP is stored only as a one-way hash.

Important: WhatsApp metrics are **click/open intent**, not proof that a message was delivered or replied to. Actual WhatsApp message status/replies require the WhatsApp Business API + webhooks.

## Owner analytics
Authenticated endpoints:
- `GET /api/analytics`
- `GET /api/analytics/customers`
- `GET /api/analytics/products`
- `GET /api/customers`
- `GET /api/orders`

## Super Admin analytics
Admin-only endpoints:
- `GET /api/admin/overview`
- `GET /api/admin/users`
- `GET /api/admin/businesses/{business}/analytics`

## Public tracking
- `POST /api/public/showrooms/{slug}/events`
- `POST /api/public/showrooms/{slug}/orders`

The public web showroom automatically creates a local anonymous visitor token and sends tracking events.
