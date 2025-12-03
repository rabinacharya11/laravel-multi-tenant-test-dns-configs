<?php

namespace App\Models;

use Stancl\Tenancy\Database\Models\Domain as BaseDomain;

class Domain extends BaseDomain
{
    protected $fillable = [
        'domain',
        'tenant_id',
        'is_primary',
        'is_verified',
        'verification_token',
        'verified_at',
        'type',
    ];

    protected $casts = [
        'is_primary' => 'boolean',
        'is_verified' => 'boolean',
        'verified_at' => 'datetime',
    ];

    /**
     * Scope to get only verified domains
     */
    public function scopeVerified($query)
    {
        return $query->where('is_verified', true);
    }

    /**
     * Scope to get only custom domains
     */
    public function scopeCustom($query)
    {
        return $query->where('type', 'custom');
    }

    /**
     * Scope to get only subdomain domains
     */
    public function scopeSubdomain($query)
    {
        return $query->where('type', 'subdomain');
    }

    /**
     * Check if domain is verified
     */
    public function isVerified(): bool
    {
        return $this->is_verified === true;
    }

    /**
     * Check if domain is custom
     */
    public function isCustom(): bool
    {
        return $this->type === 'custom';
    }

    /**
     * Generate verification token
     */
    public function generateVerificationToken(): string
    {
        $this->verification_token = bin2hex(random_bytes(32));
        $this->save();
        
        return $this->verification_token;
    }

    /**
     * Mark domain as verified
     */
    public function markAsVerified(): void
    {
        $this->update([
            'is_verified' => true,
            'verified_at' => now(),
        ]);
    }
}
