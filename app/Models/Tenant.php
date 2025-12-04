<?php

namespace App\Models;

use Stancl\Tenancy\Database\Models\Tenant as BaseTenant;
use Stancl\Tenancy\Database\Concerns\HasDomains;

class Tenant extends BaseTenant
{
    use HasDomains;

    public static function getCustomColumns(): array
    {
        return [
            'id',
        ];
    }

    /**
     * Get the primary domain for this tenant
     */
    public function primaryDomain()
    {
        return $this->domains()->where('is_primary', true)->first();
    }

    /**
     * Get all custom domains for this tenant
     */
    public function customDomains()
    {
        return $this->domains()->where('type', 'custom');
    }

    /**
     * Get verified custom domains
     */
    public function verifiedCustomDomains()
    {
        return $this->customDomains()->where('is_verified', true);
    }

    /**
     * Add a custom domain to this tenant
     */
    public function addCustomDomain(string $domain, bool $isPrimary = false): Domain
    {
        return $this->domains()->create([
            'domain' => $domain,
            'type' => 'custom',
            'is_primary' => $isPrimary,
            'is_verified' => false,
            'verification_token' => bin2hex(random_bytes(32)),
        ]);
    }

    /**
     * Add a subdomain to this tenant
     */
    public function addSubdomain(string $subdomain, string $baseDomain, bool $isPrimary = true): Domain
    {
        $fullDomain = $subdomain . '.' . $baseDomain;
        
        return $this->domains()->create([
            'domain' => $fullDomain,
            'type' => 'subdomain',
            'is_primary' => $isPrimary,
            'is_verified' => true,
            'verified_at' => now(),
        ]);
    }

    /**
     * Find tenant by domain (subdomain or custom)
     */
    public static function findByDomain(string $domain): ?self
    {
        $domainModel = Domain::where('domain', $domain)
            ->where('is_verified', true)
            ->first();

        return $domainModel ? $domainModel->tenant : null;
    }
}