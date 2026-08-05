<?php

declare(strict_types=1);

namespace App\Shared\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Carbon;

/**
 * @property int $id
 * @property Carbon $created_at
 * @property Carbon $updated_at
 *
 * @mixin \Illuminate\Database\Eloquent\Builder
 */
abstract class BaseModel extends AbstractModel
{
    use HasFactory;

    protected $guarded = ['id'];

    public function getRouteKeyName(): string
    {
        return 'id';
    }

    protected function serializeDate(\DateTimeInterface $date): string
    {
        return Carbon::instance(\DateTime::createFromInterface($date))
            ->setTimezone(config('app.timezone'))
            ->toIso8601String();
    }

    public function scopeCreatedBetween($query, Carbon $start, Carbon $end)
    {
        return $query->whereBetween('created_at', [$start, $end]);
    }

    public function scopeUpdatedBetween($query, Carbon $start, Carbon $end)
    {
        return $query->whereBetween('updated_at', [$start, $end]);
    }
}
