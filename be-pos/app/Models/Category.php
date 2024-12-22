<?php

namespace App\Models;

use App\Models\Product;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Category extends Model
{
    use HasFactory, HasUuids;

    protected $primaryKey = 'category_id';
    protected $table = 'categories';
    protected $fillable = ['category_name'];

    public function products()
    {
        return $this->hasMany(Product::class, 'category_id');
    }
}
