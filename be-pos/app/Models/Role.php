<?php

namespace App\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Role extends Model
{
    use HasFactory;
    protected $primaryKey = 'role_id';
    protected $table = 'roles';

    protected $fillable = [
        'role_name',
    ];

    public function list_user()
    {
        return $this->hasMany(User::class, 'role_id', 'role_id');
    }
}
