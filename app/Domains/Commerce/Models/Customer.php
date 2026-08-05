<?php

declare(strict_types=1);

namespace App\Domains\Commerce\Models;

use App\Domains\Catalog\Enums\SyncStatus;
use App\Shared\Enums\Gender;
use App\Shared\Models\BaseModel;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Customer extends BaseModel
{
    use HasFactory;

    protected $fillable = [
        'salla_id',
        'salla_store_id',
        'first_name',
        'last_name',
        'email',
        'mobile',
        'gender',
        'city',
        'country',
        'addresses',
        'extra_attributes',
        'source_updated_at',
        'synced_at',
        'sync_status',
    ];

    protected $hidden = [
        'email',
        'mobile',
    ];

    protected function casts(): array
    {
        return [
            'gender' => Gender::class,
            'sync_status' => SyncStatus::class,
            'addresses' => 'array',
            'extra_attributes' => 'array',
            'source_updated_at' => 'datetime',
            'synced_at' => 'datetime',
        ];
    }

    public function orders(): HasMany
    {
        return $this->hasMany(Order::class, 'customer_id');
    }
}
