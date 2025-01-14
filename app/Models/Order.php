<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;

    protected $fillable = [
        'customer_id',
        'ordering_date',
        'delivery_date',
        'notes',
        'delivery_address_id',
        'production_date',
        'isbilled',
        'isdelivered',
        'imported',
    ];

    // Relationships
    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function deliveryAddress()
    {
        return $this->belongsTo(DeliveryAddress::class)->withTrashed();
    }

    public function products()
    {
        return $this->hasMany(OrderProduct::class, 'order_id');
    }
    public function orderProducts()
    {
        return $this->hasMany(OrderProduct::class);
    }
}
