<?php

namespace App\Models;

use Carbon\Carbon;
use App\Models\Role;
use App\Models\Profile;
use App\Models\OTPCode;
use Tymon\JWTAuth\Contracts\JWTSubject;
use Illuminate\Foundation\Auth\User as Authenticable;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class User extends Authenticable implements JWTSubject
{
    use HasFactory;

    public static function boot() {
        parent::boot();

        static::created(function ($model) {
            $model->generateOTPCodeData($model);
        });
    }

    public function generateOTPCodeData($user) {
        $randomNumber = mt_rand(100000, 999999);
        $now = Carbon::now();

        $otp = OTPCode::updateOrCreate(
            ['user_id' => $user->user_id],
            [
                'otp_code' => $randomNumber,
                'valid_until' => $now->addMinutes(5),
            ]
        );
    }

    protected $primaryKey = 'user_id';
    protected $table = 'users';
    protected $fillable = ['full_name', 'email', 'password', 'address', 'phone_number','role_id'];
    protected $hidden = ['password'];

    public function getJWTIdentifier() {
        return $this->getKey();
    }

    public function getJWTCustomClaims() {
        return [];
    }

    public function profile() {
        return $this->HasOne(Profile::class, 'user_id');
    }

    public function role()
    {
        return $this->belongsTo(Role::class, 'role_id');
    }

    public function otpCode()
    {
        return $this->hasOne(OTPCode::class, 'users_id');
    }
}
