<?php

namespace App\Tenancy;

use App\Models\Domain;
use Stancl\Tenancy\Contracts\Tenant;
use Stancl\Tenancy\Resolvers\DomainTenantResolver;

class VerifiedDomainTenantResolver extends DomainTenantResolver
{
    /**
     * Resolve tenant by domain, ensuring the domain is verified
     */
    public function resolve(...$args): Tenant
    {
        $domain = $args[0];
        
        // Find domain that is verified
        $domainModel = Domain::where('domain', $domain)
            ->where(function ($query) {
                $query->where('is_verified', true)
                      ->orWhere('type', 'subdomain'); // Subdomains are auto-verified
            })
            ->first();

        if (!$domainModel) {
            // Check if domain exists but is not verified
            $unverifiedDomain = Domain::where('domain', $domain)
                ->where('is_verified', false)
                ->where('type', 'custom')
                ->first();
            
            if ($unverifiedDomain) {
                throw new \Exception("Domain {$domain} is not verified yet. Please verify DNS configuration.");
            }
            
            throw new \Exception("No tenant found for domain: {$domain}");
        }

        return $domainModel->tenant;
    }
}
