<?php

declare(strict_types=1);

namespace App\Http\Controllers\Salla;

use App\Http\Controllers\Controller;
use App\Jobs\SyncSallaProducts;
use App\Shared\Salla\SallaAuthenticator;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Throwable;

class SallaOAuthController extends Controller
{
    /**
     * Redirect the merchant to Salla OAuth authorization page.
     */
    public function connect(): RedirectResponse
    {
        $clientId = (string) config('salla.client_id');
        $redirectUri = (string) config('salla.redirect_uri');

        if (empty($clientId) || empty($redirectUri)) {
            return redirect()->route('admin.settings')->with('error', 'يرجى ضبط SALLA_CLIENT_ID و SALLA_REDIRECT_URI في إعدادات البيئة أولاً.');
        }

        $scopes = 'offline_access products.read_write orders.read_write customers.read';
        $state = bin2hex(random_bytes(16));
        session()->put('salla_oauth_state', $state);

        $params = http_build_query([
            'client_id' => $clientId,
            'response_type' => 'code',
            'redirect_uri' => $redirectUri,
            'scope' => $scopes,
            'state' => $state,
        ]);

        return redirect()->away("https://accounts.salla.sa/oauth2/auth?{$params}");
    }

    /**
     * Handle the OAuth callback from Salla.
     */
    public function callback(
        Request $request,
        SallaAuthenticator $authenticator
    ): RedirectResponse {
        $code = (string) $request->query('code');
        $error = (string) $request->query('error');
        $errorDescription = (string) $request->query('error_description', $error);

        if (! empty($error)) {
            Log::error('[SallaOAuth] Authorization rejected or failed: '.$errorDescription);

            return redirect()->route('admin.settings')->with('error', 'فشل تفويض سلة: '.$errorDescription);
        }

        if (empty($code)) {
            return redirect()->route('admin.settings')->with('error', 'لم يتم استلام رمز التفويض (code) من منصة سلة.');
        }

        try {
            $authenticator->exchangeCode($code);

            // Trigger initial background sync for catalog
            try {
                SyncSallaProducts::dispatch(perPage: 50);
            } catch (Throwable $e) {
                Log::warning('[SallaOAuth] Initial product sync dispatch warning: '.$e->getMessage());
            }

            return redirect()->route('admin.settings')->with('status', 'تم الربط مع متجر سلة بنجاح وتوليد وتخزين توكن الوصول والتحديث! بدأت مزامنة المنتجات.');
        } catch (Throwable $e) {
            Log::error('[SallaOAuth] Token exchange error: '.$e->getMessage());

            return redirect()->route('admin.settings')->with('error', 'حدث خطأ أثناء تبادل التوكن مع سلة: '.$e->getMessage());
        }
    }
}
