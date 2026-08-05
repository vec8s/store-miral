<?php
declare(strict_types=1);
namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class AuthController
{
    public function requestOTP(Request $request): JsonResponse {
        return response()->json(['success' => true, 'message' => 'تم إرسال الكود بنجاح']);
    }
}
