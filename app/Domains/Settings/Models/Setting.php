<?php

declare(strict_types=1);

namespace App\Domains\Settings\Models;

use App\Shared\Models\BaseModel;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Setting extends BaseModel
{
    use HasFactory;

    protected $fillable = ['group', 'key', 'value', 'type', 'is_public'];

    protected function casts(): array
    {
        return ['is_public' => 'boolean'];
    }

    public function scopeInGroup($q, string $group)
    {
        return $q->where('group', $group);
    }

    public function scopeByKey($q, string $group, string $key)
    {
        return $q->where('group', $group)->where('key', $key);
    }
}
