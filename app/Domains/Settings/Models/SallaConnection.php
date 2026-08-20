<?php

declare(strict_types=1);

namespace App\Domains\Settings\Models;

use App\Shared\Models\BaseModel;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class SallaConnection extends BaseModel
{
    use HasFactory;

    protected $table = 'salla_connections';

    protected $fillable = [
        'store_id',
        'merchant_id',
        'access_token',
        'refresh_token',
        'expires_at',
        'scopes',
        'store_payload',
        'status',
        'last_successful_request_at',
        'last_refresh_at',
    ];

    protected $hidden = [
        'access_token',
        'refresh_token',
    ];

    protected function casts(): array
    {
        return [
            'access_token' => 'encrypted',
            'refresh_token' => 'encrypted',
            'expires_at' => 'datetime',
            'scopes' => 'array',
            'store_payload' => 'array',
            'last_successful_request_at' => 'datetime',
            'last_refresh_at' => 'datetime',
        ];
    }

    public function scopeConnected($query)
    {
        return $query->where('status', 'connected');
    }
}
