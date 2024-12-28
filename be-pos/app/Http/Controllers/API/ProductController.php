<?php

namespace App\Http\Controllers\API;

use App\Models\Product;
use Cloudinary\Cloudinary;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;

class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $products = Product::all();

        // Transform the data to include only the category name
        $transformedProducts = $products->map(function ($product) {
            return [
                'id' => $product->product_id,
                'product_name' => $product->product_name,
                'sku' => $product->sku,
                'price' => $product->price,
                'gender' => $product->gender,
                'description' => $product->description,
                'photo_product' => $product->photo_product,
                'status' => $product->status,
            ];
        });

        return response()->json([
            'message' => 'Data produk berhasil diambil.',
            'data' => $transformedProducts
        ], 200);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'product_name' => 'required|string|max:255',
            'sku' => 'required|string|unique:products,sku|max:255',
            'price' => 'required|numeric|min:0',
            'gender' => 'required|in:male,female,unisex',
            'description' => 'nullable|string',
            'photo_product' => 'nullable|array', // Validasi sebagai array
            'photo_product.*' => 'file|image|max:5120|mimes:jpg,jpeg,png', // Validasi setiap file dalam array
            'status' => 'boolean',
            'stock' => 'required|integer|min:0',
        ]);

        // Upload foto ke Cloudinary
        $uploadedPhotos = [];
        if ($request->hasFile('photo_product')) {
            foreach ($request->file('photo_product') as $photo) {
                try {
                    $cloudinary = new Cloudinary();
                    $uploadedFileUrl = $cloudinary->uploadApi()->upload(
                        $photo->getRealPath(),
                        ['folder' => 'web-pos/products']
                    )['secure_url'];

                    Log::info('Uploaded File URL:', ['url' => $uploadedFileUrl]);
                    $uploadedPhotos[] = $uploadedFileUrl;
                } catch (\Exception $e) {
                    Log::error('Error uploading to Cloudinary:', ['message' => $e->getMessage()]);
                    return response()->json([
                        'message' => 'Gagal mengunggah foto produk ke Cloudinary: ' . $e->getMessage(),
                    ], 500);
                }
            }
        }

        if (empty($uploadedPhotos)) {
            return response()->json([
                'message' => 'Foto produk wajib diunggah.',
            ], 400);
        }

        try {
            $product = Product::create(array_merge(
                $request->only([
                    'product_name',
                    'sku',
                    'price',
                    'gender',
                    'description',
                    'status',
                    'stock',
                ]),
                ['photo_product' => json_encode($uploadedPhotos)] // Konversi array menjadi JSON
            ));

            return response()->json([
                'message' => 'Produk berhasil dibuat.',
                'data' => $product,
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Terjadi kesalahan saat menyimpan produk.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $product = Product::findOrFail($id);

        $request->validate([
            'product_name' => 'sometimes|string|max:255',
            'sku' => 'sometimes|string|unique:products,sku,' . $product->product_id,
            'price' => 'sometimes|numeric|min:0',
            'gender' => 'sometimes|in:male,female,unisex',
            'description' => 'nullable|string',
            'photo_product' => 'nullable|array',
            'photo_product.*' => 'file|image|max:5120', // Maksimal 5MB per file
            'status' => 'sometimes|boolean',
            'stock' => 'sometimes|integer|min:0',
        ]);

        // Upload foto baru ke Cloudinary jika ada
        $uploadedPhotos = $product->photo_product ? json_decode($product->photo_product, true) : [];
        if ($request->hasFile('photo_product')) {
            foreach ($request->file('photo_product') as $photo) {
                try {
                    $cloudinary = new Cloudinary();
                    $uploadedFileUrl = $cloudinary->uploadApi()->upload(
                        $photo->getRealPath(),
                        [
                            'folder' => 'web-pos/products',
                        ]
                    )['secure_url'];

                    $uploadedPhotos[] = $uploadedFileUrl;
                } catch (\Exception $e) {
                    return response()->json([
                        'message' => 'Gagal mengunggah foto produk ke Cloudinary: ' . $e->getMessage(),
                    ], 500);
                }
            }
        }

        try {
            // Update produk
            $product->update(array_merge(
                $request->only([
                    'product_name',
                    'sku',
                    'price',
                    'category_id',
                    'gender',
                    'description',
                    'status',
                    'stock',
                ]),
                ['photo_product' => json_encode($uploadedPhotos)] // Simpan sebagai JSON
            ));

            return response()->json([
                'message' => 'Produk berhasil diperbarui.',
                'data' => $product,
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Terjadi kesalahan saat memperbarui produk.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $product = Product::findOrFail($id);

        $product->delete();

        return response()->json([
            'message' => 'Product deleted successfully.'
        ], 200);
    }
}
