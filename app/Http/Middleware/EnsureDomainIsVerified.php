<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureDomainIsVerified
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Only check if tenancy is initialized
        if (!tenancy()->initialized) {
            return $next($request);
        }

        $currentDomain = $request->getHost();
        
        // Get the domain from database
        $domain = \App\Models\Domain::where('domain', $currentDomain)->first();

        // If domain exists but is not verified and is a custom domain, show error
        if ($domain && !$domain->is_verified && $domain->type === 'custom') {
            return response()->view('errors.domain-not-verified', [
                'domain' => $currentDomain,
            ], 403);
        }

        return $next($request);
    }
}
