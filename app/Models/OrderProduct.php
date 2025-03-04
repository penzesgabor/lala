<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrderProduct extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_id',
        'product_id',
        'height',
        'width',
        'divider_id',
        'divider_length',
        'dividercross',
        'dividerend',
        'gasfilling',
        'extracharge',
        'calculated_price',
        'agreed_price',
        'flowmeter',
        'squaremeter',
        'customers_order_text',
        'notes',
        'barcode', 
        'customer_product_name', 
        'randomid',
    ];

    // Relationships
    public function order()
    {
        return $this->belongsTo(Order::class, 'order_id');
    }

    public function product()
    {
        return $this->belongsTo(Product::class, 'product_id');
    }
    public function cuttingSelections()
    {
        return $this->hasMany(CuttingSelection::class, 'order_product_id');
    }
    
    
}
