<?php

return [
    'account_sid' => env('TWILIO_SID'),
    'auth_token' => env('TWILIO_AUTH_TOKEN'),
    'from_number' => env('TWILIO_WHATSAPP_FROM', 'whatsapp:+14155238886'),
];
