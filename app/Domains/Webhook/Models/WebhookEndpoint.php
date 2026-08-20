<?php

declare(strict_types=1);

namespace App\Domains\Webhook\Models;

use App\Domains\Webhook\Enums\SignatureAlgorithm;
use App\Shared\Models\BaseModel;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class WebhookEndpoint extends BaseModel
{
    use HasFactory;
    use SoftDeletes;

    protected $fillable = ['name', 'slug', 'url', 'secret', 'algorithm', 'subscribed_events', 'is_active', 'timeout_seconds', 'max_retries', 'description', 'last_triggered_at', 'total_deliveries', 'failed_deliveries', 'created_by_id', 'updated_by_id'];

    protected $hidden = ['secret'];

    protected function casts(): array
    {
        return ['algorithm' => SignatureAlgorithm::class, 'subscribed_events' => 'array', 'is_active' => 'boolean', 'timeout_seconds' => 'integer', 'max_retries' => 'integer', 'last_triggered_at' => 'datetime', 'total_deliveries' => 'integer', 'failed_deliveries' => 'integer'];
    }

    public function events(): HasMany
    {
        return $this->hasMany(WebhookEvent::class);
    }

    public function scopeActive($q)
    {
        return $q->where('is_active', true);
    }
}
