<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'type',
        'base_material_type_id',
        'vat_id',
        'product_group_id',
        'english_name',
        'weight_per_squaremeter',
        'liseccode',
        'base_price'
    ];

    public function baseMaterialType()
    {
        return $this->belongsTo(BaseMaterialType::class);
    }

    public function vat()
    {
        return $this->belongsTo(Vat::class);
    }

    public function productGroup()
    {
        return $this->belongsTo(ProductGroup::class);
    }

    public function components()
    {
        return $this->belongsToMany(
            Product::class,
            'configurable_product_components',
            'configurable_product_id',
            'simple_product_id'
        )->withPivot('quantity');
    }

    public function calculateConfigurablePrice()
{
    $basePrice = $this->components->sum(function ($component) {
        return $component->pivot->quantity * $component->base_price;
    });

    return $this->base_price ?? $basePrice; // Use custom base price if set
}

public function customerPrice($customerId)
{
    $customPrice = $this->customerPrices()->where('customer_id', $customerId)->first();

    return $customPrice->custom_price ?? $this->base_price;
}

public function customerPrices()
{
    return $this->hasMany(CustomerProductPrice::class);
}
public function priceHistories()
{
    return $this->hasMany(PriceHistory::class);
}

public function customerMappings()
{
    return $this->hasMany(CustomerProductMapping::class);
}


}

