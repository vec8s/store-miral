<?php

declare(strict_types=1);

namespace App\Domains\Blog\Models;

use App\Domains\Blog\Enums\PostLayout;
use App\Domains\Identity\Models\User;
use App\Shared\Enums\PublicationStatus;
use App\Shared\Enums\Visibility;
use App\Shared\Models\BaseModel;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Post extends BaseModel
{
    use HasFactory;
    use SoftDeletes;

    protected $fillable = ['post_category_id', 'author_id', 'title', 'slug', 'excerpt', 'content', 'featured_image_url', 'status', 'visibility', 'password', 'is_featured', 'allow_comments', 'view_count', 'reading_time_minutes', 'published_at', 'scheduled_at', 'layout', 'seo_title', 'seo_description', 'seo_keywords', 'canonical_url', 'og_image_url', 'robots', 'custom_fields', 'created_by_id', 'updated_by_id'];
    protected $hidden = ['password'];
    protected function casts(): array { return ['status' => PublicationStatus::class, 'visibility' => Visibility::class, 'layout' => PostLayout::class, 'is_featured' => 'boolean', 'allow_comments' => 'boolean', 'view_count' => 'integer', 'reading_time_minutes' => 'integer', 'published_at' => 'datetime', 'scheduled_at' => 'datetime', 'custom_fields' => 'array']; }
    public function category(): BelongsTo { return $this->belongsTo(PostCategory::class, 'post_category_id'); }
    public function author(): BelongsTo { return $this->belongsTo(User::class, 'author_id'); }
    public function tags(): BelongsToMany { return $this->belongsToMany(PostTag::class, 'post_tag'); }
    public function media(): MorphMany { return $this->morphMany(\App\Domains\Media\Models\Media::class, 'mediable'); }
    public function scopePublished($q) { return $q->where('status', PublicationStatus::Published->value); }
    public function scopeBySlug($q, string $slug) { return $q->where('slug', $slug); }
}
