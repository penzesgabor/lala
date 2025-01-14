<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductGroup extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'base_material_types_id'];

    public function baseMaterialType()
    {
        return $this->belongsTo(BaseMaterialType::class, 'base_material_types_id');
    }
}

