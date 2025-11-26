<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\Tenant;

// The subdomain from your URL
$subdomain = 'zs8s0ocs8cwcwgckkw848g8k';
$fullDomain = 'zs8s0ocs8cwcwgckkw848g8k.209.50.228.254.sslip.io';

// Check if tenant already exists
$existingTenant = Tenant::where('id', $subdomain)->first();

if ($existingTenant) {
    echo "Tenant '{$subdomain}' already exists.\n";
    
    // Check if domain is associated
    $domain = $existingTenant->domains()->where('domain', $fullDomain)->first();
    
    if ($domain) {
        echo "Domain '{$fullDomain}' is already associated with this tenant.\n";
    } else {
        echo "Adding domain '{$fullDomain}' to tenant...\n";
        $existingTenant->domains()->create(['domain' => $fullDomain]);
        echo "Domain added successfully!\n";
    }
} else {
    echo "Creating tenant '{$subdomain}' (Single Database Mode)...\n";
    
    $tenant = Tenant::create([
        'id' => $subdomain,
    ]);
    
    echo "Tenant created successfully!\n";
    echo "Adding domain '{$fullDomain}'...\n";
    
    $tenant->domains()->create(['domain' => $fullDomain]);
    
    echo "Domain added successfully!\n";
    echo "Note: Using single database tenancy - all tenant data is stored in the same database with tenant_id scoping.\n";
}

echo "\nTenant setup complete!\n";
echo "You can now access your tenant at: http://{$fullDomain}\n";
