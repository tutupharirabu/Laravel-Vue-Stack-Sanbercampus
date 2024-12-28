<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    use HasFactory, HasUuids;

    protected $primaryKey = 'transaction_id';
    protected $table = 'transactions';

    protected $fillable = [
        'user_id',
        'cashier_id',
        'status',
        'amount',
        'items',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'user_id');
    }

    public function cashier()
    {
        return $this->belongsTo(User::class, 'cashier_id', 'user_id');
    }

    public function items() {
        return $this->hasMany(Product::class, 'product_id', 'items');
    }
}
