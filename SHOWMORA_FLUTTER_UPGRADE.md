# Showmora Flutter upgrade

## New behavior
- User-facing brand renamed to **Showmora**.
- Super Admin goes to the platform Control Center after login.
- Shop Owner goes to business setup or owner dashboard.
- Shop Admin goes directly to the assigned shop dashboard.
- Bottom navigation is built from permissions for Shop Admins.
- Owner Profile includes **Shop Admins** management.
- Product voice assistant now calls the server-side Showmora AI endpoint; local parser remains as fallback.

## Shop Admin workflow
1. Shop Owner opens Profile → Shop Admins → Add Admin.
2. Enter admin name and email and choose permissions.
3. Admin uses the same email in Firebase Email/Password or Google Sign-In.
4. Laravel links the Firebase UID to the existing Shop Admin account.
5. Shop Admin sees only allowed screens/actions and only that shop's data.

## Required commands

```bash
flutter clean
flutter pub get
flutter analyze
flutter build apk --release
```

The backend base URL currently keeps the existing production URL in `lib/services/api_service.dart`. Change it only if your API domain changes.
