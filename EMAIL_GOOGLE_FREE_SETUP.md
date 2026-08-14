# Pocket Showroom Laravel API - Firebase Email + Google Authentication

This API accepts Firebase Email/Password and Google identities and exchanges them for Laravel Sanctum tokens.
There are no active phone OTP request/verify routes in this build.

## Required `.env`

```env
FIREBASE_PROJECT_ID=pocket-showroom-307ef
POCKET_SHOWROOM_FREE_MODE=true
POCKET_SHOWROOM_MASTER_ADMIN_EMAIL=
```

Optional admin example:

```env
POCKET_SHOWROOM_MASTER_ADMIN_EMAIL=owner@example.com
```

## Deploy/update commands

```bash
composer install --no-dev --optimize-autoloader
php artisan migrate --force
php artisan optimize:clear
php artisan config:cache
```

Ensure your normal database configuration is present in `.env` before running migrations.

## Public authentication endpoint

`POST /api/auth/firebase-login`

Body:

```json
{
  "firebase_token": "FIREBASE_ID_TOKEN",
  "name": "Optional display name"
}
```

Laravel verifies the Firebase JWT signature, issuer, audience, timestamps and subject. It only accepts Firebase sign-in providers `password` and `google.com`.

On first successful login it creates a user. On later logins it matches by Firebase UID, with email linking for an older account, then returns a Sanctum bearer token.

## App flow

- New account without a business -> `needs_business_setup: true`
- Existing account with business -> `needs_business_setup: false`
- Flutter uses this value to open Business Setup once or go directly to the dashboard.

## Free mode

When `POCKET_SHOWROOM_FREE_MODE=true`, signing in normalizes non-blocked users to active and clears trial/subscription expiry dates.
No payment gateway or SMS provider is required for this authentication flow.
