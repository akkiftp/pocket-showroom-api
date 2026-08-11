<?php

return [
    'fixed_otp' => env('POCKET_SHOWROOM_FIXED_OTP'),
    'otp_expires_minutes' => (int) env('POCKET_SHOWROOM_OTP_EXPIRES_MINUTES', 10),
];
