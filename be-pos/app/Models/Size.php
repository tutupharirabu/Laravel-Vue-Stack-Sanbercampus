<?php

namespace App\Models;

use App\Models\ProductVariant;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Size extends Model
{
    use HasFactory, HasUuids;

    protected $primaryKey = 'size_id';
    protected $table = 'sizes';
    protected $fillable = ['size_name'];

    public function product_variants()
    {
        return $this->hasMany(ProductVariant::class, 'size_id');
    }
}
