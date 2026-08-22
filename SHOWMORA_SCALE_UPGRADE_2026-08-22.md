# Showmora Scale Upgrade

## Added
- Multi-business membership foundation (`business_members`)
- Multiple marketplace categories per business
- Business modes: product/service/travel/product_service
- Home services + public booking + owner booking management
- Local reels feed + product/service linkage + view tracking
- Travel vehicles/routes + public booking requests + owner management
- Cloudinary-first business logo/banner storage fallback to public disk
- Removed unsafe public `/api/system/migrate` endpoint

## Deploy
1. Configure production `.env` from `.env.example`; do not upload a real `.env` to source control.
2. Set `CLOUDINARY_URL` for durable media.
3. Run `composer install --no-dev --optimize-autoloader`.
4. Run `php artisan migrate --force` from deployment/CLI.
5. Run `php artisan config:cache && php artisan route:cache`.
6. For fallback local storage only: `php artisan storage:link`.

## Scale next
Use Redis for cache/queues and Laravel Scout + Meilisearch/OpenSearch when catalog/search volume grows substantially.
