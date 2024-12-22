<?php

namespace App\Http\Controllers\API;

use App\Models\Profile;
use Cloudinary\Cloudinary;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class ProfileController extends Controller
{
    public function updateOrCreate(Request $request)
    {
        // Validasi input
        $request->validate([
            'address' => 'sometimes|string|max:255', // Validasi untuk alamat
            'gender' => 'sometimes|in:male,female', // Gender harus sesuai dengan nilai enum
            'photo_profile' => 'nullable|file|mimes:jpg,png|max:2048', // Validasi untuk file foto profil
        ], [
            'string' => ':attribute harus berupa teks.',
            'in' => ':attribute harus berupa male atau female!',
            'file' => ':attribute harus berupa file.',
            'mimes' => ':attribute harus berupa file jpg atau png.',
            'max' => ':attribute tidak boleh lebih dari :max kilobytes.',
        ]);

        // Ambil user yang sedang login
        $currentUser = Auth::guard('api')->user();

        if (!$currentUser) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }

        // Perbarui alamat di tabel users
        $currentUser->update([
            'address' => $request->address,
        ]);

        // Upload file foto profil ke Cloudinary jika ada
        $uploadedFileUrl = $currentUser->profile->photo_profile ?? null; // Gunakan foto lama jika tidak ada file baru
        if ($request->hasFile('photo_profile')) {
            try {
                $cloudinary = new Cloudinary();
                $uploadedFileUrl = $cloudinary->uploadApi()->upload(
                    $request->file('photo_profile')->getRealPath(),
                    [
                        'folder' => 'web-pos/photo-profiles',
                    ]
                )['secure_url'];
            } catch (\Exception $e) {
                return response()->json([
                    'message' => 'Gagal mengunggah foto profil ke Cloudinary. Silakan coba lagi.',
                ], 500);
            }
        }

        // Gunakan updateOrCreate untuk memperbarui atau membuat data profile
        $profile = Profile::updateOrCreate(
            ['user_id' => $currentUser->user_id], // Cari berdasarkan user_id
            [
                'gender' => $request->gender,
                'photo_profile' => $uploadedFileUrl, // URL dari Cloudinary
                'user_id' => $currentUser->user_id,
            ]
        );

        // Kembalikan respons sukses
        return response()->json([
            'message' => "Data profil dan alamat berhasil diperbarui!",
            'data' => [
                'user' => [
                    'address' => $currentUser->address,
                ],
                'profile' => $profile,
            ],
        ], 200);
    }

    public function getProfile()
    {
        $currentUser = Auth::guard('api')->user();
        $profile = Profile::where('user_id', $currentUser->user_id)->first();

        if (!$profile) {
            return response()->json(['message' => 'Profil tidak ditemukan.'], 404);
        }

        return response()->json(['profile' => $profile], 200);
    }
}
