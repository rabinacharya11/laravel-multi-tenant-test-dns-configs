<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Route;
use Stancl\Tenancy\Middleware\InitializeTenancyByDomain;
use Stancl\Tenancy\Middleware\PreventAccessFromCentralDomains;
use App\Http\Middleware\EnsureDomainIsVerified;

/*
|--------------------------------------------------------------------------
| Tenant Routes
|--------------------------------------------------------------------------
|
| Here you can register the tenant routes for your application.
| These routes are loaded by the TenantRouteServiceProvider.
|
| Feel free to customize them however you want. Good luck!
|
*/

Route::middleware([
    'web',
    InitializeTenancyByDomain::class,
    PreventAccessFromCentralDomains::class,
    EnsureDomainIsVerified::class,
])->group(function () {
    // Dashboard UI
    Route::get('/', function () {
        return view('dashboard', [
            'tenantName' => tenant()->data['name'] ?? 'Tenant Dashboard'
        ]);
    });

    Route::get('/dashboard', function () {
        return view('dashboard', [
            'tenantName' => tenant()->data['name'] ?? 'Tenant Dashboard'
        ]);
    });
    
    Route::get('/users', function () {
        $users = \App\Models\User::all();
        
        return response()->json([
            'tenant_id' => tenant('id'),
            'users' => $users->map(function($user) {
                return [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'tenant_id' => $user->tenant_id,
                    'created_at' => $user->created_at,
                ];
            }),
        ], 200, [], JSON_PRETTY_PRINT);
    });
    
    Route::get('/stats', function () {
        return response()->json([
            'tenant_id' => tenant('id'),
            'tenant_domain' => request()->getHost(),
            'users_count' => \App\Models\User::count(),
            'database_connection' => config('database.default'),
            'tenancy_initialized' => tenancy()->initialized,
        ], 200, [], JSON_PRETTY_PRINT);
    });

    // Domain Management Routes
    Route::prefix('api/domains')->group(function () {
        Route::get('/', [\App\Http\Controllers\DomainController::class, 'index']);
        Route::post('/', [\App\Http\Controllers\DomainController::class, 'store']);
        Route::post('/{domainId}/verify', [\App\Http\Controllers\DomainController::class, 'verify']);
        Route::get('/{domainId}/instructions', [\App\Http\Controllers\DomainController::class, 'instructions']);
        Route::post('/{domainId}/set-primary', [\App\Http\Controllers\DomainController::class, 'setPrimary']);
        Route::delete('/{domainId}', [\App\Http\Controllers\DomainController::class, 'destroy']);
    });
});
