<template>
    <div class="p-8 bg-white rounded-lg">
        <!-- Loader -->
        <div v-if="loading" class="text-center py-10">
            <div class="flex justify-center items-center space-x-2">
                <div class="w-8 h-8 border-4 border-blue-500 border-t-transparent rounded-full animate-spin"></div>
                <span class="text-blue-600 font-medium">Loading detail product data...</span>
            </div>
        </div>

        <div v-else>
            <h1 class="text-xl font-semibold mb-8">{{ isEdit ? 'Edit Product' : 'Add Product' }}</h1>

            <!-- Product Information Section -->
            <div class="mb-8">
                <h2 class="text-base font-semibold border-l-4 border-gray-800 pl-2 mb-6">Product Information</h2>

                <div class="space-y-6">
                    <div>
                        <label class="block text-sm mb-1">Product Name</label>
                        <input v-model="productData.name" type="text" placeholder="Enter product name"
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:outline-none" />
                    </div>

                    <div>
                        <label class="block text-sm mb-1">SKU</label>
                        <input v-model="productData.sku" type="text" placeholder="Enter SKU"
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:outline-none" />
                    </div>

                    <div>
                        <label class="block text-sm mb-1">Price</label>
                        <div class="relative">
                            <span class="absolute left-2 top-2">Rp</span>
                            <input v-model="productData.price" type="number" placeholder="000"
                                class="w-full pl-8 pr-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:outline-none" />
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm mb-1">Stock</label>
                        <input v-model="productData.stock" type="number" placeholder="Enter stock quantity"
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:outline-none" />
                    </div>

                    <div>
                        <label class="block text-sm mb-1">Gender</label>
                        <select v-model="productData.gender"
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
                <div class="grid grid-cols-5 gap-4">
                    <div v-for="(photo, index) in photos" :key="index"
                        class="relative aspect-square border border-dashed rounded-lg flex flex-col items-center justify-center group">
                        <template v-if="photo">
                            <img :src="typeof photo === 'string' ? photo : URL.createObjectURL(photo)"
                                class="w-full h-full object-cover rounded-lg" @error="handleImageError(index)" />
                            <div
                                class="absolute inset-0 bg-black bg-opacity-50 opacity-0 group-hover:opacity-100 transition-opacity flex items-center justify-center space-x-2">
                                <button @click="editPhoto(index)"
                                    class="p-2 bg-blue-500 text-white rounded-full hover:bg-blue-600">
                                    <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" />
                                    </svg>
                                </button>
                                <button @click="removePhoto(index)"
                                    class="p-2 bg-red-500 text-white rounded-full hover:bg-red-600">
                                    <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                    </svg>
                                </button>
                            </div>
                        </template>
                        <template v-else>
                            <div class="flex flex-col items-center">
                                <svg class="w-5 h-5 text-gray-400 mb-2" viewBox="0 0 24 24" fill="none"
                                    stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12" />
                                </svg>
                                <button type="button" @click="triggerFileInput(index)"
                                    class="text-blue-600 text-sm font-medium">
                                    Add Image
                                </button>
                            </div>
                        </template>
                        <input type="file" accept="image/jpeg,image/png,image/gif" class="hidden"
                            :ref="(el) => { if (el) fileInputs[index] = el }"
                            @change="(e) => handlePhotoUpload(e, index)" />
                    </div>
                </div>
            </div>

            <!-- Status -->
            <div class="mb-8">
                <label class="block text-sm mb-1">Status</label>
                <label class="relative inline-flex items-center cursor-pointer">
                    <input v-model="productData.status" type="checkbox" class="sr-only peer" />
                    <div
                        class="w-11 h-6 bg-gray-200 rounded-full peer peer-checked:after:translate-x-full after:content-[''] after:absolute after:top-0.5 after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-blue-600">
                    </div>
                </label>
            </div>

            <!-- Product Description -->
            <div class="mb-8">
                <label class="block text-sm mb-1">Product Description</label>
                <textarea v-model="productData.description" rows="4" placeholder="Enter product description"
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:outline-none"></textarea>
            </div>

            <!-- Footer -->
            <div class="flex items-center justify-end mt-8">
                <div class="flex gap-4">
                    <button @click="cancel" class="px-4 py-2 text-gray-600 hover:bg-gray-100 rounded-lg">
                        Cancel
                    </button>
                    <button @click="submitForm" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
                        {{ isEdit ? 'Update Product' : 'Save Product' }}
                    </button>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref, reactive, onMounted } from 'vue'
import { useRouter, useRoute } from 'vue-router'
import { useAuthStore } from '@/stores/Auth'
import customFetch from '@/utils/customFetch'

const AuthStore = useAuthStore()
const router = useRouter()
const route = useRoute()
const loading = ref(false)
const isEdit = ref(false)
const fileInputs = reactive(Array(5).fill(null))
const photos = ref(Array(5).fill(null))

const productData = reactive({
    name: '',
    sku: '',
    price: '',
    stock: '',
    gender: '',
    status: false,
    description: '',
})

const triggerFileInput = (index) => {
    if (fileInputs[index]) {
        fileInputs[index].click()
    }
}

const handlePhotoUpload = (e, index) => {
    const file = e.target.files?.[0]
    if (!file) return

    const allowedTypes = ['image/jpeg', 'image/png', 'image/gif']
    if (!allowedTypes.includes(file.type)) {
        alert('Please upload only image files (JPEG, PNG, GIF)')
        return
    }

    const maxSize = 5 * 1024 * 1024 // 5MB
    if (file.size > maxSize) {
        alert('File size exceeds the 5MB limit.')
        return
    }

    photos.value[index] = file
}

const editPhoto = (index) => {
    triggerFileInput(index)
}

const removePhoto = (index) => {
    photos.value[index] = null
    if (fileInputs[index]) {
        fileInputs[index].value = ''
    }
}

const handleImageError = (index) => {
    photos.value[index] = null
}

const fetchProductForEdit = async () => {
    loading.value = true
    try {
        const { data } = await customFetch.get(`/product/${route.params.id}`, {
            headers: { Authorization: `Bearer ${AuthStore.tokenUser}` },
        })

        const productData = data.data
        Object.assign(productData, {
            name: productData.product_name || '',
            sku: productData.sku || '',
            price: productData.price || '',
            stock: productData.stock || '',
            gender: productData.gender || '',
            status: !!productData.status,
            description: productData.description || '',
        })

        try {
            const parsedPhotos = JSON.parse(productData.photo_product || '[]')
            photos.value = [...parsedPhotos, ...Array(5 - parsedPhotos.length).fill(null)]
        } catch (err) {
            console.error('Error parsing photos:', err)
            photos.value = Array(5).fill(null)
        }

        isEdit.value = true
    } catch (error) {
        console.error('Error fetching product:', error)
        alert('Product not found')
        router.push('/dashboard/product')
    } finally {
        loading.value = false
    }
}

const submitForm = async () => {
    try {
        const formData = new FormData()
        formData.append('product_name', productData.name)
        formData.append('sku', productData.sku)
        formData.append('price', productData.price)
        formData.append('stock', productData.stock)
        formData.append('gender', productData.gender)
        formData.append('status', productData.status ? 1 : 0)
        formData.append('description', productData.description)

        // Append photos
        photos.value.forEach((photo, index) => {
            if (photo) {
                formData.append(`photos[${index}]`, photo)
            }
        })

        const config = {
            headers: {
                'Content-Type': 'multipart/form-data',
                Authorization: `Bearer ${AuthStore.tokenUser}`,
            },
        }

        if (isEdit.value) {
            await customFetch.post(`/product/${route.params.id}?_method=PUT`, formData, config)
            alert('Product updated successfully!')
        } else {
            await customFetch.post('/products', formData, config)
            alert('Product added successfully!')
        }

        router.push('/dashboard/product')
    } catch (error) {
        console.error('Error submitting form:', error)
        alert('An error occurred. Please try again.')
    }
}

const cancel = () => {
    router.push('/dashboard/product')
}

onMounted(() => {
    if (route.params.id) {
        fetchProductForEdit()
    }
})
</script>