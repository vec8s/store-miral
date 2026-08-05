<?php

declare(strict_types=1);

namespace App\Domains\Webhook\Models;

use App\Shared\Models\BaseModel;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class WebhookEventLog extends BaseModel
{
    use HasFactory;

    protected $fillable = [
        'webhook_event_id',
        'attempt_number',
        'level',
        'stage',
        'message',
        'context',
        'duration_ms',
        'source_ip',
        'occurred_at',
    ];

    protected function casts(): array
    {
        return [
            'attempt_number' => 'integer',
            'context' => 'array',
            'duration_ms' => 'integer',
            'occurred_at' => 'datetime',
        ];
    }

    public function event(): BelongsTo
    {
        return $this->belongsTo(WebhookEvent::class, 'webhook_event_id');
    }

    public function scopeForEvent(Builder $query, int $eventId): Builder
    {
        return $query->where('webhook_event_id', $eventId);
    }

    public function scopeByLevel(Builder $query, string $level): Builder
    {
        return $query->where('level', $level);
    }

    public function scopeErrors(Builder $query): Builder
    {
        return $query->whereIn('level', ['error', 'critical']);
    }

    public function scopeByStage(Builder $query, string $stage): Builder
    {
        return $query->where('stage', $stage);
    }

    public function scopeByAttempt(Builder $query, int $attempt): Builder
    {
        return $query->where('attempt_number', $attempt);
    }

    public function scopeRecent(Builder $query): Builder
    {
        return $query->orderByDesc('occurred_at')->orderByDesc('id');
    }
}
