<?php

declare(strict_types=1);

namespace App\Domains\Identity\Models;

use App\Shared\Models\BaseModel;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

/**
 * @property int $id
 * @property string $code
 * @property string $name
 * @property string $group
 * @property string|null $description
 * @property bool $is_protected
 * @property \Illuminate\Support\Carbon $created_at
 * @property \Illuminate\Support\Carbon $updated_at
 *
 * @property-read \Illuminate\Database\Eloquent\Collection<int, Role> $roles
 *
 * @mixin Builder
 */
class Permission extends BaseModel
{
    use HasFactory;

    protected $fillable = [
        "code",
        "name",
        "group",
        "description",
        "is_protected",
    ];

    protected function casts(): array
    {
        return [
            "is_protected" => "boolean",
        ];
    }

    public function roles(): BelongsToMany
    {
        return $this->belongsToMany(Role::class, "permission_role");
    }

    public function scopeByCode(Builder $query, string $code): Builder
    {
        return $query->where("code", $code);
    }

    public function scopeInGroup(Builder $query, string $group): Builder
    {
        return $query->where("group", $group);
    }

    public function scopeProtected(Builder $query): Builder
    {
        return $query->where("is_protected", true);
    }
}
