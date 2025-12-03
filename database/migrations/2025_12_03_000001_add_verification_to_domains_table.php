<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddVerificationToDomainsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up(): void
    {
        Schema::table('domains', function (Blueprint $table) {
            $table->boolean('is_primary')->default(false)->after('domain');
            $table->boolean('is_verified')->default(true)->after('is_primary'); // true for subdomains, false for custom
            $table->string('verification_token', 64)->nullable()->after('is_verified');
            $table->timestamp('verified_at')->nullable()->after('verification_token');
            $table->string('type', 20)->default('subdomain')->after('verified_at'); // 'subdomain' or 'custom'
            
            // Add index for faster lookups
            $table->index(['is_verified', 'type']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(): void
    {
        Schema::table('domains', function (Blueprint $table) {
            $table->dropIndex(['is_verified', 'type']);
            $table->dropColumn([
                'is_primary',
                'is_verified',
                'verification_token',
                'verified_at',
                'type',
            ]);
        });
    }
}
