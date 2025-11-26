<?php

namespace Database\Seeders;

use App\Models\Tenant;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class TenantSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $baseDomain = env('TENANT_DOMAIN', 'localhost');
        
        // Create first tenant with domain
        $tenant1 = Tenant::firstOrCreate([
            'id' => 'tenant1',
        ]);
        
        $tenant1->domains()->firstOrCreate([
            'domain' => "tenant1.{$baseDomain}",
        ]);
        
        // Create users for tenant1
        User::firstOrCreate(
            ['tenant_id' => 'tenant1', 'email' => 'john@tenant1.com'],
            [
                'name' => 'John Doe',
                'password' => Hash::make('password'),
            ]
        );
        
        User::firstOrCreate(
            ['tenant_id' => 'tenant1', 'email' => 'jane@tenant1.com'],
            [
                'name' => 'Jane Smith',
                'password' => Hash::make('password'),
            ]
        );
        
        User::firstOrCreate(
            ['tenant_id' => 'tenant1', 'email' => 'bob@tenant1.com'],
            [
                'name' => 'Bob Wilson',
                'password' => Hash::make('password'),
            ]
        );
        
        // Create second tenant with domain
        $tenant2 = Tenant::firstOrCreate([
            'id' => 'tenant2',
        ]);
        
        $tenant2->domains()->firstOrCreate([
            'domain' => "tenant2.{$baseDomain}",
        ]);
        
        // Create users for tenant2
        User::firstOrCreate(
            ['tenant_id' => 'tenant2', 'email' => 'alice@tenant2.com'],
            [
                'name' => 'Alice Johnson',
                'password' => Hash::make('password'),
            ]
        );
        
        User::firstOrCreate(
            ['tenant_id' => 'tenant2', 'email' => 'charlie@tenant2.com'],
            [
                'name' => 'Charlie Brown',
                'password' => Hash::make('password'),
            ]
        );
        
        // Create existing tenant
        $tenant3 = Tenant::firstOrCreate([
            'id' => 'zs8s0ocs8cwcwgckkw848g8k',
        ]);
        
        $tenant3->domains()->firstOrCreate([
            'domain' => "zs8s0ocs8cwcwgckkw848g8k.{$baseDomain}",
        ]);
        
        // Create users for tenant3
        User::firstOrCreate(
            ['tenant_id' => 'zs8s0ocs8cwcwgckkw848g8k', 'email' => 'admin@tenant3.com'],
            [
                'name' => 'Admin User',
                'password' => Hash::make('password'),
            ]
        );
        
        User::firstOrCreate(
            ['tenant_id' => 'zs8s0ocs8cwcwgckkw848g8k', 'email' => 'demo@tenant3.com'],
            [
                'name' => 'Demo User',
                'password' => Hash::make('password'),
            ]
        );
        
        User::firstOrCreate(
            ['tenant_id' => 'zs8s0ocs8cwcwgckkw848g8k', 'email' => 'test@tenant3.com'],
            [
                'name' => 'Test User',
                'password' => Hash::make('password'),
            ]
        );
        
        User::firstOrCreate(
            ['tenant_id' => 'zs8s0ocs8cwcwgckkw848g8k', 'email' => 'guest@tenant3.com'],
            [
                'name' => 'Guest User',
                'password' => Hash::make('password'),
            ]
        );
    }
}
