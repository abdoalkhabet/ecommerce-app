<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;
    protected $fillable = [
        'name',
        'description',
        'price',
        'description',
        'oldPrice',
        'quantity',
        'inStock',
        'category_id',
        'discount'
    ];
    public function category()
    {
        return $this->belongsTo(Category::class);
    }
    public function isInStock()
    {
        return $this->quantity > 0;
    }
}