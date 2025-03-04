<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ConfigurableProductComponent extends Model
{
    protected $table = 'configurable_products_components'; // Specify the table name

    protected $fillable = [
        'configurable_product_id',
        'simple_product_id',
        'quantity',
    ];

    // Relationship with Product (simple product)
    public function simpleProduct()
    {
        return $this->belongsTo(Product::class, 'simple_product_id');
    }

    // Relationship with Product (configurable product)
    public function configurableProduct()
    {
        return $this->belongsTo(Product::class, 'configurable_product_id');
    }
}
