# SHOWMORA Final Marketplace Architecture

One Flutter app supports public customers, Shop Owners, Shop Admins and Super Admin. Public customers do not need login. Existing Pocket Showroom URLs continue to work.

## Public customer flow
Marketplace Home -> Location -> Category -> Shops -> Shop Pocket Showroom -> Product -> WhatsApp/Enquiry/Order.

Public API:
- GET /api/marketplace/home
- GET /api/marketplace/categories
- GET /api/marketplace/locations
- GET /api/marketplace/shops
- GET /api/marketplace/shops/{slug}
- GET /api/marketplace/search?q=...
- Existing /api/public/showrooms/{slug} endpoints remain unchanged.

## Roles
- super_admin: entire platform, marketplace categories, locations, shops, owners, plans and analytics.
- shop_owner: owns one showroom and has full permissions for it.
- shop_admin: belongs to one showroom and receives explicit permissions from the owner.
- customer: public browse mode; no account required for discovery and WhatsApp enquiry.

## Setup
Run:
php artisan optimize:clear
php artisan migrate
php artisan db:seed --class=ShowmoraMarketplaceSeeder
php artisan storage:link

For AI add to .env:
OPENAI_API_KEY=your_secret_key
OPENAI_MODEL=gpt-5-mini

Never place OPENAI_API_KEY in Flutter.
