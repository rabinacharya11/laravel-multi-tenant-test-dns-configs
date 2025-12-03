<?php

namespace App\Http\Controllers;

use App\Models\Domain;
use App\Models\Tenant;
use App\Services\DomainService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class DomainController extends Controller
{
    protected DomainService $domainService;

    public function __construct(DomainService $domainService)
    {
        $this->domainService = $domainService;
    }

    /**
     * Get all domains for current tenant
     */
    public function index(): JsonResponse
    {
        $tenant = tenant();
        
        if (!$tenant) {
            return response()->json([
                'error' => 'Tenant context not initialized',
            ], 400);
        }

        $domains = $tenant->domains()->get()->map(function ($domain) {
            return [
                'id' => $domain->id,
                'domain' => $domain->domain,
                'type' => $domain->type,
                'is_primary' => $domain->is_primary,
                'is_verified' => $domain->is_verified,
                'verified_at' => $domain->verified_at,
                'created_at' => $domain->created_at,
            ];
        });

        return response()->json([
            'success' => true,
            'domains' => $domains,
        ]);
    }

    /**
     * Add a new custom domain
     */
    public function store(Request $request): JsonResponse
    {
        $tenant = tenant();
        
        if (!$tenant) {
            return response()->json([
                'error' => 'Tenant context not initialized',
            ], 400);
        }

        $request->validate([
            'domain' => 'required|string|max:255',
            'is_primary' => 'boolean',
        ]);

        // Clean the domain input
        $domain = $this->cleanDomainInput($request->input('domain'));

        $result = $this->domainService->addCustomDomain(
            $tenant,
            $domain,
            $request->input('is_primary', false)
        );

        if (!$result['success']) {
            return response()->json($result, 422);
        }

        // Get DNS instructions
        $instructions = $this->domainService->getDNSInstructions($domain);

        return response()->json([
            'success' => true,
            'message' => $result['message'],
            'domain' => [
                'id' => $result['domain']->id,
                'domain' => $result['domain']->domain,
                'type' => $result['domain']->type,
                'is_verified' => $result['domain']->is_verified,
                'verification_token' => $result['verification_token'],
            ],
            'dns_instructions' => $instructions,
        ], 201);
    }

    /**
     * Clean domain input by removing protocol, paths, etc.
     */
    private function cleanDomainInput(string $domain): string
    {
        // Remove whitespace
        $domain = trim($domain);
        
        // Remove http:// or https://
        $domain = preg_replace('/^https?:\/\//', '', $domain);
        
        // Remove everything after the first slash (paths, query strings)
        $domain = preg_replace('/\/.*$/', '', $domain);
        
        // Convert to lowercase
        $domain = strtolower($domain);
        
        return $domain;
    }

    /**
     * Verify a custom domain
     */
    public function verify(Request $request, int $domainId): JsonResponse
    {
        $tenant = tenant();
        
        if (!$tenant) {
            return response()->json([
                'error' => 'Tenant context not initialized',
            ], 400);
        }

        $domain = Domain::where('id', $domainId)
            ->where('tenant_id', $tenant->id)
            ->first();

        if (!$domain) {
            return response()->json([
                'error' => 'Domain not found',
            ], 404);
        }

        $result = $this->domainService->verifyAndActivateDomain($domain);

        return response()->json($result, $result['success'] ? 200 : 422);
    }

    /**
     * Get DNS instructions for a domain
     */
    public function instructions(Request $request, int $domainId): JsonResponse
    {
        $tenant = tenant();
        
        if (!$tenant) {
            return response()->json([
                'error' => 'Tenant context not initialized',
            ], 400);
        }

        $domain = Domain::where('id', $domainId)
            ->where('tenant_id', $tenant->id)
            ->first();

        if (!$domain) {
            return response()->json([
                'error' => 'Domain not found',
            ], 404);
        }

        $instructions = $this->domainService->getDNSInstructions($domain->domain);

        return response()->json([
            'success' => true,
            'domain' => $domain->domain,
            'is_verified' => $domain->is_verified,
            'instructions' => $instructions,
        ]);
    }

    /**
     * Delete a custom domain
     */
    public function destroy(int $domainId): JsonResponse
    {
        $tenant = tenant();
        
        if (!$tenant) {
            return response()->json([
                'error' => 'Tenant context not initialized',
            ], 400);
        }

        $domain = Domain::where('id', $domainId)
            ->where('tenant_id', $tenant->id)
            ->where('type', 'custom')
            ->first();

        if (!$domain) {
            return response()->json([
                'error' => 'Domain not found or cannot be deleted',
            ], 404);
        }

        if ($domain->is_primary) {
            return response()->json([
                'error' => 'Cannot delete primary domain. Please set another domain as primary first.',
            ], 422);
        }

        $domain->delete();

        return response()->json([
            'success' => true,
            'message' => 'Domain deleted successfully',
        ]);
    }

    /**
     * Set a domain as primary
     */
    public function setPrimary(int $domainId): JsonResponse
    {
        $tenant = tenant();
        
        if (!$tenant) {
            return response()->json([
                'error' => 'Tenant context not initialized',
            ], 400);
        }

        $domain = Domain::where('id', $domainId)
            ->where('tenant_id', $tenant->id)
            ->first();

        if (!$domain) {
            return response()->json([
                'error' => 'Domain not found',
            ], 404);
        }

        if (!$domain->is_verified) {
            return response()->json([
                'error' => 'Domain must be verified before setting as primary',
            ], 422);
        }

        // Unset all other primary domains
        Domain::where('tenant_id', $tenant->id)
            ->where('is_primary', true)
            ->update(['is_primary' => false]);

        // Set this as primary
        $domain->update(['is_primary' => true]);

        return response()->json([
            'success' => true,
            'message' => 'Domain set as primary successfully',
        ]);
    }
}
