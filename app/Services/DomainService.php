<?php

namespace App\Services;

use App\Models\Domain;
use App\Models\Tenant;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class DomainService
{
    /**
     * Validate domain format
     */
    public function validateDomain(string $domain): array
    {
        $validator = Validator::make(
            ['domain' => $domain],
            [
                'domain' => [
                    'required',
                    'string',
                    'regex:/^(?:[a-z0-9](?:[a-z0-9-]{0,61}[a-z0-9])?\.)+[a-z0-9][a-z0-9-]{0,61}[a-z0-9]$/i',
                    'max:255',
                ]
            ],
            [
                'domain.regex' => 'Invalid domain format. Please enter a valid domain (e.g., example.com or www.example.com)',
            ]
        );

        if ($validator->fails()) {
            return [
                'valid' => false,
                'errors' => $validator->errors()->all(),
            ];
        }

        // Check if domain is not a reserved/central domain
        $centralDomains = config('tenancy.central_domains', []);
        $baseDomain = config('app.domain', 'petmelo.com');
        
        if (in_array($domain, $centralDomains) || Str::endsWith($domain, '.' . $baseDomain)) {
            return [
                'valid' => false,
                'errors' => ['This domain is reserved and cannot be used as a custom domain.'],
            ];
        }

        // Check if domain already exists
        if (Domain::where('domain', $domain)->exists()) {
            return [
                'valid' => false,
                'errors' => ['This domain is already registered to another tenant.'],
            ];
        }

        return [
            'valid' => true,
            'errors' => [],
        ];
    }

    /**
     * Verify DNS is pointing correctly
     */
    public function verifyDNS(string $domain): array
    {
        $baseDomain = config('app.domain', 'petmelo.com');
        $expectedIP = config('app.server_ip');

        try {
            // 1. Check CNAME record (for subdomains)
            $cnameRecords = @dns_get_record($domain, DNS_CNAME);
            if (!empty($cnameRecords)) {
                foreach ($cnameRecords as $record) {
                    if (isset($record['target']) && Str::contains($record['target'], $baseDomain)) {
                        return [
                            'verified' => true,
                            'type' => 'CNAME',
                            'target' => $record['target'],
                        ];
                    }
                }
            }

            // 2. Check A record (for root domains or subdomains)
            // We use gethostbynamel as it's often more reliable for simple A record resolution
            $ips = @gethostbynamel($domain);
            
            if ($ips && $expectedIP) {
                if (in_array($expectedIP, $ips)) {
                    return [
                        'verified' => true,
                        'type' => 'A',
                        'ip' => $expectedIP,
                    ];
                }
            }

            // Fallback: If no expected IP is configured, we can't verify A records securely
            if (!$expectedIP) {
                 return [
                    'verified' => false,
                    'message' => 'Server IP not configured in application. Please contact support.',
                ];
            }

            return [
                'verified' => false,
                'message' => "DNS verification failed. \nExpected A Record: $expectedIP \nFound IPs: " . ($ips ? implode(', ', $ips) : 'None'),
            ];

        } catch (\Exception $e) {
            return [
                'verified' => false,
                'message' => 'Unable to verify DNS: ' . $e->getMessage(),
            ];
        }
    }

    /**
     * Add custom domain to tenant
     */
    public function addCustomDomain(Tenant $tenant, string $domain, bool $isPrimary = false): array
    {
        // Validate domain
        $validation = $this->validateDomain($domain);
        if (!$validation['valid']) {
            return [
                'success' => false,
                'errors' => $validation['errors'],
            ];
        }

        // Create domain record
        $domainModel = $tenant->addCustomDomain($domain, $isPrimary);

        return [
            'success' => true,
            'domain' => $domainModel,
            'verification_token' => $domainModel->verification_token,
            'message' => 'Custom domain added successfully. Please verify DNS configuration.',
        ];
    }

    /**
     * Verify and activate custom domain
     */
    public function verifyAndActivateDomain(Domain $domain): array
    {
        if ($domain->is_verified) {
            return [
                'success' => true,
                'message' => 'Domain is already verified.',
            ];
        }

        // Verify DNS
        $dnsCheck = $this->verifyDNS($domain->domain);

        if (!$dnsCheck['verified']) {
            return [
                'success' => false,
                'message' => $dnsCheck['message'],
            ];
        }

        // Mark as verified
        $domain->markAsVerified();

        return [
            'success' => true,
            'message' => 'Domain verified and activated successfully!',
            'dns_info' => $dnsCheck,
        ];
    }

    /**
     * Get DNS instructions for tenant
     */
    public function getDNSInstructions(string $domain): array
    {
        $baseDomain = config('app.domain', 'petmelo.com');
        $serverIP = config('app.server_ip');

        return [
            'domain' => $domain,
            'instructions' => [
                [
                    'step' => 1,
                    'title' => 'Go to your domain provider',
                    'description' => 'Log in to your domain registrar (GoDaddy, Namecheap, Cloudflare, etc.)',
                ],
                [
                    'step' => 2,
                    'title' => 'Create CNAME record',
                    'description' => 'Add a CNAME record with the following details:',
                    'details' => [
                        'Type' => 'CNAME',
                        'Name' => $this->getCNAMEName($domain),
                        'Value/Target' => $baseDomain,
                        'TTL' => 'Auto or 3600',
                    ],
                ],
                [
                    'step' => 3,
                    'title' => 'Alternative: Use A record',
                    'description' => $serverIP ? 'If your provider doesn\'t support CNAME for root domain, use:' : 'Contact support for server IP address',
                    'details' => $serverIP ? [
                        'Type' => 'A',
                        'Name' => '@',
                        'Value/IP' => $serverIP,
                        'TTL' => 'Auto or 3600',
                    ] : null,
                ],
                [
                    'step' => 4,
                    'title' => 'Wait for DNS propagation',
                    'description' => 'DNS changes can take 5 minutes to 24 hours to propagate globally.',
                ],
                [
                    'step' => 5,
                    'title' => 'Verify configuration',
                    'description' => 'Return to your dashboard and click "Verify Domain" to activate SSL.',
                ],
            ],
        ];
    }

    /**
     * Get CNAME name based on domain
     */
    private function getCNAMEName(string $domain): string
    {
        // If it's www.example.com, return 'www'
        // If it's example.com, return '@'
        $parts = explode('.', $domain);
        
        if (count($parts) > 2 && $parts[0] === 'www') {
            return 'www';
        }
        
        return '@';
    }
}
