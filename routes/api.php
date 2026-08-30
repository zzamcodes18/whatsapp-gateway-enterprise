<?php

use App\Http\Controllers\Api\V1\GatewayApiController;
use App\Http\Middleware\VerifyApiKey;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Public WhatsApp Gateway API v1
|--------------------------------------------------------------------------
*/
Route::prefix('v1')->middleware(VerifyApiKey::class)->group(function () {
    Route::get('/devices', [GatewayApiController::class, 'getDevices']);
    Route::post('/messages/send-text', [GatewayApiController::class, 'sendTextMessage']);
    Route::post('/messages/send-media', [GatewayApiController::class, 'sendMediaMessage']);
    Route::post('/messages/send-button', [GatewayApiController::class, 'sendButtonMessage']);
    Route::post('/messages/send-interactive', [GatewayApiController::class, 'sendButtonMessage']);
});
