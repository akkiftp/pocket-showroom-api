<?php

return [
    // Authentication is intentionally limited to Firebase Email/Password + Google.
    // Phone OTP/SMS is disabled in this build.
    'auth_driver' => 'firebase_email_google',
    'firebase_project_id' => env('FIREBASE_PROJECT_ID', 'pocket-showroom-307ef'),

    // Optional: make one configured email a backend admin without relying on a phone number.
    'master_admin_email' => env('POCKET_SHOWROOM_MASTER_ADMIN_EMAIL', ''),

    // Keep the app fully usable without paid subscription while the product is being tested.
    'free_mode' => filter_var(env('POCKET_SHOWROOM_FREE_MODE', true), FILTER_VALIDATE_BOOL),
];
