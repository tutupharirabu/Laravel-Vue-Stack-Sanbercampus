<?php

namespace App\Models;

use App\Models\Category;
use App\Models\ProductVariant;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Product extends Model
{
    use HasFactory;

    protected $primaryKey = 'product_id';
    protected $table = 'products';
    protected $fillable = ['product_name', 'sku', 'status', 'price', 'gender','description', 'photo_product', 'category_id'];

    public function category()
    {
        return $this->belongsTo(Category::class, 'category_id');
    }

    public function productVariants()
{
    return $this->hasMany(ProductVariant::class, 'product_id');
}

}
