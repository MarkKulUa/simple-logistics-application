<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;

class Warehouse extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'address',
        'supplier_id',
    ];

    public function suplier(): BelongsTo
    {
        return $this->belongsTo(Supplier::class);
    }

    public function products(): HasManyThrough
    {
        return $this->hasManyThrough(
            Product::class,
            WarehouseProduct::class,
            'warehouse_id',
            'product_id',
            'id',
            'product_id'
        );
    }
}
