# SHOWMORA API Final Concept Build

Added the marketplace layer while keeping the existing Pocket Showroom system intact.

## Architecture
Customer discovery -> category/location shop directory -> existing Pocket Showroom -> product -> enquiry/order.

Roles remain: super_admin, shop_owner, shop_admin. Customers can browse publicly without an account.

## New marketplace database
- marketplace_categories
- marketplace_locations
- businesses marketplace category/location, locality/pincode/GPS, timings, delivery, order acceptance, verification, featured and view counters.

## New public APIs
- GET `/api/marketplace/home`
- GET `/api/marketplace/categories`
- GET `/api/marketplace/locations`
- GET `/api/marketplace/shops`
- GET `/api/marketplace/shops/{slug}`
- GET `/api/marketplace/search?q=`

## Super Admin marketplace APIs
CRUD category/location APIs and shop marketplace verification/featured/category/location control are under `/api/admin/marketplace/...`.

## Deploy
```bash
php artisan optimize:clear
php artisan migrate --force
php artisan db:seed --class=ShowmoraMarketplaceSeeder --force
php artisan storage:link
php artisan config:cache
```

For OpenAI:
```env
OPENAI_API_KEY=YOUR_SECRET_KEY
OPENAI_MODEL=gpt-5-mini
```
Never put the secret key in Flutter.

## Verification performed
- PHP syntax lint passed for app/routes/migrations/seeders.
- Laravel loaded 70 API routes.
- Marketplace, shop-admin and AI routes were confirmed.
- Database migration execution could not be run in this packaging environment because the PHP SQLite driver is not installed. Run the deployment commands on your Laravel server before production.
