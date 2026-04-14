<?php

declare(strict_types=1);

use App\Domain\Notifications\Web\Controllers\TelegramWebhookController;
use Illuminate\Support\Facades\Route;

Route::post('/webhooks/telegram', [TelegramWebhookController::class, 'handle']);
