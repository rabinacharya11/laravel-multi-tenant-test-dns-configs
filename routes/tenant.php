<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Route;
use Stancl\Tenancy\Middleware\InitializeTenancyByDomain;
use Stancl\Tenancy\Middleware\PreventAccessFromCentralDomains;

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
])->group(function () {
    Route::get('/', function () { 

    echo tenant('id'); 

  
        $tenantId = tenant('id');
        $users = \App\Models\User::all();  

        echo "Users count: " . $users->count() . "\n" . $users;
        
        return response()->json([
            'message' => 'Single Database Multi-Tenant Application',
            'tenant_id' => $tenantId,
            'users_count' => $users->count(),
            'users' => $users->map(function($user) {
                return [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'tenant_id' => $user->tenant_id,
                ];
            }),
            'note' => 'All data is stored in a single database with tenant_id scoping. Users are automatically filtered by tenant_id.',
        ], 200, [], JSON_PRETTY_PRINT);
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
});
