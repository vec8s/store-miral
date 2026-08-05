<?php

// declare(strict_types=1);

// namespace App\Domains\Identity\Models;

// use App\Domains\Identity\Enums\RoleCode;
// use App\Shared\Enums\Locale;
// use App\Shared\Models\BaseModel;
// use Illuminate\Database\Eloquent\Factories\HasFactory;
// use Illuminate\Database\Eloquent\Relations\BelongsToMany;
// use Illuminate\Database\Eloquent\Relations\HasMany;
// use Illuminate\Database\Eloquent\Relations\MorphMany;
// use Illuminate\Database\Eloquent\SoftDeletes;
// use Illuminate\Notifications\Notifiable;
// use Laravel\Sanctum\HasApiTokens;

// /**
//  * @property int $id
//  * @property string|null $salla_customer_id
//  * @property string $name
//  * @property string $email
//  * @property string|null $phone
//  * @property \Illuminate\Support\Carbon|null $email_verified_at
//  * @property string|null $password
//  * @property string|null $avatar_url
//  * @property Locale $locale
//  * @property array<string,mixed>|null $preferences
//  * @property string|null $remember_token
//  * @property int|null $created_by_id
//  * @property int|null $updated_by_id
//  * @property \Illuminate\Support\Carbon $created_at
//  * @property \Illuminate\Support\Carbon $updated_at
//  * @property \Illuminate\Support\Carbon|null $deleted_at
//  *
//  * @property-read \Illuminate\Database\Eloquent\Collection<int, Role> $roles
//  *
//  * @mixin \Illuminate\Database\Eloquent\Builder
//  */
// class User extends BaseModel
// {
//     use HasApiTokens;
//     use HasFactory;
//     use Notifiable;
//     use SoftDeletes;

//     protected $fillable = [
//         'salla_customer_id',
//         'name',
//         'email',
//         'phone',
//         'password',
//         'avatar_url',
//         'locale',
//         'preferences',
//         'created_by_id',
//         'updated_by_id',
//     ];

//     protected $hidden = [
//         'password',
//         'remember_token',
//     ];

//     protected function casts(): array
//     {
//         return [
//             'email_verified_at' => 'datetime',
//             'password' => 'hashed',
//             'preferences' => 'array',
//             'locale' => Locale::class,
//         ];
//     }

//     public function roles(): BelongsToMany
//     {
//         return $this->belongsToMany(Role::class, 'role_user')
//             ->withPivot(['assigned_at', 'assigned_by_id']);
//     }

//     public function tokens(): HasMany
//     {
//         return $this->hasMany(\Laravel\Sanctum\PersonalAccessToken::class, 'tokenable_id');
//     }

//     public function wishlists(): HasMany
//     {
//         return $this->hasMany(\App\Domains\Wishlist\Models\Wishlist::class);
//     }

//     public function reviews(): HasMany
//     {
//         return $this->hasMany(\App\Domains\Reviews\Models\Review::class);
//     }

//     public function media(): MorphMany
//     {
//         return $this->morphMany(\App\Domains\Media\Models\Media::class, 'mediable');
//     }

//     public function scopeRole($query, string $code)
//     {
//         return $query->whereHas('roles', fn ($q) => $q->where('code', $code));
//     }

//     public function hasRole(RoleCode|string $code): bool
//     {
//         $needle = $code instanceof RoleCode ? $code->value : $code;
//         return $this->relationLoaded('roles')
//             ? $this->roles->contains('code', $needle)
//             : $this->roles()->where('code', $needle)->exists();
//     }

//     public function hasAnyRole(array $codes): bool
//     {
//         $values = array_map(
//             fn ($c) => $c instanceof RoleCode ? $c->value : $c,
//             $codes,
//         );
//         return $this->relationLoaded('roles')
//             ? $this->roles->whereIn('code', $values)->isNotEmpty()
//             : $this->roles()->whereIn('code', $values)->exists();
//     }

//     public function isStaff(): bool
//     {
//         return $this->relationLoaded('roles')
//             ? $this->roles->contains(fn (Role $r) => $r->codeEnum()->isStaff())
//             : $this->roles()->where('code', '!=', RoleCode::Customer->value)->exists();
//     }

//     public function highestRole(): ?Role
//     {
//         return $this->relationLoaded('roles')
//             ? $this->roles->sortByDesc('level')->first()
//             : $this->roles()->orderByDesc('level')->first();
//     }

//     public function assignRole(RoleCode $code, ?int $assignedById = null): void
//     {
//         $role = Role::where('code', $code

// cat > app/Domains/Identity/Models/User.php << 'PHPEOF'


declare(strict_types=1);

namespace App\Domains\Identity\Models;

use App\Shared\Models\BaseModel;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Permission extends BaseModel
{
    use HasFactory;

    protected $fillable = [
        'code',
        'name',
        'group',
        'description',
        'is_protected',
    ];

    protected function casts(): array
    {
        return [
            'is_protected' => 'boolean',
        ];
    }

    public function roles(): BelongsToMany
    {
        return $this->belongsToMany(Role::class, 'permission_role');
    }
}
