<?php

declare(strict_types=1);

namespace App\Domains\CMS\Models;

use App\Domains\CMS\Enums\RedirectStatusCode;
use App\Shared\Models\BaseModel;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Redirect extends BaseModel
{
    use HasFactory;

    protected $fillable = ['source_url', 'target_url', 'status_code', 'is_active', 'hit_count', 'last_hit_at', 'notes', 'created_by_id', 'updated_by_id'];

    protected function casts(): array
    {
        return ['status_code' => RedirectStatusCode::class, 'is_active' => 'boolean', 'hit_count' => 'integer', 'last_hit_at' => 'datetime'];
    }

    public function scopeActive($q)
    {
        return $q->where('is_active', true);
    }
}
