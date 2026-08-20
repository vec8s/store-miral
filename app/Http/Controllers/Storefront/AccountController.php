<?php

declare(strict_types=1);

namespace App\Http\Controllers\Storefront;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Inertia\Inertia;
use Inertia\Response;

class AccountController extends Controller
{
    public function profile(): Response
    {
        return Inertia::render('Customer/Account');
    }

    public function updateProfile(Request $request): JsonResponse|RedirectResponse
    {
        $user = auth()->user();
        if ($user) {
            $validated = $request->validate([
                'name' => ['required', 'string', 'max:255'],
                'email' => ['required', 'string', 'email', 'max:255', Rule::unique('users')->ignore($user->id)],
                'phone' => ['nullable', 'string', 'regex:/^[0-9+\- ]{7,20}$/'],
            ]);

            $user->update($validated);
        }

        if ($request->wantsJson()) {
            return response()->json(['success' => true, 'message' => 'تم تحديث البيانات بنجاح!']);
        }

        return back()->with('status', 'تم تحديث البيانات بنجاح!');
    }
}
