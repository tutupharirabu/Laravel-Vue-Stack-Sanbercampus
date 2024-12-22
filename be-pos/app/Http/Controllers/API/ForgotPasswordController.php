<?php

namespace App\Http\Controllers\API;

use App\Models\User;
use App\Models\OTPCode;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Models\ForgotPassword;
use Illuminate\Support\Carbon;
use App\Http\Controllers\Controller;
use App\Mail\ForgotPasswordMailSend;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;

class ForgotPasswordController extends Controller
{
    public function forgotPassword(Request $request)
    {
        $request->validate([
            'email' => 'required|email|max:255',
        ]);

        $user = User::where('email', $request->email)->first();

        if (!$user) {
            return response()->json(['error' => 'Email tidak ditemukan!'], 404);
        }

        if (!$user->email_verified_at) {
            return response()->json(['error' => 'Email belum diverifikasi.'], 403);
        }

        $user->generateOtpCodeData($user);
        Mail::to($user->email)->send(new ForgotPasswordMailSend($user));

        return response()->json([
            'message' => 'OTP telah dikirim ke email Anda!',
        ], 200);
    }

    public function verifyOtpForgotPassword(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'otp_code' => 'required|numeric'
        ]);

        $user = User::where('email', $request->email)->first();

        if (!$user) {
            return response()->json(['error' => 'Email tidak ditemukan.'], 404);
        }

        $otp = OTPCode::where('user_id', $user->user_id)
            ->where('otp_code', $request->otp_code)
            ->first();

        if (!$otp) {
            return response()->json(['error' => 'Kode OTP tidak valid.'], 400);
        }

        if (Carbon::now() > $otp->valid_until) {
            return response()->json(['error' => 'Kode OTP sudah kadaluarsa.'], 400);
        }

        // Hasilkan token reset password
        $resetToken = Str::random(60);

        // Simpan token reset password di tabel forgot_passwords
        ForgotPassword::create([
            'token' => $resetToken,
            'user_id' => $user->user_id,
            'valid_until' => Carbon::now()->addMinutes(15), // Token valid selama 15 menit
        ]);

        return response()->json([
            'message' => 'Verifikasi OTP berhasil~',
            'reset_token' => $resetToken,
        ], 200);
    }

    public function resetPassword(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'reset_token' => 'required',
            'password' => 'required|min:8|confirmed',
            'password_confirmation' => 'required'
        ]);

        // Cari user berdasarkan email
        $user = User::where('email', $request->email)->first();

        if (!$user) {
            return response()->json(['error' => 'Email tidak ditemukan.'], 404);
        }

        // Validasi reset token dari tabel forgot_passwords
        $forgotPassword = ForgotPassword::where('user_id', $user->user_id)
            ->where('token', $request->reset_token)
            ->first();

        if (!$forgotPassword) {
            return response()->json(['error' => 'Token reset password tidak valid.'], 400);
        }

        // Periksa apakah token reset sudah kadaluarsa
        if (Carbon::now() > $forgotPassword->valid_until) {
            return response()->json(['error' => 'Token reset password sudah kadaluarsa.'], 400);
        }

        // Update password user
        $user->password = Hash::make($request->password);
        $user->save();

        // Hapus token reset password setelah digunakan
        $forgotPassword->delete();

        return response()->json([
            'message' => 'Password berhasil diubah~'
        ], 200);
    }
}
