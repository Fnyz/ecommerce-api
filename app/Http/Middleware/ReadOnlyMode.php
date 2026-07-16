<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ReadOnlyMode
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
    private array $allowedRoutes = [
        'api/v1/login',
        'api/v1/logout',
        'api/v1/stripe/webhook',
        'api/v1/products',
        'api/v1/categories',
        'api/v1/carts',
        'api/v1/orders',
        'api/v1/admin/dashboard',
        'api/v1/admin/orders',
    ];

    public function handle(Request $request, Closure $next): Response
    {
        $isWriteMethod = in_array($request->method(), ['POST', 'PUT', 'PATCH', 'DELETE']);
        $isAllowed = collect($this->allowedRoutes)->contains(fn($route) => $request->is($route));

        if (app()->isProduction() && $isWriteMethod && !$isAllowed) {
            return response()->json([
                'message' => 'This API is currently in read-only mode. Please contact the developer to make changes.',
            ], 403);
        }

        return $next($request);
    }
}
