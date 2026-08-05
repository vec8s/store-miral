<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

final class ValidateProductId
{
    public function handle(Request $request, Closure $next): Response
    {
        $id = $request->route('id');

        if ($id === null || ! ctype_digit((string) $id) || (int) $id < 1) {
            abort(404, 'Invalid product ID');
        }

        $request->route()->setParameter('id', (int) $id);

        return $next($request);
    }
}
