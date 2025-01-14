<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Customer extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'city',
        'street',
        'zip',
        'phone',
        'contact_name',
        'bank_account_nr',
        'tax_number',
        'booking_id'
    ];

    public function deliveryAddresses()
    {
        return $this->hasMany(DeliveryAddress::class);
    }
    public function customPrices()
    {
        return $this->hasMany(CustomerProductPrice::class);
    }
    // In Customer.php model
    public function orders()
    {
        return $this->hasMany(Order::class);
    }

    public function productMappings()
    {
        return $this->hasMany(CustomerProductMapping::class);
    }


}

