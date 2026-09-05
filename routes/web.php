<?php

use App\Http\Controllers\Admin\AdminBotServerController;
use App\Http\Controllers\Admin\AdminDashboardController;
use App\Http\Controllers\Admin\AdminDeviceController;
use App\Http\Controllers\Admin\AdminPlanController;
use App\Http\Controllers\Admin\AdminSettingController;
use App\Http\Controllers\Admin\AdminUserController;
use App\Http\Controllers\ApiKeyController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DeviceController;
use App\Http\Controllers\DocsController;
use App\Http\Controllers\DownloadController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\InternalEngineController;
use App\Http\Controllers\MessageController;
use App\Http\Controllers\MessageTemplateController;
use App\Http\Controllers\PlanController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\PublicPageController;
use App\Http\Controllers\SocialAuthController;
use App\Http\Controllers\WebhookController;
use App\Http\Middleware\AdminMiddleware;
use App\Http\Middleware\AntiBruteForce;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Public Landing & Informational Pages
|--------------------------------------------------------------------------
*/
Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/terms', [PublicPageController::class, 'terms'])->name('pages.terms');
Route::get('/privacy', [PublicPageController::class, 'privacy'])->name('pages.privacy');
Route::get('/faq', [PublicPageController::class, 'faq'])->name('pages.faq');
Route::get('/support', [PublicPageController::class, 'support'])->name('pages.support');
Route::post('/support', [PublicPageController::class, 'submitSupport'])->name('pages.support.submit');

/*
|--------------------------------------------------------------------------
| OAuth Social Login Routes (Public & User Accessible)
|--------------------------------------------------------------------------
*/
Route::get('/auth/google', [SocialAuthController::class, 'redirectToGoogle'])->name('auth.google');
Route::get('/auth/google/callback', [SocialAuthController::class, 'handleGoogleCallback'])->name('auth.google.callback');
Route::get('/auth/github', [SocialAuthController::class, 'redirectToGithub'])->name('auth.github');
Route::get('/auth/github/callback', [SocialAuthController::class, 'handleGithubCallback'])->name('auth.github.callback');

/*
|--------------------------------------------------------------------------
| Guest Authentication Routes
|--------------------------------------------------------------------------
*/
Route::middleware('guest')->group(function () {
    // Gerbang verifikasi Turnstile sebelum akses halaman auth
    Route::get('/auth/verify', [AuthController::class, 'showVerify'])->name('auth.verify');
    Route::post('/auth/verify', [AuthController::class, 'verifyGate'])
        ->middleware(AntiBruteForce::class.':10,1')
        ->name('auth.verify.submit');

    Route::get('/auth/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/auth/login', [AuthController::class, 'login'])
        ->middleware(AntiBruteForce::class.':5,1')
        ->name('login.submit');

    Route::get('/auth/register', [AuthController::class, 'showRegister'])->name('register');
    Route::post('/auth/register', [AuthController::class, 'register'])
        ->middleware(AntiBruteForce::class.':5,1')
        ->name('register.submit');

    Route::get('/auth/register/verify-otp', [AuthController::class, 'showVerifyOtp'])->name('register.otp');
    Route::post('/auth/register/verify-otp', [AuthController::class, 'verifyOtp'])
        ->middleware(AntiBruteForce::class.':10,1')
        ->name('register.verify-otp');
    Route::post('/auth/register/resend-otp', [AuthController::class, 'resendOtp'])
        ->middleware(AntiBruteForce::class.':3,1')
        ->name('register.resend-otp');

    Route::get('/auth/forgot-password', [AuthController::class, 'showForgotPassword'])->name('password.request');
    Route::post('/auth/forgot-password', [AuthController::class, 'sendResetLink'])
        ->middleware(AntiBruteForce::class.':3,1')
        ->name('password.email');
});

Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth')->name('logout');

/*
|--------------------------------------------------------------------------
| Internal Engine Webhook Callbacks (Node.js -> Laravel)
|--------------------------------------------------------------------------
*/
Route::post('/api/internal/wa-event', [InternalEngineController::class, 'handleEvent'])->name('internal.wa-event');

/*
|--------------------------------------------------------------------------
| Protected User Dashboard Routes
|--------------------------------------------------------------------------
*/
Route::prefix('users')->middleware('auth')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Plans & Billing
    Route::get('/plans', [PlanController::class, 'index'])->name('plans.index');

    // Devices Management
    Route::get('/devices', [DeviceController::class, 'index'])->name('devices.index');
    Route::post('/devices', [DeviceController::class, 'store'])->name('devices.store');
    Route::get('/devices/{device}', [DeviceController::class, 'show'])->name('devices.show');
    Route::put('/devices/{device}', [DeviceController::class, 'update'])->name('devices.update');
    Route::get('/devices/{device}/status', [DeviceController::class, 'status'])->name('devices.status');
    Route::post('/devices/{device}/stop', [DeviceController::class, 'stop'])->name('devices.stop');
    Route::post('/devices/{device}/start', [DeviceController::class, 'start'])->name('devices.start');
    Route::put('/devices/{device}/features', [DeviceController::class, 'updateFeatures'])->name('devices.features');
    Route::get('/devices/{device}/console-logs', [DeviceController::class, 'consoleLogs'])->name('devices.console-logs');
    Route::post('/devices/{device}/disconnect', [DeviceController::class, 'disconnect'])->name('devices.disconnect');
    Route::delete('/devices/{device}', [DeviceController::class, 'destroy'])->name('devices.destroy');

    // Messages & Logs
    Route::get('/messages', [MessageController::class, 'index'])->name('messages.index');
    Route::post('/messages/send', [MessageController::class, 'send'])->name('messages.send');

    // Message Templates Management
    Route::get('/templates', [MessageTemplateController::class, 'index'])->name('templates.index');
    Route::post('/templates', [MessageTemplateController::class, 'store'])->name('templates.store');
    Route::post('/templates/test-draft', [MessageTemplateController::class, 'testDraft'])->name('templates.test-draft');
    Route::put('/templates/{template}', [MessageTemplateController::class, 'update'])->name('templates.update');
    Route::delete('/templates/{template}', [MessageTemplateController::class, 'destroy'])->name('templates.destroy');
    Route::post('/templates/{template}/test', [MessageTemplateController::class, 'testSend'])->name('templates.test');

    // API Keys & Integrasi
    Route::get('/api-keys', [ApiKeyController::class, 'index'])->name('api-keys.index');
    Route::post('/api-keys', [ApiKeyController::class, 'store'])->name('api-keys.store');
    Route::delete('/api-keys/{apiKey}', [ApiKeyController::class, 'destroy'])->name('api-keys.destroy');

    // Interactive API Documentation
    Route::get('/docs', [DocsController::class, 'index'])->name('docs.index');

    // WHMCS Module Download (ZIP generated on-the-fly)
    Route::get('/download/wagateway-whmcs-module.zip', [DownloadController::class, 'whmcsModule'])->name('download.whmcs-module');

    // Profile & Account Settings
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::put('/profile/information', [ProfileController::class, 'updateInformation'])->name('profile.update-information');
    Route::put('/profile/avatar', [ProfileController::class, 'updateAvatar'])->name('profile.update-avatar');
    Route::put('/profile/password', [ProfileController::class, 'updatePassword'])->name('profile.update-password');
    Route::post('/profile/social/unlink/{provider}', [ProfileController::class, 'unlinkSocial'])->name('profile.unlink-social');

    // Webhook Settings
    Route::get('/webhooks', function () {
        return redirect()->route('api-keys.index', ['tab' => 'webhooks']);
    })->name('webhooks.index');
    Route::post('/webhooks', [WebhookController::class, 'store'])->name('webhooks.store');
    Route::post('/webhooks/test', [WebhookController::class, 'test'])->name('webhooks.test');
});

/*
|--------------------------------------------------------------------------
| Protected Admin Panel Routes (Role: admin)
|--------------------------------------------------------------------------
*/
Route::prefix('anjayadminwkwk')->middleware(['auth', AdminMiddleware::class])->name('admin.')->group(function () {
    Route::get('/', [AdminDashboardController::class, 'index'])->name('dashboard');

    // User Management
    Route::get('/users', [AdminUserController::class, 'index'])->name('users.index');
    Route::post('/users', [AdminUserController::class, 'store'])->name('users.store');
    Route::put('/users/{user}', [AdminUserController::class, 'update'])->name('users.update');
    Route::post('/users/{user}/toggle-status', [AdminUserController::class, 'toggleStatus'])->name('users.toggle-status');
    Route::delete('/users/{user}', [AdminUserController::class, 'destroy'])->name('users.destroy');

    // Global Devices Management
    Route::get('/devices', [AdminDeviceController::class, 'index'])->name('devices.index');
    Route::post('/devices', [AdminDeviceController::class, 'store'])->name('devices.store');
    Route::put('/devices/{device}', [AdminDeviceController::class, 'update'])->name('devices.update');
    Route::post('/devices/{device}/force-disconnect', [AdminDeviceController::class, 'forceDisconnect'])->name('devices.force-disconnect');
    Route::post('/devices/{device}/restart', [AdminDeviceController::class, 'restart'])->name('devices.restart');
    Route::delete('/devices/{device}', [AdminDeviceController::class, 'destroy'])->name('devices.destroy');

    // Bot Server / OTP Connection Configuration
    Route::get('/bot-server', [AdminBotServerController::class, 'index'])->name('bot-server.index');
    Route::post('/bot-server/assign', [AdminBotServerController::class, 'assign'])->name('bot-server.assign');
    Route::post('/bot-server/test-otp', [AdminBotServerController::class, 'testSendOtp'])->name('bot-server.test-otp');

    // Plans & Subscriptions Management
    Route::get('/plans', [AdminPlanController::class, 'index'])->name('plans.index');
    Route::post('/plans', [AdminPlanController::class, 'store'])->name('plans.store');
    Route::put('/plans/{plan}', [AdminPlanController::class, 'update'])->name('plans.update');
    Route::delete('/plans/{plan}', [AdminPlanController::class, 'destroy'])->name('plans.destroy');
    Route::post('/users/{user}/assign-plan', [AdminPlanController::class, 'assign'])->name('plans.assign');
    Route::post('/users/{user}/revoke-plan', [AdminPlanController::class, 'revoke'])->name('plans.revoke');

    // Website & System Settings
    Route::get('/settings', [AdminSettingController::class, 'index'])->name('settings.index');
    Route::post('/settings', [AdminSettingController::class, 'update'])->name('settings.update');
    Route::post('/settings/test-smtp', [AdminSettingController::class, 'testSmtp'])->name('settings.test-smtp');
});
