<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureCompanyAccess
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if (! $user) {
            return $next($request);
        }

        if ($request->routeIs('billing.*') || $request->routeIs('profile.*')) {
            return $next($request);
        }

        $company = $user->company;

        if (! $company || ! $company->accessBlocked()) {
            return $next($request);
        }

        return redirect()
            ->route('billing.portal')
            ->with('error', $company->accessNotice()['message'] ?? 'Sua conta está temporariamente bloqueada.');
    }
}
