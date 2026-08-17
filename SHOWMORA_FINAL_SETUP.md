# SHOWMORA Final Backend

Architecture: Super Admin -> Shop Owner -> Shop Admin, plus public customer marketplace.

Run after deployment:

```bash
php artisan optimize:clear
php artisan migrate --force
php artisan db:seed --class=ShowmoraMarketplaceSeeder --force
php artisan storage:link
php artisan config:cache
```

OpenAI stays server-side only:

```env
OPENAI_API_KEY=
OPENAI_MODEL=gpt-5-mini
```

Public customer APIs are under `/api/marketplace/*`. Existing `/showrooms/{slug}` and `/api/public/showrooms/{slug}` remain compatible.
