<?php

namespace App\Models;

use App\Models\Review;
use App\Models\Category;
use App\Helpers\MediaHelper;
use Spatie\MediaLibrary\HasMedia;
use Illuminate\Database\Eloquent\Model;
use Spatie\MediaLibrary\InteractsWithMedia;
use Illuminate\Database\Eloquent\Factories\HasFactory;


class Product extends Model implements HasMedia
{
    use HasFactory, InteractsWithMedia;
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
    public function mainImage()
    {
        return MediaHelper::mediaRelationship($this, 'product_main_image');
    }

    public function otherImages()
    {
        $media = MediaHelper::mediaRelationship($this, 'product_other_images');

        if ($media) {
            return $media;
        } else {
            return null;
        }
    }
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
