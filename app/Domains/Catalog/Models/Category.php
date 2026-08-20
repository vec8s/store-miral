<?php

declare(strict_types=1);

namespace App\Domains\Catalog\Models;

use App\Domains\Catalog\Enums\SyncStatus;
use App\Domains\Media\Models\Media;
use App\Shared\Models\BaseModel;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;

class Category extends BaseModel
{
    use HasFactory;

    protected $fillable = [
        'salla_id',
        'parent_id',
        'name',
        'slug',
        'description',
        'image_url',
        'banner_url',
        'sort_order',
        'is_visible',
        'seo_title',
        'seo_description',
        'seo_keywords',
        'canonical_url',
        'source_updated_at',
        'synced_at',
        'sync_status',
        'sync_error',
        'extra_attributes',
        'created_by_id',
        'updated_by_id',
    ];

    protected function casts(): array
    {
        return [
            'sort_order' => 'integer',
            'is_visible' => 'boolean',
            'source_updated_at' => 'datetime',
            'synced_at' => 'datetime',
            'sync_status' => SyncStatus::class,
            'extra_attributes' => 'array',
        ];
    }

    public function parent(): BelongsTo
    {
        return $this->belongsTo(self::class, 'parent_id');
    }

    public function children(): HasMany
    {
        return $this->hasMany(self::class, 'parent_id')->orderBy('sort_order');
    }

    public function products(): HasMany
    {
        return $this->hasMany(Product::class, 'category_id');
    }

    public function media(): MorphMany
    {
        return $this->morphMany(Media::class, 'mediable');
    }

    public function scopeVisible($query)
    {
        return $query->where('is_visible', true);
    }

    public function scopeRoots($query)
    {
        return $query->whereNull('parent_id');
    }

    public function scopeBySlug($query, string $slug)
    {
        return $query->where('slug', $slug);
    }
}
