#!/usr/bin/env php
<?php

// Test script to verify tenant and domain setup

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\Tenant;
use App\Models\Domain;
use Illuminate\Support\Str;

echo "=== Multi-Tenant Domain Setup Test ===\n\n";

// Check if tenants exist
$tenantCount = Tenant::count();
echo "Total tenants: {$tenantCount}\n";

if ($tenantCount === 0) {
    echo "\n❌ No tenants found. Creating a test tenant...\n";
    
    $baseDomain = config('app.domain', 'petmelo.com');
    
    // Create tenant
    $tenant = Tenant::create([
        'id' => Str::uuid()->toString(),
        'data' => ['name' => 'Test Tenant'],
    ]);
    
    // Add subdomain
    $subdomain = $tenant->addSubdomain('test', $baseDomain, true);
    
    echo "✅ Created tenant: {$tenant->id}\n";
    echo "✅ Created subdomain: {$subdomain->domain}\n\n";
}

// List all tenants and their domains
echo "\n=== Tenant Details ===\n";
$tenants = Tenant::with('domains')->get();

foreach ($tenants as $tenant) {
    $name = $tenant->data['name'] ?? 'Unnamed';
    echo "\nTenant: {$name} (ID: {$tenant->id})\n";
    echo "Domains:\n";
    
    foreach ($tenant->domains as $domain) {
        $primary = $domain->is_primary ? ' [PRIMARY]' : '';
        $verified = $domain->is_verified ? ' [VERIFIED]' : ' [UNVERIFIED]';
        $type = strtoupper($domain->type);
        echo "  - {$domain->domain} ({$type}){$primary}{$verified}\n";
    }
    
    if ($tenant->domains->count() === 0) {
        echo "  ⚠️  No domains found for this tenant!\n";
    }
}

echo "\n=== Domain Statistics ===\n";
$totalDomains = Domain::count();
$subdomains = Domain::where('type', 'subdomain')->count();
$customDomains = Domain::where('type', 'custom')->count();
$verifiedDomains = Domain::where('is_verified', true)->count();

echo "Total domains: {$totalDomains}\n";
echo "Subdomains: {$subdomains}\n";
echo "Custom domains: {$customDomains}\n";
echo "Verified domains: {$verifiedDomains}\n";

echo "\n=== Test Complete ===\n";
