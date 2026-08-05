<?php

declare(strict_types=1);

namespace App\Domains\CMS\Models;

use App\Domains\CMS\Enums\MenuItemType;
use App\Shared\Models\BaseModel;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class MenuItem extends BaseModel
{
    use HasFactory;

    protected $fillable = ['menu_id', 'parent_id', 'title', 'url', 'type', 'reference_id', 'target', 'icon', 'css_class', 'description', 'sort_order', 'is_active'];
    protected function casts(): array { return ['type' => MenuItemType::class, 'reference_id' => 'integer', 'sort_order' => 'integer', 'is_active' => 'boolean']; }
    public function menu(): BelongsTo { return $this->belongsTo(Menu::class); }
    public function parent(): BelongsTo { return $this->belongsTo(self::class, 'parent_id'); }
    public function children(): HasMany { return $this->hasMany(self::class, 'parent_id')->orderBy('sort_order'); }
    public function scopeActive($q) { return $q->where('is_active', true); }
    public function scopeRoots($q) { return $q->whereNull('parent_id'); }
}
