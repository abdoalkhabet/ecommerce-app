<?php

namespace App\Models;

use App\Models\Product;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Review extends Model
{
    use HasFactory;
    protected $fillable = ['product_id', 'review', 'rating'];
    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}
