<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OTPCode extends Model
{
    use HasFactory;

    protected $primaryKey = 'otp_code_id';
    protected $table = 'otp_codes';

    protected $fillable = [
        'otp_code',
        'user_id',
        'valid_until',
    ];

    public $timestamps = false;
}
