<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BaseMaterial extends Model
{
    protected $table = 'base_materials';

    // Define the inverse relationship with Product
    public function products()
    {
        return $this->hasMany(Product::class, 'base_material_id');
    }
}
