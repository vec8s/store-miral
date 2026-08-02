<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Cart extends Model
{
    use HasFactory;

    /**
     * الحقول المسموح بإدخالها جملة واحدة (Mass Assignment).
     */
    protected $fillable = [
        'user_id',
        'session_id',
    ];

    /* =========================================================================
     | العلاقات (Relations)
     | ========================================================================= */

    /**
     * العميل صاحب السلة (إن وجد).
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * بنود/منتجات السلة.
     */
    public function items(): HasMany
    {
        return $this->hasMany(CartItem::class);
    }

    /* =========================================================================
     | النطاقات والوظائف المساعدة (Scopes & Helpers)
     | ========================================================================= */

    /**
     * حساب إجمالي السلة المباشر من العناصر.
     */
    public function getTotalAttribute(): float
    {
        return (float) $this->items->sum('total_price');
    }

    /**
     * حساب إجمالي عدد القطع في السلة.
     */
    public function getTotalQuantityAttribute(): int
    {
        return (int) $this->items->sum('quantity');
    }
}
