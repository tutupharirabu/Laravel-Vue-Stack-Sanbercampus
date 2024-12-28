<template>
    <div class="p-8 bg-white rounded-lg">
        <h1 class="text-xl font-semibold mb-8">Add Products</h1>

        <!-- Product Information Section -->
        <div class="mb-8">
            <h2 class="text-base font-semibold border-l-4 border-gray-800 pl-2 mb-6">Product Informations</h2>

            <div class="space-y-6">
                <div>
                    <label class="block text-sm mb-1">Product name</label>
                    <p class="text-xs text-gray-500 mb-2">Do not exceed 20 characters when entering the product name.
                    </p>
                    <input v-model="product.name" type="text" placeholder="Enter product name"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:outline-none" />
                </div>

                <div>
                    <label class="block text-sm mb-1">SKU</label>
                    <p class="text-xs text-gray-500 mb-2">SKU is a scannable barcode and is the unit of measure in which
                        the stock of a product is managed.</p>
                    <input v-model="product.sku" type="text" placeholder="Enter SKU"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:outline-none" />
                </div>

                <div>
                    <label class="block text-sm mb-1">Price</label>
                    <div class="relative">
                        <span class="absolute left-3 top-2">Rp</span>
                        <input v-model="product.price" type="text" placeholder="000"
                            class="w-full pl-8 pr-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:outline-none" />
                    </div>
                </div>

                <div>
                    <label class="block text-sm mb-1">Category</label>
                    <p class="text-xs text-gray-500 mb-2">Please select your product category from the list provided.
                    </p>
                    <select v-model="product.category"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:outline-none">
                        <option value="">Select category</option>
                        <option v-for="category in categories" :key="category.id" :value="category.id">
                            {{ category.name }}
                        </option>
                    </select>
                </div>

                <div>
                    <label class="block text-sm mb-1">Gender</label>
                    <p class="text-xs text-gray-500 mb-2">Set the gender for this product.</p>
                    <select v-model="product.gender"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:outline-none">
                        <option value="">Select gender</option>
                        <option value="male">Male</option>
                        <option value="female">Female</option>
                        <option value="unisex">Unisex</option>
                    </select>
                </div>
            </div>
        </div>

        <!-- Photo Product Section -->
        <div class="mb-8">
            <h2 class="text-base font-semibold border-l-4 border-gray-800 pl-2 mb-6">Photo Product</h2>
            <p class="text-xs text-gray-500 mb-4">
                Recommended minimum width 1080px x 1080px, with a max size of 5MB. *.png, *.jpg and *.jpeg image files
                are accepted.
            </p>
            <div class="grid grid-cols-5 gap-4">
                <div v-for="(photo, index) in product.photos" :key="index"
                    class="relative aspect-square border border-dashed rounded-lg flex flex-col items-center justify-center">
                    <img v-if="photo" :src="photo" class="w-full h-full object-cover rounded-lg" />
                    <template v-else>
                        <div class="flex flex-col items-center">
                            <svg class="w-5 h-5 text-gray-400 mb-2" viewBox="0 0 24 24" fill="none"
                                stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12" />
                            </svg>
                            <button class="text-blue-600 text-sm font-medium">Add image</button>
                        </div>
                        <p class="text-xs text-gray-500 mt-2">or Drop image to Upload</p>
                    </template>
                    <input type="file" class="hidden" @change="(e) => handlePhotoUpload(e, index)"
                        accept="image/png,image/jpeg,image/jpg" />
                </div>
            </div>
        </div>

        <!-- Status -->
        <div class="mb-8">
            <label class="block text-sm mb-1">Status</label>
            <p class="text-xs text-gray-500 mb-2">
                Set a status for your product to determine whether your product is displayed or not.
            </p>
            <label class="relative inline-flex items-center cursor-pointer">
                <input v-model="product.status" type="checkbox" class="sr-only peer" />
                <div
                    class="w-11 h-6 bg-gray-200 rounded-full peer peer-checked:after:translate-x-full after:content-[''] after:absolute after:top-0.5 after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-blue-600">
                </div>
            </label>
        </div>

        <!-- Product Description -->
        <div class="mb-8">
            <label class="block text-sm mb-1">Product description</label>
            <p class="text-xs text-gray-500 mb-2">Set a description on product to detail your product and better
                visibility.</p>
            <textarea v-model="product.description" rows="4" placeholder="Description"
                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:outline-none"></textarea>
        </div>

        <!-- Variant Section -->
        <div class="mb-8">
            <div class="flex justify-between items-center mb-6">
                <h2 class="text-base font-semibold border-l-4 border-gray-800 pl-2">Variant</h2>
                <div class="flex gap-4">
                    <button @click="deleteAllVariants" class="text-red-500 font-medium">Delete All Variant</button>
                    <button @click="addVariant"
                        class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 font-medium">
                        + Add Variant
                    </button>
                </div>
            </div>

            <div class="border rounded-lg">
                <div class="grid grid-cols-7 gap-4 p-4 bg-gray-50 border-b">
                    <div class="flex items-center">
                        <input type="checkbox" class="mr-2" />
                        <span class="text-sm font-medium">VARIANT IMAGE</span>
                    </div>
                    <span class="text-sm font-medium">COLOR NAME</span>
                    <span class="text-sm font-medium">SIZE</span>
                    <span class="text-sm font-medium">STOCK</span>
                    <span class="text-sm font-medium">SKU</span>
                    <span class="text-sm font-medium">STATUS</span>
                    <span class="text-sm font-medium">ACTION</span>
                </div>

                <div v-for="(variant, index) in product.variants" :key="index"
                    class="grid grid-cols-7 gap-4 p-4 items-center">
                    <div class="flex items-center">
                        <input type="checkbox" class="mr-2" />
                        <div
                            class="w-16 h-16 border border-dashed rounded-lg flex items-center justify-center relative">
                            <img v-if="variant.image" :src="variant.image"
                                class="absolute inset-0 w-full h-full object-cover rounded-lg" />
                            <button v-else class="text-blue-600 text-sm">Add image</button>
                            <input type="file" class="hidden" @change="(e) => handleVariantImageUpload(e, index)"
                                accept="image/png,image/jpeg,image/jpg" />
                        </div>
                    </div>
                    <input v-model="variant.color" type="text" placeholder="Color name"
                        class="px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:outline-none" />
                    <select v-model="variant.size"
                        class="px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:outline-none">
                        <option value="">Select</option>
                        <option v-for="size in sizes" :key="size.id" :value="size.id">{{ size.name }}</option>
                    </select>
                    <input v-model="variant.stock" type="text" placeholder="Enter stock"
                        class="px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:outline-none" />
                    <input v-model="variant.sku" type="text" placeholder="Enter SKU"
                        class="px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:outline-none" />
                    <label class="relative inline-flex items-center cursor-pointer">
                        <input v-model="variant.status" type="checkbox" class="sr-only peer" />
                        <div
                            class="w-11 h-6 bg-gray-200 rounded-full peer peer-checked:after:translate-x-full after:content-[''] after:absolute after:top-0.5 after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-blue-600">
                        </div>
                    </label>
                    <button @click="deleteVariant(index)" class="text-red-500">
                        <i class="bi bi-trash h-5 w-5"></i>
                    </button>
                </div>
            </div>
        </div>

        <!-- Footer -->
        <div class="flex items-center justify-between mt-8">
            <div class="text-sm text-gray-500">
                last saved Nov 9, 2022 - 17:09
            </div>
            <div class="flex gap-4">
                <button @click="cancel" class="px-4 py-2 text-gray-600 hover:bg-gray-100 rounded-lg">
                    Cancel
                </button>
                <button @click="saveProduct" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
                    Save Product
                </button>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref } from 'vue'

const product = ref({
    name: '',
    sku: '',
    price: '',
    category: '',
    gender: '',
    photos: Array(4).fill(null),
    status: false,
    description: '',
    variants: []
})

const categories = ref([])
const sizes = ref([])

const handlePhotoUpload = (e, index) => {
    const file = e.target.files[0]
    if (file) {
        const reader = new FileReader()
        reader.onload = (e) => {
            product.value.photos[index] = e.target.result
        }
        reader.readAsDataURL(file)
    }
}

const handleVariantImageUpload = (e, index) => {
    const file = e.target.files[0]
    if (file) {
        const reader = new FileReader()
        reader.onload = (e) => {
            product.value.variants[index].image = e.target.result
        }
        reader.readAsDataURL(file)
    }
}

const addVariant = () => {
    product.value.variants.push({
        image: null,
        color: '',
        size: '',
        stock: '',
        sku: '',
        status: false
    })
}

const deleteVariant = (index) => {
    product.value.variants.splice(index, 1)
}

const deleteAllVariants = () => {
    product.value.variants = []
}

const cancel = () => {
    // Handle cancel logic
}

const saveProduct = () => {
    // Handle save logic
}
</script>