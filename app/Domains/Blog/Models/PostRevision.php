<?php

declare(strict_types=1);

namespace App\Domains\Blog\Models;

use App\Domains\Identity\Models\User;
use App\Shared\Models\BaseModel;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property int $id
 * @property int $post_id
 * @property string $title
 * @property string $content
 * @property string|null $excerpt
 * @property string|null $revision_note
 * @property int|null $editor_id
 * @property \Illuminate\Support\Carbon $created_at
 * @property \Illuminate\Support\Carbon $updated_at
 *
 * @property-read Post $post
 * @property-read User|null $editor
 *
 * @mixin Builder
 */
class PostRevision extends BaseModel
{
    use HasFactory;

    protected $fillable = [
        "post_id",
        "title",
        "content",
        "excerpt",
        "revision_note",
        "editor_id",
    ];

    public function post(): BelongsTo
    {
        return $this->belongsTo(Post::class);
    }

    public function editor(): BelongsTo
    {
        return $this->belongsTo(User::class, "editor_id");
    }

    public function scopeByPost(Builder $query, int $postId): Builder
    {
        return $query->where("post_id", $postId);
    }

    public function scopeRecent(Builder $query): Builder
    {
        return $query->orderByDesc("created_at");
    }
}
