<?php

declare(strict_types=1);

namespace App\Domains\Sync\Models;

use App\Domains\Sync\Enums\SyncAction;
use App\Shared\Models\BaseModel;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SyncLog extends BaseModel
{
    use HasFactory;

    protected $fillable = ['sync_job_id', 'resource_type', 'resource_id', 'salla_id', 'action', 'status', 'attempt_number', 'duration_ms', 'before_state', 'after_state', 'error_message', 'error_context', 'source_ip', 'occurred_at'];
    protected function casts(): array { return ['action' => SyncAction::class, 'attempt_number' => 'integer', 'duration_ms' => 'integer', 'before_state' => 'array', 'after_state' => 'array', 'error_context' => 'array', 'occurred_at' => 'datetime']; }
    public function job(): BelongsTo { return $this->belongsTo(SyncJob::class, 'sync_job_id'); }
}
