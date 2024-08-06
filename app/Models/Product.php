<?php

namespace App\Models;

use App\Models\Review;
use App\Models\Category;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

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
    public function reviews()
    {
        return $this->hasMany(Review::class);
    }

    public function averageRating()
    {
        return $this->reviews()->avg('rating');
    }
    public function isInStock()
    {
        return $this->quantity > 0;
    }
}