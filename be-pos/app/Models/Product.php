<?php

namespace App\Models;

use App\Models\Transaction;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Product extends Model
{
    use HasFactory, HasUuids;

    protected $primaryKey = 'product_id';
    protected $table = 'products';
    protected $fillable = ['product_name', 'sku', 'status', 'price', 'gender', 'description', 'photo_product', 'stock'];

    public function transactions()
    {
        return $this->belongsToMany(Transaction::class, 'transaction_details', 'product_id', 'transaction_id');
    }
}
