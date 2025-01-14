<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CuttingList extends Model
{
    use HasFactory;

    protected $fillable = ['daily_number', 'list_date'];

    protected static function boot()
{
    parent::boot();

    static::creating(function ($cuttingList) {
        // Automatically set the daily number based on the count of records for today
        $today = now()->format('Y-m-d');
        $cuttingList->daily_number = CuttingList::whereDate('list_date', $today)->count() + 1;
    });
}

    public function selections()
    {
        return $this->hasMany(CuttingSelection::class);
    }
    public function orderProduct()
    {
        return $this->belongsTo(OrderProduct::class, 'order_product_id');
    }

    public function cuttingList()
    {
        return $this->belongsTo(CuttingList::class);
    }
}

