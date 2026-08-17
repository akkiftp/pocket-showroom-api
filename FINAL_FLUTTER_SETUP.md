# Pocket Showroom Flutter – Final Setup

## Authentication
Only Firebase Email/Password + Google Sign-In are used. Phone OTP is disabled.

## Super Admin
Login with `akkiftp1@gmail.com`. The Laravel backend marks this account as admin and Flutter opens Super Admin Control Center directly.

## Added features
- Owner showroom analytics
- Super Admin global analytics + business drill-down
- Persisted customer directory
- Customer activity screen
- Working showroom QR generator
- Working theme preset save
- Working order persistence
- Share/WhatsApp/product activity tracking
- Smart Voice Assist in Add Product

## Smart Voice Assist
Uses the phone's speech recognition through `speech_to_text`; no paid AI API is required. It parses spoken Hindi/English-style product details into product name, category, price, offer price, description, stock and featured state. User must review before publishing.

Example:
`Product name Nokia 5G phone, category Mobile, price 12000, offer price 10999, description 8GB RAM 128GB storage, stock yes, featured yes.`

## Commands
```bash
flutter clean
flutter pub get
flutter run
```

Android needs microphone permission (already added). iOS includes speech-recognition description; existing microphone permission remains.

## Firebase
Keep these providers enabled:
- Email/Password
- Google

Keep Phone disabled.
