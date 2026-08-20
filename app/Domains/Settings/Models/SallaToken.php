<?php

declare(strict_types=1);

namespace App\Domains\Settings\Models;

use App\Shared\Models\BaseModel;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class SallaToken extends BaseModel
{
    use HasFactory;

    protected $fillable = ['merchant_id', 'access_token', 'refresh_token', 'token_type', 'scope', 'access_token_expires_at', 'refresh_token_expires_at', 'metadata'];

    protected $hidden = ['access_token', 'refresh_token'];

    protected function casts(): array
    {
        return ['access_token_expires_at' => 'datetime', 'refresh_token_expires_at' => 'datetime', 'metadata' => 'array'];
    }
}
