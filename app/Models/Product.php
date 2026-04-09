<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Storage;

class Product extends Model
{
    //
    use SoftDeletes;
    protected $fillable = [
        'name',
        'thumbnail',
        'about',
        'price',
        'category_id',
        'is_popular',
    ];

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function warehouses()
    {
        return $this->belongsToMany(Warehouse::class, 'warehouse_products')
            ->withPivot('stock')
            ->withTimestamps();
    }

    public function merchants()
    {
        return $this->belongsToMany(Merchant::class, 'merchant_products')
            ->withPivot('stock')
            ->withTimestamps();
    }   

    public function transactions()
    {
       return $this->hasMany(Transaction::class);
    }

    public function getWarehouseProductStock(): int
    {
        return $this->warehouses()->sum('stock');
    }

    public function getMerchantProductStock(): int
    {
        return $this->merchants()->sum('stock');
    }

    public function getThumbnailAttribute($value)
    {
        if (!$value) {
            return null;
        }

        return url(Storage::url($value));

    }
}
