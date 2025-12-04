<?php

namespace App\Http\Controllers;

use App\Models\Tenant;
use App\Models\Domain;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Str;

class TenantController extends Controller
{
    /**
     * Create a new tenant with subdomain
     */
    public function store(Request $request): JsonResponse
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'subdomain' => 'required|string|max:63|regex:/^[a-z0-9][a-z0-9-]*[a-z0-9]$/|unique:domains,domain',
        ]);

        $baseDomain = config('app.domain', 'petmelo.com');
        $fullSubdomain = $request->input('subdomain') . '.' . $baseDomain;

        // Check if subdomain already exists
        if (Domain::where('domain', $fullSubdomain)->exists()) {
            return response()->json([
                'error' => 'This subdomain is already taken',
            ], 422);
        }

        // Create tenant
        $tenant = Tenant::create([
            'id' => Str::uuid()->toString(),
            'name' => $request->input('name'),
        ]);

        // Add subdomain as primary domain
        $tenant->addSubdomain($request->input('subdomain'), $baseDomain, true);

        return response()->json([
            'success' => true,
            'message' => 'Tenant created successfully',
            'tenant' => [
                'id' => $tenant->id,
                'name' => $request->input('name'),
                'subdomain' => $fullSubdomain,
                'url' => 'https://' . $fullSubdomain,
            ],
        ], 201);
    }

    /**
     * Get tenant information
     */
    public function show(string $tenantId): JsonResponse
    {
        $tenant = Tenant::with('domains')->find($tenantId);

        if (!$tenant) {
            return response()->json([
                'error' => 'Tenant not found',
            ], 404);
        }

        return response()->json([
            'success' => true,
            'tenant' => [
                'id' => $tenant->id,
                'name' => $tenant->data['name'] ?? null,
                'domains' => $tenant->domains->map(function ($domain) {
                    return [
                        'domain' => $domain->domain,
                        'type' => $domain->type,
                        'is_primary' => $domain->is_primary,
                        'is_verified' => $domain->is_verified,
                    ];
                }),
                'created_at' => $tenant->created_at,
            ],
        ]);
    }

    /**
     * List all tenants (for admin/central app)
     */
    public function index(): JsonResponse
    {
        $tenants = Tenant::with('domains')->get()->map(function ($tenant) {
            return [
                'id' => $tenant->id,
                'name' => $tenant->data['name'] ?? null,
                'primary_domain' => optional($tenant->primaryDomain())->domain,
                'domains_count' => $tenant->domains->count(),
                'created_at' => $tenant->created_at,
            ];
        });

        return response()->json([
            'success' => true,
            'tenants' => $tenants,
        ]);
    }
}
