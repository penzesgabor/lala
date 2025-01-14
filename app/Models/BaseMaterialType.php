<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BaseMaterialType extends Model
{
    use HasFactory;

    protected $fillable = ['name'];

    public function productGroups()
{
    return $this->hasMany(ProductGroup::class, 'base_material_types_id');
}
}
