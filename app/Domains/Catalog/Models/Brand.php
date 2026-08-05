<?php

declare(strict_types=1);

namespace App\Domains\Catalog\Models;

use App\Domains\Catalog\Enums\SyncStatus;
use App\Shared\Models\BaseModel;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;

class Brand extends BaseModel
{
    use HasFactory;

    protected $fillable = [
        'salla_id',
        'name',
        'slug',
        'logo_url',
        'banner_url',
        'description',
        'is_visible',
        'sort_order',
        'seo_title',
        'seo_description',
        'seo_keywords',
        'canonical_url',
        'source_updated_at',
        'synced_at',
        'sync_status',
        'sync_error',
        'extra_attributes',
    ];

    protected function casts(): array
    {
        return [
            'is_visible' => 'boolean',
            'sort_order' => 'integer',
            'source_updated_at' => 'datetime',
            'synced_at' => 'datetime',
            'sync_status' => SyncStatus::class,
            'extra_attributes' => 'array',
        ];
    }

    public function products(): HasMany
    {
        return $this->hasMany(Product::class, 'brand_id');
    }

    public function media(): MorphMany
    {
        return $this->morphMany(\App\Domains\Media\Models\Media::class, 'mediable');
    }

    public function scopeVisible($query)
    {
        return $query->where('is_visible', true);
    }
}
