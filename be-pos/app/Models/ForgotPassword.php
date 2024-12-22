<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ForgotPassword extends Model
{
    use HasFactory, HasUuids;

    protected $primaryKey = 'forgot_password_id';
    protected $table = 'forgot_passwords';

    protected $fillable = [
        'token',
        'user_id',
        'valid_until',
    ];

    public $timestamps = false;
}
