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

#   public function components()
#{
#    return $this->hasMany(ConfigurableProductComponent::class, 'configurable_product_id');
#}
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

    // Relationship with CustomerProductPrice (for customer-specific prices)
    public function customPrices()
    {
        return $this->hasMany(CustomerProductPrice::class, 'product_id');
    }

    // Calculate the product price
    public function getPrice($customerPrices = [])
    {
        // 1. Check for a customer-specific price
        if (isset($customerPrices[$this->id])) {
            return (float) $customerPrices[$this->id];
        }

        // 2. Check for base price
        if ($this->base_price !== null) {
            return (float) $this->base_price;
        }

        // 3. Calculate for configurable products
        if ($this->type === 'configurable') {
            $price = $this->components->sum(function ($component) use ($customerPrices) {
                // Check for customer-specific price for each component
                if (isset($customerPrices[$component->simple_product_id])) {
                    return (float) $customerPrices[$component->simple_product_id] * $component->quantity;
                }

                // Default to base price if no customer-specific price
                return (float) ($component->simpleProduct->base_price ?? 0) * $component->quantity;
            });

            return $price;
        }

        // 4. Default for simple products without base price
        return 0;
    }
    public function baseMaterial()
    {
        return $this->belongsTo(BaseMaterial::class, 'base_material_id');
    }

    public function scopeConfigurable($query)
    {
        return $query->where('type', 'configurable');
    }
}

