<?php

declare(strict_types=1);

namespace App\Domains\Reviews\Models;

use App\Domains\Identity\Models\User;
use App\Shared\Models\BaseModel;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class ReviewReply extends BaseModel
{
    use HasFactory;
    use SoftDeletes;

    protected $fillable = [
        'review_id',
        'user_id',
        'content',
    ];

    public function review(): BelongsTo
    {
        return $this->belongsTo(Review::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
