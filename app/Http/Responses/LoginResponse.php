<?php

namespace App\Http\Responses;

use App\Domains\Identity\Enums\RoleCode;
use Illuminate\Http\Request;
use Laravel\Fortify\Contracts\LoginResponse as LoginResponseContract;
use Symfony\Component\HttpFoundation\Response;

class LoginResponse implements LoginResponseContract
{
    /**
     * Create an HTTP response that represents the object.
     *
     * @param  Request  $request
     * @return Response
     */
    public function toResponse($request)
    {
        if ($request->wantsJson()) {
            return response()->json(['two_factor' => false]);
        }

        $user = $request->user();

        $fallback = $user?->hasAnyRole([RoleCode::SuperAdmin, RoleCode::Admin, RoleCode::Manager])
            ? '/admin'
            : '/';

        return redirect()->intended($fallback);
    }
}
