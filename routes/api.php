<?php

use App\Http\Controllers\Api\WhatsAppWebhookController;
use App\Http\Middleware\VerifyTwilioSignature;
use Illuminate\Support\Facades\Route;

Route::post('/webhook/whatsapp', WhatsAppWebhookController::class)
    ->middleware(VerifyTwilioSignature::class);
