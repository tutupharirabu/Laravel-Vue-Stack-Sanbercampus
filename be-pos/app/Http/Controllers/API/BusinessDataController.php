<?php

namespace App\Http\Controllers\API;

use App\Models\Business;
use Cloudinary\Cloudinary;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class BusinessDataController extends Controller
{
    public function storeBusinessInfo(Request $request)
    {
        // Validasi input
        $request->validate([
            'business_name' => 'required|string|max:255|unique:businesses',
            'city' => 'required|string|max:255',
            'district' => 'required|string|max:255',
            'postal_code' => 'required|string|max:10',
            'photo_id_card' => 'required|file|mimes:jpg,png,pdf|max:2048',
        ]);

        // Ambil user yang sedang login
        $user = Auth::guard('api')->user();

        // Validasi role user (hanya Owner yang dapat membuat bisnis)
        if (!in_array($user->role->role_name, ['owner'])) {
            return response()->json([
                'message' => 'Hanya pengguna dengan peran Owner atau Admin yang dapat membuat bisnis.',
            ], 403);
        }

        // Upload file ID Card ke Cloudinary
        try {
            $cloudinary = new Cloudinary();
            $uploadedFileUrl = $cloudinary->uploadApi()->upload(
                $request->file('photo_id_card')->getRealPath(),
                [
                    'folder' => 'web-pos/ktp-for-business',
                ]
            )['secure_url'];
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Gagal mengunggah KTP ke Cloudinary. Silakan coba lagi.',
            ], 500);
        }

        // Simpan data bisnis ke database
        $business = Business::create([
            'business_name' => $request->business_name,
            'city' => $request->city,
            'district' => $request->district,
            'postal_code' => $request->postal_code,
            'photo_id_card' => $uploadedFileUrl,
        ]);

        $user->business_id = $business->business_id;
        $user->save();

        // Response sukses
        return response()->json([
            'message' => 'Informasi bisnis berhasil disimpan.',
            'data' => $business,
        ], 201);
    }
}
