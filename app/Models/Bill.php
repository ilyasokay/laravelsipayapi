<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Bill extends Model
{
    use HasFactory;

    protected $fillable = [
        'description',
        'bill_address1',
        'bill_address2',
        'bill_city',
        'bill_postcode',
        'bill_state',
        'bill_country',
        'bill_email',
        'bill_phone',
        'order_id',
    ];

    public function order()
    {
        return $this->belongsTo(Order::class);
    }
}
