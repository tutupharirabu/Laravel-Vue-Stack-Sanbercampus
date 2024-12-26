<?php

namespace App\Http\Controllers\API;

use App\Models\Product;
use Cloudinary\Cloudinary;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $products = Product::with(['category', 'productVariants'])->get();

        return response()->json([
            'message' => 'Products retrieved successfully.',
            'data' => $products
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
            'category_id' => 'required|uuid|exists:categories,category_id',
            'gender' => 'required|in:male,female,unisex',
            'description' => 'nullable|string',
            'photo_product' => 'nullable|array',
            'photo_product.*' => 'file|image|max:5120', // Maksimal 5MB per file
            'status' => 'boolean',
            'variants' => 'nullable|array',
            'variants.*.color' => 'required_with:variants|string|max:50',
            'variants.*.stock' => 'required_with:variants|integer|min:0',
            'variants.*.sku' => 'required_with:variants|string|unique:product_variants,sku|max:255',
            'variants.*.size_id' => 'required_with:variants|uuid|exists:sizes,size_id',
        ]);

        // Upload foto ke Cloudinary
        $uploadedPhotos = [];
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
            // Buat produk baru
            $product = Product::create(array_merge(
                $request->only([
                    'product_name',
                    'sku',
                    'price',
                    'category_id',
                    'gender',
                    'description',
                    'status',
                ]),
                ['photo_product' => json_encode($uploadedPhotos)] // Simpan sebagai JSON
            ));

            // Tambahkan varian jika ada
            if ($request->has('variants') && is_array($request->variants)) {
                foreach ($request->variants as $variant) {
                    $product->productVariants()->create($variant);
                }
            }

            return response()->json([
                'message' => 'Product created successfully.',
                'data' => $product->load('productVariants') // Load relasi untuk respons
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
            'sku' => 'sometimes|string|unique:products,sku,' . $product->id,
            'price' => 'sometimes|numeric|min:0',
            'category_id' => 'sometimes|uuid|exists:categories,category_id',
            'gender' => 'sometimes|in:male,female,unisex',
            'description' => 'nullable|string',
            'photo_product' => 'nullable|array',
            'photo_product.*' => 'file|image|max:5120', // Maksimal 5MB per file
            'status' => 'boolean',
            'variants' => 'nullable|array',
            'variants.*.color' => 'required_with:variants|string|max:50',
            'variants.*.stock' => 'required_with:variants|integer|min:0',
            'variants.*.sku' => 'required_with:variants|string|unique:product_variants,sku',
            'variants.*.size_id' => 'required_with:variants|uuid|exists:sizes,size_id',
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
                ]),
                ['photo_product' => json_encode($uploadedPhotos)] // Simpan sebagai JSON
            ));

            // Update atau hapus varian jika ada
            if ($request->has('variants') && is_array($request->variants)) {
                // Hapus semua varian lama
                $product->productVariants()->delete();

                // Tambahkan varian baru
                foreach ($request->variants as $variant) {
                    $product->productVariants()->create($variant);
                }
            }

            return response()->json([
                'message' => 'Product updated successfully.',
                'data' => $product->load('productVariants'), // Load relasi untuk respons
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
