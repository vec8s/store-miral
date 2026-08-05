<?php

declare(strict_types=1);

namespace App\Domains\Catalog\Models;

use App\Shared\Models\BaseModel;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProductOptionValue extends BaseModel
{
    use HasFactory;

    protected $fillable = [
        'product_option_id',
        'value',
        'display_value',
        'color_code',
        'image_url',
        'sort_order',
    ];

    protected function casts(): array
    {
        return [
            'sort_order' => 'integer',
        ];
    }

    public function option(): BelongsTo
    {
        return $this->belongsTo(ProductOption::class, 'product_option_id');
    }
}
