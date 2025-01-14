<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CuttingSelection extends Model
{
    use HasFactory;

    protected $fillable = ['order_product_id', 'cutting_list_id', 'trolley_id'];

    public function cuttingList()
    {
        return $this->belongsTo(CuttingList::class);
    }
    public function orderProduct()
    {
        return $this->belongsTo(OrderProduct::class, 'order_product_id');
    }
    public function trolley()
    {
        return $this->belongsTo(Trolley::class);
    }
}
