<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class WarehouseProduct extends Model
{
    //

    use SoftDeletes;
    protected $fillable = [
        'warehouse_id',
        'product_id',
        'stock',
    ];
}
