<?php

declare(strict_types=1);

namespace App\Domains\Catalog\Models;

use App\Shared\Models\BaseModel;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CustomizationField extends BaseModel
{
    use HasFactory;

    protected $table = 'customization_fields';

    protected $fillable = [
        'product_id',
        'key',
        'label',
        'type',
        'required',
        'max_length',
        'validation_rules',
        'options_json',
        'sort_order',
    ];

    protected function casts(): array
    {
        return [
            'required' => 'boolean',
            'max_length' => 'integer',
            'validation_rules' => 'array',
            'options_json' => 'array',
            'sort_order' => 'integer',
        ];
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }
}
