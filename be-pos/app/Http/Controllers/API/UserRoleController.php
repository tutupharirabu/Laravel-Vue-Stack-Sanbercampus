<?php

namespace App\Http\Controllers\API;

use App\Models\Role;
use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class UserRoleController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // Ambil user yang sedang login
        $currentUser = Auth::guard('api')->user();

        // Pastikan user yang sedang login memiliki business_id
        if (!$currentUser->business_id) {
            return response()->json([
                'message' => 'Anda belum terhubung dengan bisnis mana pun. Tidak dapat melihat daftar pengguna.',
            ], 403);
        }

        // Ambil daftar user dengan 3 role dari bisnis yang sama
        $users = User::select('users.*', 'roles.role_name') // Pilih kolom yang relevan
            ->join('roles', 'roles.role_id', '=', 'users.role_id') // Join ke tabel roles
            ->where('users.business_id', $currentUser->business_id) // Filter berdasarkan business_id
            ->whereIn('roles.role_name', ['admin', 'owner', 'cashier']) // Filter berdasarkan role
            ->orderByRaw("
            CASE
                WHEN roles.role_name = 'owner' THEN 1
                WHEN roles.role_name = 'admin' THEN 2
                WHEN roles.role_name = 'cashier' THEN 3
                ELSE 4
            END
        ") // Prioritaskan berdasarkan role_name
            ->orderBy('users.created_at', 'desc') // Urutkan berdasarkan created_at
            ->paginate(10); // Pagination untuk 10 data per halaman

        return response()->json([
            'message' => 'Daftar pengguna berhasil diambil.',
            'data' => $users,
        ], 200);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // Ambil user yang sedang login
        $currentUser = Auth::guard('api')->user();

        // Pastikan user yang sedang login memiliki business_id
        if (!$currentUser->business_id) {
            return response()->json([
                'message' => 'Anda belum terhubung dengan bisnis mana pun. Tidak dapat menambahkan pengguna baru.',
            ], 403);
        }

        // Validasi input
        $request->validate([
            'full_name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'phone_number' => 'required|unique:users,phone_number',
            'password' => 'required|min:6',
            'role_name' => 'required|in:owner,admin,cashier', // Validasi role_name
        ]);

        // Dapatkan role_id berdasarkan role_name
        $role = Role::where('role_name', $request->role_name)->first();

        if (!$role) {
            return response()->json([
                'message' => 'Role yang dipilih tidak valid.',
            ], 422);
        }

        // Buat user baru
        $user = User::create([
            'full_name' => $request->full_name,
            'email' => $request->email,
            'phone_number' => $request->phone_number,
            'password' => Hash::make($request->password),
            'role_id' => $role->role_id,
            'business_id' => $currentUser->business_id,
        ]);

        return response()->json([
            'message' => 'Pengguna berhasil ditambahkan.',
            'data' => $user
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        // Ambil pengguna berdasarkan ID
        $user = User::with('role')->findOrFail($id);

        // Cek apakah user ditemukan
        if (!$user) {
            return response()->json([
                'message' => 'Pengguna tidak ditemukan.',
            ], 404);
        }

        return response()->json([
            'message' => 'Data Pengguna berhasil diambil.',
            'data' => $user,
        ], 200);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        try {
            // Ambil pengguna berdasarkan ID
            $user = User::findOrFail($id);

            // Validasi input
            $request->validate([
                'full_name' => 'sometimes|string|max:255',
                'email' => 'sometimes|email|unique:users,email,' . $id . ',user_id',
                'phone_number' => 'sometimes|unique:users,phone_number,' . $id . ',user_id',
                'password' => 'nullable|min:6',
                'role_name' => 'sometimes|in:owner,admin,cashier', // Validasi role_name
            ]);

            // Siapkan data untuk update
            $updateData = [
                'full_name' => $request->full_name,
                'email' => $request->email,
                'phone_number' => $request->phone_number,
            ];

            // Update password jika ada
            if ($request->filled('password')) {
                $updateData['password'] = Hash::make($request->password);
            }

            // Update role jika ada
            if ($request->filled('role_name')) {
                $role = Role::where('role_name', $request->role_name)->first();
                if (!$role) {
                    return response()->json([
                        'message' => 'Role yang dipilih tidak valid.',
                    ], 422);
                }
                $updateData['role_id'] = $role->role_id;
            }

            // Perbarui data pengguna
            $user->update($updateData);

            return response()->json([
                'message' => 'Data Pengguna berhasil diperbarui.',
                'data' => $user
            ], 200);

        } catch (ModelNotFoundException $e) {
            return response()->json([
                'message' => 'Pengguna tidak ditemukan.',
            ], 404);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function bulkDestroy(Request $request)
    {
        $request->validate([
            'ids' => 'required|array',
            'ids.*' => 'uuid|exists:users,user_id',
        ]);

        try {
            User::whereIn('user_id', $request->ids)->delete();

            return response()->json([
                'message' => 'Users deleted successfully.',
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to delete products.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

}
