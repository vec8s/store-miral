<?php

declare(strict_types=1);

namespace App\Domains\Blog\Models;

use App\Shared\Models\BaseModel;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class PostCategory extends BaseModel
{
    use HasFactory;
    use SoftDeletes;

    protected $fillable = ['name', 'slug', 'description', 'sort_order', 'parent_id', 'seo_title', 'seo_description'];
    public function posts(): HasMany { return $this->hasMany(Post::class); }
    public function scopeRoots($q) { return $q->whereNull('parent_id'); }
}
