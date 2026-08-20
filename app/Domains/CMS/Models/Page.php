<?php

declare(strict_types=1);

namespace App\Domains\CMS\Models;

use App\Domains\CMS\Enums\PageTemplate;
use App\Domains\Identity\Models\User;
use App\Domains\Media\Models\Media;
use App\Shared\Enums\PublicationStatus;
use App\Shared\Enums\Visibility;
use App\Shared\Models\BaseModel;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Page extends BaseModel
{
    use HasFactory;
    use SoftDeletes;

    protected $fillable = ['title', 'slug', 'content', 'excerpt', 'featured_image_url', 'status', 'visibility', 'password', 'published_at', 'scheduled_at', 'template', 'layout', 'view_count', 'seo_title', 'seo_description', 'seo_keywords', 'canonical_url', 'og_image_url', 'robots', 'custom_fields', 'parent_id', 'sort_order', 'author_id', 'created_by_id', 'updated_by_id'];

    protected $hidden = ['password'];

    protected function casts(): array
    {
        return ['status' => PublicationStatus::class, 'visibility' => Visibility::class, 'template' => PageTemplate::class, 'published_at' => 'datetime', 'scheduled_at' => 'datetime', 'view_count' => 'integer', 'sort_order' => 'integer', 'custom_fields' => 'array'];
    }

    public function parent(): BelongsTo
    {
        return $this->belongsTo(self::class, 'parent_id');
    }

    public function children(): HasMany
    {
        return $this->hasMany(self::class, 'parent_id')->orderBy('sort_order');
    }

    public function author(): BelongsTo
    {
        return $this->belongsTo(User::class, 'author_id');
    }

    public function media(): MorphMany
    {
        return $this->morphMany(Media::class, 'mediable');
    }

    public function scopePublished($q)
    {
        return $q->where('status', PublicationStatus::Published->value);
    }

    public function scopeBySlug($q, string $slug)
    {
        return $q->where('slug', $slug);
    }
}
