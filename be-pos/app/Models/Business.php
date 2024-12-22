<?php

namespace App\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Business extends Model
{
    use HasFactory, HasUuids;

    protected $primaryKey = 'business_id';
    protected $table = 'businesses';
    protected $fillable = ['business_name', 'city', 'district', 'postal_code', 'photo_id_card'];

    public function users()
    {
        return $this->hasMany(User::class, 'business_id');
    }
}
