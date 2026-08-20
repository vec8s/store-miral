<?php

declare(strict_types=1);

namespace App\Domains\Blog\Models;

use App\Shared\Models\BaseModel;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class PostTag extends BaseModel
{
    use HasFactory;

    protected $fillable = ['name', 'slug', 'description', 'posts_count'];

    protected function casts(): array
    {
        return ['posts_count' => 'integer'];
    }

    public function posts(): BelongsToMany
    {
        return $this->belongsToMany(Post::class, 'post_tag');
    }
}
