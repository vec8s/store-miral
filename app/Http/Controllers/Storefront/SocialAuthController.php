<?php

declare(strict_types=1);

namespace App\Http\Controllers\Storefront;

use App\Domains\Identity\Enums\AuthProvider;
use App\Domains\Identity\Enums\RoleCode;
use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Laravel\Socialite\Contracts\User as SocialiteUser;
use Laravel\Socialite\Facades\Socialite;
use Symfony\Component\HttpFoundation\RedirectResponse as SymfonyRedirectResponse;

class SocialAuthController extends Controller
{
    private const ALLOWED_PROVIDERS = [
        'google' => AuthProvider::Google,
        'apple' => AuthProvider::Apple,
    ];

    public function redirect(string $provider): SymfonyRedirectResponse
    {
        if (! isset(self::ALLOWED_PROVIDERS[$provider])) {
            return redirect()->route('login')->withErrors([
                'email' => 'مزوّد تسجيل الدخول غير مدعوم.',
            ]);
        }

        if (! $this->isConfigured($provider)) {
            return redirect()->route('login')->withErrors([
                'email' => 'تسجيل الدخول عبر '.AuthProvider::from($provider)->label().' غير مُفعّل حالياً.',
            ]);
        }

        try {
            return Socialite::driver($provider)->redirect();
        } catch (\Throwable $e) {
            Log::warning('Social login redirect failed', [
                'provider' => $provider,
                'error' => $e->getMessage(),
            ]);

            return redirect()->route('login')->withErrors([
                'email' => 'تسجيل الدخول عبر '.AuthProvider::from($provider)->label().' غير مُفعّل حالياً.',
            ]);
        }
    }

    public function callback(string $provider): SymfonyRedirectResponse
    {
        if (! isset(self::ALLOWED_PROVIDERS[$provider])) {
            return redirect()->route('login')->withErrors([
                'email' => 'مزوّد تسجيل الدخول غير مدعوم.',
            ]);
        }

        try {
            $social = Socialite::driver($provider)->user();
        } catch (\Throwable $e) {
            Log::warning('Social login callback failed', [
                'provider' => $provider,
                'error' => $e->getMessage(),
            ]);

            return redirect()->route('login')->withErrors([
                'email' => 'تعذّر تسجيل الدخول عبر '.AuthProvider::from($provider)->label().'، حاول مرة أخرى.',
            ]);
        }

        if (! $social->getEmail()) {
            return redirect()->route('login')->withErrors([
                'email' => 'لم يتم توفير بريد إلكتروني من '.AuthProvider::from($provider)->label().'، استخدم التسجيل بالبريد الإلكتروني.',
            ]);
        }

        $user = $this->findOrCreateUser($provider, $social);

        Auth::login($user, true);

        return $user->hasAnyRole([RoleCode::SuperAdmin, RoleCode::Admin, RoleCode::Manager])
            ? redirect()->intended('/admin')
            : redirect()->intended('/');
    }

    private function isConfigured(string $provider): bool
    {
        return match ($provider) {
            'google' => config('services.google.client_id') !== null && config('services.google.client_id') !== '',
            'apple' => config('services.apple.client_id') !== null && config('services.apple.client_id') !== '',
            default => false,
        };
    }

    private function findOrCreateUser(string $provider, SocialiteUser $social): User
    {
        $authProvider = self::ALLOWED_PROVIDERS[$provider];
        $email = strtolower($social->getEmail());
        $providerId = (string) $social->getId();

        return DB::transaction(function () use ($authProvider, $providerId, $email, $social): User {
            $user = User::where('auth_provider', $authProvider->value)
                ->where('auth_provider_id', $providerId)
                ->first();

            if ($user) {
                return $user;
            }

            $user = User::where('email', $email)->first();

            if ($user) {
                $user->forceFill([
                    'auth_provider' => $authProvider->value,
                    'auth_provider_id' => $providerId,
                    'avatar_url' => $social->getAvatar() ?? $user->avatar_url,
                ])->save();

                return $user;
            }

            return User::create([
                'auth_provider' => $authProvider->value,
                'auth_provider_id' => $providerId,
                'name' => $social->getName() ?: $email,
                'email' => $email,
                'avatar_url' => $social->getAvatar(),
                'email_verified_at' => now(),
            ]);
        });
    }
}
