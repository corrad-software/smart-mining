<?php

use App\Http\Controllers\ApiController;
use Illuminate\Support\Facades\Route;

Route::post('/login', [ApiController::class, 'login']);

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/me', [ApiController::class, 'me']);
    Route::post('/logout', [ApiController::class, 'logout']);

    Route::get('/dashboard', [ApiController::class, 'dashboard']);
    Route::get('/sales', [ApiController::class, 'sales']);
    Route::get('/sales/daily', [ApiController::class, 'salesDaily']);
    Route::post('/sync', [ApiController::class, 'sync']);
    Route::get('/sites', [ApiController::class, 'sites']);
    Route::get('/sites/{id}', [ApiController::class, 'site']);
    Route::get('/sites/{id}/media', [ApiController::class, 'siteMedia']);
    Route::get('/regions', [ApiController::class, 'regions']);
    Route::get('/contractors', [ApiController::class, 'contractors']);
    Route::get('/employees', [ApiController::class, 'employees']);
    Route::get('/cameras', [ApiController::class, 'cameras']);
    Route::get('/reports', [ApiController::class, 'reports']);
    Route::get('/monitoring-reports', [ApiController::class, 'monitoringReports']);
    Route::get('/roles', [ApiController::class, 'roles']);
    Route::get('/users', [ApiController::class, 'users']);
    Route::get('/attendances', [ApiController::class, 'attendances']);
    Route::get('/attendances/monthly', [ApiController::class, 'attendanceMonthly']);
    Route::get('/attendances/grid', [ApiController::class, 'attendanceGrid']);
    Route::get('/weekly-reports', [ApiController::class, 'weeklyReports']);
    Route::get('/weekly-reports/{id}', [ApiController::class, 'weeklyReport']);
    Route::post('/weekly-reports/{id}/transition', [ApiController::class, 'transition']);
});
