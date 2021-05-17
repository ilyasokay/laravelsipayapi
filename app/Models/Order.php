<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;

    protected $fillable = [
        'status',
        'payment_order_no',
        'user_id',
        'address_id'
    ];

    const STATUS_PAYMENT_WAITING = 'payment_waiting';
    const STATUS_PAYMENT_SUCCESS = 'payment_success';
    const STATUS_REFUND = 'refund';

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function address()
    {
        return $this->belongsTo(Address::class);
    }

    public function invoice()
    {
        return $this->hasOne(Bill::class);
    }

    public function item()
    {
        return $this->hasOne(OrderItem::class);
    }

    public function items()
    {
        return $this->hasMany(OrderItem::class);
    }
}
