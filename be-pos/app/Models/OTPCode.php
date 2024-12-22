<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class OTPCode extends Model
{
    use HasFactory, HasUuids;

    protected $primaryKey = 'otp_code_id';
    protected $table = 'otp_codes';

    protected $fillable = [
        'otp_code',
        'user_id',
        'valid_until',
    ];

    public $timestamps = false;
}
