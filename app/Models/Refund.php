<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Refund extends Model
{
    use HasFactory;

    protected $table = 'refunds';

    protected $fillable = [
        'order_id',
        'user_id',
        'amount',
        'reason',
        'status',
    ];

    /**
     * علاقة المرتجع بالطلب
     */
    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    /**
     * علاقة المرتجع بالمستخدم (العميل)
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
