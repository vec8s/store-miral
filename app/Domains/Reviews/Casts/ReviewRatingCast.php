<?php

declare(strict_types=1);

namespace App\Domains\Reviews\Casts;

use App\Shared\Exceptions\InvalidPayloadException;
use Illuminate\Contracts\Database\Eloquent\CastsAttributes;
use Illuminate\Database\Eloquent\Model;

final class ReviewRatingCast implements CastsAttributes
{
    public function get(Model $model, string $key, mixed $value, array $attributes): int
    {
        return (int) $value;
    }

    public function set(Model $model, string $key, mixed $value, array $attributes): array
    {
        $rating = (int) $value;

        if ($rating < 1 || $rating > 5) {
            throw InvalidPayloadException::forField(
                'rating',
                'Rating must be between 1 and 5.',
            );
        }

        return [$key => $rating];
    }
}
