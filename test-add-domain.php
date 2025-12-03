#!/usr/bin/env php
<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\Tenant;
use App\Services\DomainService;

echo "=== Test Adding Custom Domain ===\n\n";

// Get first tenant
$tenant = Tenant::first();

if (!$tenant) {
    echo "❌ No tenants found!\n";
    exit(1);
}

echo "Testing with Tenant ID: {$tenant->id}\n\n";

// Test domain
$testDomain = 'example.com';

echo "Attempting to add domain: {$testDomain}\n";

$domainService = new DomainService();

$result = $domainService->addCustomDomain($tenant, $testDomain, false);

if ($result['success']) {
    echo "✅ Domain added successfully!\n";
    echo "Domain ID: {$result['domain']->id}\n";
    echo "Verification Token: {$result['verification_token']}\n";
    echo "Type: {$result['domain']->type}\n";
    echo "Verified: " . ($result['domain']->is_verified ? 'Yes' : 'No') . "\n";
} else {
    echo "❌ Failed to add domain:\n";
    foreach ($result['errors'] as $error) {
        echo "  - {$error}\n";
    }
}

echo "\n=== Current Domains for Tenant ===\n";
foreach ($tenant->domains as $domain) {
    $type = $domain->type;
    $verified = $domain->is_verified ? 'VERIFIED' : 'UNVERIFIED';
    $primary = $domain->is_primary ? 'PRIMARY' : '';
    echo "- {$domain->domain} ({$type}) [{$verified}] {$primary}\n";
}

echo "\n=== Test Complete ===\n";
