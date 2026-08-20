<?php

declare(strict_types=1);

namespace App\Domains\CMS\Models;

use App\Domains\Identity\Models\User;
use App\Shared\Models\BaseModel;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;

/**
 * @property int $id
 * @property int $page_id
 * @property string $title
 * @property string|null $content
 * @property array<string,mixed>|null $custom_fields
 * @property string|null $revision_note
 * @property int|null $editor_id
 * @property Carbon $created_at
 * @property Carbon $updated_at
 * @property-read Page $page
 * @property-read User|null $editor
 *
 * @mixin Builder
 */
class PageRevision extends BaseModel
{
    use HasFactory;

    protected $fillable = [
        'page_id',
        'title',
        'content',
        'custom_fields',
        'revision_note',
        'editor_id',
    ];

    protected function casts(): array
    {
        return [
            'custom_fields' => 'array',
        ];
    }

    public function page(): BelongsTo
    {
        return $this->belongsTo(Page::class);
    }

    public function editor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'editor_id');
    }

    public function scopeByPage(Builder $query, int $pageId): Builder
    {
        return $query->where('page_id', $pageId);
    }

    public function scopeRecent(Builder $query): Builder
    {
        return $query->orderByDesc('created_at');
    }
}
