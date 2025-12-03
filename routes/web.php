<?php

use Illuminate\Support\Facades\Route;

foreach (config('tenancy.central_domains') as $domain) {
    Route::domain($domain)->group(function () {
        // Central domain routes
        Route::get('/', function () {
            return view('admin');
        });

        Route::get('/admin', function () {
            return view('admin');
        });

        // Tenant management API (for central app)
        Route::prefix('api/tenants')->group(function () {
            Route::get('/', [\App\Http\Controllers\TenantController::class, 'index']);
            Route::post('/', [\App\Http\Controllers\TenantController::class, 'store']);
            Route::get('/{tenantId}', [\App\Http\Controllers\TenantController::class, 'show']);
        });
    });
}


