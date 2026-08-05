<?php

declare(strict_types=1);

namespace App\Domains\Settings\Models;

use App\Shared\Models\BaseModel;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ApiLog extends BaseModel
{
    use HasFactory;

    protected $fillable = ['service', 'method', 'endpoint', 'status_code', 'request_headers', 'request_body', 'response_headers', 'response_body', 'duration_ms', 'request_id', 'correlation_id', 'source_ip', 'user_id', 'is_error', 'error_message', 'occurred_at'];
    protected $hidden = ['request_headers', 'response_headers'];
    protected function casts(): array { return ['status_code' => 'integer', 'request_headers' => 'array', 'request_body' => 'array', 'response_headers' => 'array', 'duration_ms' => 'integer', 'is_error' => 'boolean', 'occurred_at' => 'datetime']; }
}
