<?php

declare(strict_types=1);

namespace App\Domains\Sync\Models;

use App\Domains\Sync\Enums\SyncStatus;
use App\Domains\Sync\Enums\SyncTrigger;
use App\Domains\Sync\Enums\SyncType;
use App\Shared\Models\BaseModel;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;

class SyncJob extends BaseModel
{
    use HasFactory;

    protected $fillable = ['reference', 'resource_type', 'sync_type', 'status', 'total_items', 'processed_items', 'successful_items', 'failed_items', 'batch_size', 'attempts', 'max_attempts', 'filters', 'metadata', 'error_message', 'failure_context', 'started_at', 'completed_at', 'failed_at', 'next_retry_at', 'duration_seconds', 'triggered_by_id', 'triggered_by_type', 'triggered_by_source'];
    protected function casts(): array { return ['sync_type' => SyncType::class, 'status' => Sync

cat > app/Domains/Sync/Models/SyncJob.php << 'PHPEOF'
<?php

declare(strict_types=1);

namespace App\Domains\Sync\Models;

use App\Domains\Sync\Enums\SyncStatus;
use App\Domains\Sync\Enums\SyncTrigger;
use App\Domains\Sync\Enums\SyncType;
use App\Shared\Models\BaseModel;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;

class SyncJob extends BaseModel
{
    use HasFactory;

    protected $fillable = ['reference', 'resource_type', 'sync_type', 'status', 'total_items', 'processed_items', 'successful_items', 'failed_items', 'batch_size', 'attempts', 'max_attempts', 'filters', 'metadata', 'error_message', 'failure_context', 'started_at', 'completed_at', 'failed_at', 'next_retry_at', 'duration_seconds', 'triggered_by_id', 'triggered_by_type', 'triggered_by_source'];
    protected function casts(): array { return ['sync_type' => SyncType::class, 'status' => SyncStatus::class, 'triggered_by_type' => SyncTrigger::class, 'total_items' => 'integer', 'processed_items' => 'integer', 'successful_items' => 'integer', 'failed_items' => 'integer', 'batch_size' => 'integer', 'attempts' => 'integer', 'max_attempts' => 'integer', 'filters' => 'array', 'metadata' => 'array', 'started_at' => 'datetime', 'completed_at' => 'datetime', 'failed_at' => 'datetime', 'next_retry_at' => 'datetime', 'duration_seconds' => 'integer']; }
    public function logs(): HasMany { return $this->hasMany(SyncLog::class); }
    public function scopePending($q) { return $q->where('status', SyncStatus::Pending->value); }
    public function scopeRunning($q) { return $q->where('status', SyncStatus::Running->value); }
    public function scopeFailed($q) { return $q->where('status', SyncStatus::Failed->value); }
}
