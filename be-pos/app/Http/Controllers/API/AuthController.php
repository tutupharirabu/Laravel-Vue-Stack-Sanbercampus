<?php

namespace App\Http\Controllers\API;

use App\Models\Role;
use App\Models\User;
use App\Models\OTPCode;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Mail\RegisterMailSend;
use Illuminate\Support\Carbon;
use Tymon\JWTAuth\Facades\JWTAuth;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
    public function register(Request $request)
    {
        $validatedData = Validator::make($request->all(), [
            'full_name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:users',
            'phone_number' => 'required|string|max:15|unique:users',
            'password' => 'required|min:8|confirmed',
            'password_confirmation' => 'required',
        ]);

        if ($validatedData->fails()) {
            return response()->json(['error' => $validatedData->errors()], 400);
        }

        $roleData = Role::where('role_name', 'owner')->first();

        $user = User::create([
            'full_name' => $request->full_name,
            'email' => $request->email,
            'phone_number' => $request->phone_number,
            'password' => Hash::make($request->password),
            'role_id' => $roleData->role_id,
        ]);

        $user->generateOtpCodeData($user);
        $token = JWTAuth::fromUser($user);

        Mail::to($user->email)->send(new RegisterMailSend($user));

        return response([
            'message' => "Register berhasil! Silahkan cek email untuk verifikasi.",
            "user" => [
                "name" => $request->full_name,
                "email" => $request->email,
            ],
            'token' => $token,
        ], 201);
    }

    public function generateOtpCode(Request $request)
    {
        $request->validate([
            'email' => 'required|email|max:255',
        ]);

        $user = User::where('email', $request->email)->first();

        if (!$user) {
            return response()->json(['error' => 'Email tidak ditemukan!'], 404);
        }

        $user->generateOtpCodeData($user);

        Mail::to($user->email)->send(new RegisterMailSend($user));

        return response([
            'message' => "OTP telah dikirim ke email Anda!",
        ], 200);
    }

    public function verificationEmail(Request $request)
    {
        $request->validate([
            'otp_code' => 'required|numeric',
        ]);

        $otp = OTPCode::where('otp_code', $request->otp_code)->first();

        if (!$otp) {
            return response([
                "message" => "Gagal verifikasi email! (OTP Code TIDAK DITEMUKAN)",
            ], 404);
        }

        if (Carbon::now() > $otp->valid_until) {
            return response([
                "message" => "Gagal verifikasi email! (OTP Code SUDAH KADALUARSA)",
            ], 400);
        }

        $user = User::find($otp->user_id);
        $user->email_verified_at = Carbon::now();
        $user->save();

        $otp->delete();

        return response([
            "message" => "Berhasil verifikasi email!",
        ], 200);
    }

    public function getUser()
    {
        $currentUser = auth()->user();
        $dataUser = User::with('role', 'profile')->find($currentUser->user_id);

        return response()->json([
            'message' => 'Berhasil menampilkan data user!',
            'data' => $dataUser,
        ], 200);
    }

    public function login(Request $request)
    {
        // Validasi input
        $request->validate([
            'email' => 'required|email|max:255',
            'password' => 'required',
            'role' => 'required',
        ]);

        // Ambil kredensial kecuali role
        $credentials = $request->only('email', 'password');

        // Coba login dengan kredensial
        if (!$token = Auth::guard('api')->attempt($credentials)) {
            return response()->json([
                'error' => 'Unauthorized',
                'message' => 'Email atau password salah.',
            ], 401);
        }

        // Ambil data pengguna
        $user = User::where('email', $request->email)
            ->with('role') // Eager load role untuk menghindari query tambahan
            ->first();

        // Verifikasi apakah role yang dipilih cocok dengan role user
        if (!$user->role || strtolower($user->role->role_name) !== strtolower($request->role)) {
            return response()->json([
                'error' => 'Unauthorized',
                'message' => 'Role yang dipilih tidak sesuai.',
            ], 403);
        }

        // Berikan respons login berhasil
        return response()->json([
            "message" => "Login berhasil!",
            "user" => [
                "id" => $user->user_id,
                "name" => $user->full_name,
                "email" => $user->email,
                "role" => Str::ucfirst($request->role),
            ],
            "token" => $token,
        ], 200);
    }

    // public function loginv2(Request $request)
    // {
    //     // Validasi input
    //     $request->validate([
    //         'email' => 'required|email|max:255',
    //         'password' => 'required',
    //         'role' => 'required',
    //     ]);

    //     // Ambil kredensial kecuali role
    //     $credentials = $request->only('email', 'password');

    //     // Coba login dengan kredensial
    //     if (!$token = Auth::guard('api')->attempt($credentials)) {
    //         return response()->json([
    //             'error' => 'Unauthorized',
    //             'message' => 'Email atau password salah.',
    //         ], 401);
    //     }

    //     // Ambil data pengguna
    //     $user = User::where('email', $request->email)
    //         ->with('role') // Eager load role untuk menghindari query tambahan
    //         ->first();

    //     // Verifikasi apakah role yang dipilih cocok dengan role user
    //     if (!$user->role || strtolower($user->role->role_name) !== strtolower($request->role)) {
    //         return response()->json([
    //             'error' => 'Unauthorized',
    //             'message' => 'Role yang dipilih tidak sesuai.',
    //         ], 403);
    //     }

    //     // Berikan respons login berhasil
    //     return response()->json([
    //         "message" => "Login berhasil!",
    //         "user" => [
    //             "id" => $user->user_id,
    //             "name" => $user->full_name,
    //             "email" => $user->email,
    //             "role" => Str::ucfirst($request->role),
    //         ],
    //         "token" => $token,
    //     ], 200);
    // }

    public function logout()
    {
        try {
            Auth::guard('api')->logout();
            return response()->json(['message' => 'Logout berhasil~']);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Logout gagal!'], 500);
        }
    }
}
