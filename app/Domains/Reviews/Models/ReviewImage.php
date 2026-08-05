<?php

declare(strict_types=1);

namespace App\Domains\Reviews\Models;

use App\Shared\Models\BaseModel;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ReviewImage extends BaseModel
{
    use HasFactory;

    protected $fillable = [
        'review_id',
        'url',
        'thumbnail_url',
        'sort_order',
    ];

    protected function casts(): array
    {
        return [
            'sort_order' => 'integer',
        ];
    }

    public function review(): BelongsTo
    {
        return $this->belongsTo(Review::class);
    }
}
