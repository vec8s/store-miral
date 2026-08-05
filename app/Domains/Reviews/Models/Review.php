<?php

declare(strict_types=1);

namespace App\Domains\Reviews\Models;

use App\Domains\Catalog\Models\Product;
use App\Domains\Identity\Models\User;
use App\Domains\Reviews\Enums\ReviewStatus;
use App\Shared\Models\BaseModel;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Review extends BaseModel
{
    use HasFactory;
    use SoftDeletes;

    protected $fillable = [
        'user_id',
        'product_id',
        'rating',
        'title',
        'content',
        'status',
        'is_verified_purchase',
        'helpful_count',
        'unhelpful_count',
        'reviewer_name',
        'reviewer_email',
        'ip_address',
        'admin_response',
        'approved_at',
        'approved_by_id',
        'created_by_id',
        'updated_by_id',
    ];

    protected function casts(): array
    {
        return [
            'rating' => 'integer',
            'status' => ReviewStatus::class,
            'is_verified_purchase' => 'boolean',
            'helpful_count' => 'integer',
            'unhelpful_count' => 'integer',
            'approved_at' => 'datetime',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function images(): HasMany
    {
        return $this->hasMany(ReviewImage::class)->orderBy('sort_order');
    }

    public function replies(): HasMany
    {
        return $this->hasMany(ReviewReply::class);
    }

    public function scopeApproved($query)
    {
        return $query->where('status', ReviewStatus::Approved->value);
    }

    public function scopePending($query)
    {
        return $query->where('status', ReviewStatus::Pending->value);
    }
}
