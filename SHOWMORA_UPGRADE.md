# Showmora – upgraded architecture

This build upgrades the original Pocket Showroom project into **Showmora**, an AI-assisted digital showroom platform.

## Role logic

### Super Admin
- Not attached to any shop/business.
- Sees platform-wide owners, businesses, products, visitors, views, shares, WhatsApp clicks, inquiries and orders.
- Can activate, extend trial or block a shop owner.
- Can inspect any business analytics.
- Super Admin identity is controlled by `POCKET_SHOWROOM_MASTER_ADMIN_EMAIL` in server `.env`.

### Shop Owner
- Owns exactly one `businesses` row through `businesses.user_id`.
- Full control of their own business.
- Can create/delete Shop Admin accounts and choose permissions for each admin.
- Can manage products, categories, orders, inquiries, customers, analytics, branding and showroom sharing.

### Shop Admin
- Assigned to one shop through `users.business_id`.
- Does **not** own the business.
- Access is permission-based using `users.permissions` JSON.
- Can never access another shop because every owner endpoint resolves business through `BusinessContext`.
- To sign in, create the Shop Admin in the Owner app with an email, then the admin signs up/signs in to Firebase using the exact same email. The Laravel login links that Firebase identity to the pre-created Shop Admin row.

## AI setup

The OpenAI key stays only on Laravel/server. Never put it in Flutter.

```env
OPENAI_API_KEY=
OPENAI_MODEL=gpt-5-mini
```

`POST /api/ai/product-draft` accepts:

```json
{
  "instruction": "22 carat ladies gold ring, 5.8 gram, price 45000, in stock"
}
```

The Flutter Product form now sends voice text to this endpoint. If the AI endpoint is unavailable, it automatically falls back to the existing offline parser.

## New API endpoints

- `GET /api/staff`
- `POST /api/staff`
- `PATCH /api/staff/{id}`
- `DELETE /api/staff/{id}`
- `POST /api/ai/product-draft`
- `PATCH /api/business`

Existing products, categories, analytics, inquiries, orders, customers, public showroom and tracking endpoints remain available.

## Deployment after replacing backend

```bash
php artisan optimize:clear
php artisan migrate --force
php artisan storage:link
php artisan route:clear
php artisan config:cache
```

Set the following on production:

```env
APP_NAME=Showmora
APP_URL=https://your-api-domain.com
POCKET_SHOWROOM_MASTER_ADMIN_EMAIL=YOUR_SUPER_ADMIN_EMAIL
FIREBASE_PROJECT_ID=YOUR_FIREBASE_PROJECT_ID
OPENAI_API_KEY=YOUR_KEY
OPENAI_MODEL=gpt-5-mini
```

Do not put the OpenAI API key inside the Flutter project.

## Verification performed in this workspace

- All PHP controllers, middleware, models, services and the new migration passed `php -l` syntax checks.
- `php artisan route:list --path=api` successfully loaded all API routes (55 routes).
- Full database migration test could not be executed in this workspace because the PHP runtime does not include the SQLite PDO driver.
- PHPUnit could not run because the runtime is missing `dom`, `mbstring`, and `xmlwriter` extensions.
- Flutter/Dart SDK is not installed in this workspace, so a real `flutter analyze` / APK build could not be run here. Run `flutter pub get && flutter analyze && flutter build apk` on your Flutter machine/CI after replacing the source.
