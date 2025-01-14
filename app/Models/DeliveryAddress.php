<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

    
class DeliveryAddress extends Model
{
    use HasFactory, SoftDeletes;
    
    protected $fillable = ['customer_id', 'city', 'street', 'zip'];

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }
}

