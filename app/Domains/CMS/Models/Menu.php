<?php

declare(strict_types=1);

namespace App\Domains\CMS\Models;

use App\Domains\CMS\Enums\MenuLocation;
use App\Shared\Models\BaseModel;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Menu extends BaseModel
{
    use HasFactory;
    use SoftDeletes;

    protected $fillable = ['name', 'slug', 'location', 'description', 'is_active'];
    protected function casts(): array { return ['location' => MenuLocation::class, 'is_active' => 'boolean']; }
    public function items(): HasMany { return $this->hasMany(MenuItem::class)->orderBy('sort_order'); }
    public function scopeActive($q) { return $q->where('is_active', true); }
}
