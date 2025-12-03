#!/bin/bash

# Multi-Tenant Application Setup Script
# This script sets up the database and creates initial tenant data

set -e

echo "🚀 Multi-Tenant Application Setup"
echo "=================================="
echo ""

# Check if .env exists
if [ ! -f .env ]; then
    echo "❌ Error: .env file not found!"
    echo "Please copy .env.example to .env and configure it first."
    exit 1
fi

# Run migrations
echo "📦 Running database migrations..."
php artisan migrate --force

echo "✅ Migrations completed!"
echo ""

# Ask if user wants to create a sample tenant
read -p "Would you like to create a sample tenant? (y/n) " -n 1 -r
echo ""

if [[ $REPLY =~ ^[Yy]$ ]]; then
    echo ""
    echo "Creating sample tenant..."
    
    # Run tenant creation via PHP
    php artisan tinker --execute="
        \$tenant = App\Models\Tenant::create(['id' => Illuminate\Support\Str::uuid()->toString()]);
        \$tenant->addSubdomain('demo', config('app.domain', 'petmelo.com'), true);
        \$tenant->update(['data' => ['name' => 'Demo Tenant']]);
        
        // Create sample users
        \$user1 = App\Models\User::create([
            'tenant_id' => \$tenant->id,
            'name' => 'John Doe',
            'email' => 'john@demo.com',
            'password' => bcrypt('password'),
        ]);
        
        \$user2 = App\Models\User::create([
            'tenant_id' => \$tenant->id,
            'name' => 'Jane Smith',
            'email' => 'jane@demo.com',
            'password' => bcrypt('password'),
        ]);
        
        echo '✅ Sample tenant created!';
        echo PHP_EOL;
        echo 'Tenant ID: ' . \$tenant->id;
        echo PHP_EOL;
        echo 'Subdomain: demo.' . config('app.domain', 'petmelo.com');
        echo PHP_EOL;
        echo 'Users created: 2';
        echo PHP_EOL;
    "
fi

echo ""
echo "✅ Setup complete!"
echo ""
echo "Next steps:"
echo "1. Configure your DNS (see MULTI_TENANT_README.md)"
echo "2. Set up SSL with Caddy or Certbot (see SSL_SETUP.md)"
echo "3. Access admin panel at: http://localhost/admin"
echo "4. Access demo tenant at: http://demo.localhost (or your configured domain)"
echo ""
echo "For production deployment, make sure to:"
echo "- Update APP_ENV=production in .env"
echo "- Set APP_DOMAIN to your actual domain"
echo "- Configure SSL certificates"
echo "- Set up proper DNS records"
echo ""
