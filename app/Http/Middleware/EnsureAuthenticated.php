<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureAuthenticated
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // إذا لم يكن المستخدم مسجل الدخول، قم بإعادة توجيهه لصفحة الدخول
        if (!Auth::check()) {
            return redirect()->route('login')->with('error', 'الرجاء تسجيل الدخول أولاً');
        }
        return $next($request);
    }
}
