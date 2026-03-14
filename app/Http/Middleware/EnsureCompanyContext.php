<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureCompanyContext
{
    public function handle(Request $request, Closure $next): Response
    {
        if ($request->user() && empty($request->user()->company_id)) {
            abort(403, 'Usuário autenticado sem empresa vinculada.');
        }

        return $next($request);
    }
}
