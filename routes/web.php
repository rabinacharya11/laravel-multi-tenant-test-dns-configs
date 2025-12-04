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

        // Caddy On-Demand TLS Check
        Route::get('/caddy-check', function () {
            $domain = request()->query('domain');
            
            // Optional: Log the check for debugging
            // \Illuminate\Support\Facades\Log::info("Caddy checking domain: {$domain}");

            // Allow central domains
            if (in_array($domain, config('tenancy.central_domains'))) {
                return response('OK', 200);
            }

            // Allow tenant domains (subdomains and custom domains)
            // This maps the domain to the tenant in the database
            if (\App\Models\Domain::where('domain', $domain)->exists()) {
                return response('OK', 200);
            }

            return response('Domain not found', 404);
        });
    });
}


