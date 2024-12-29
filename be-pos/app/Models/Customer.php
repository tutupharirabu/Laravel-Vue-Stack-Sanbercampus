<?php

namespace App\Models;

use App\Models\Transaction;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Customer extends Model
{
    use HasFactory, HasUuids;

    protected $primaryKey = 'customer_id';
    protected $table = 'customers';

    protected $fillable = [
        'email',
        'full_name',
        'phone_number',
        'address',
    ];

    public function transactions()
    {
        return $this->hasMany(Transaction::class, 'customer_id', 'customer_id');
    }
}
