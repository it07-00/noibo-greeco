<?php

declare(strict_types=1);

use App\Http\Controllers\Api\DutyScheduleApiController;
use Illuminate\Support\Facades\Route;

Route::get('/duty-schedules', [DutyScheduleApiController::class, 'index']);
Route::get('/users', [DutyScheduleApiController::class, 'users']);
