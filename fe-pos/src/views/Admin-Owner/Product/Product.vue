<template>
    <!-- Header -->
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold text-gray-800">Products</h1>
        <button class="flex items-center px-4 py-2 bg-blue-600 text-white rounded-lg font-medium hover:bg-blue-700"
            @click="addProduct">
            <v-icon name="io-add-circle" class="h-5 w-5 mr-2" /> Add Product
        </button>
    </div>

    <!-- Loader -->
    <div v-if="loading" class="text-center py-10 bg-white shadow-md rounded-lg overflow-hidden">
        <div class="flex justify-center items-center space-x-2">
            <div class="w-6 h-6 border-4 border-blue-500 border-t-transparent rounded-full animate-spin"></div>
            <span class="text-blue-600 font-medium">Loading products...</span>
        </div>
    </div>

    <!-- Product List -->
    <div v-else-if="products.length > 0" class="bg-white shadow-md rounded-lg overflow-hidden">
        <div class="p-4 border-b">
            <span class="font-semibold text-gray-700">Product List</span>
        </div>
        <table class="w-full text-left text-sm">
            <thead class="bg-base-200 text-gray-600 font-medium">
                <tr>
                    <th class="p-4"><input type="checkbox" v-model="selectAll" @change="toggleSelectAll" /></th>
                    <th class="p-4">PRODUCT</th>
                    <th class="p-4">STATUS</th>
                    <th class="p-4">STOCK</th>
                    <th class="p-4">PRICE</th>
                    <th class="p-4"></th>
                </tr>
            </thead>
            <tbody>
                <tr v-for="product in paginatedProducts" :key="product.id" class="hover:bg-gray-50 border-t">
                    <td class="p-4">
                        <input type="checkbox" v-model="selectedProducts" :value="product.id" />
                    </td>
                    <td class="p-4 flex items-center space-x-4">
                        <img :src="product.image" alt="Product Image" class="w-36 h-36 rounded-lg" />
                        <span>{{ product.name }}</span>
                    </td>
                    <td class="p-4">
                        <span :class="{
                            'text-green-600 bg-green-50 px-2 py-1 rounded-full': product.status === 'Active',
                            'text-yellow-600 bg-yellow-50 px-2 py-1 rounded-full': product.status === 'Draft',
                        }">
                            {{ product.status }}
                        </span>
                    </td>
                    <td class="p-4">{{ product.stock }}</td>
                    <td class="p-4">{{ product.price }}</td>
                    <td class="p-4 text-right">
                        <button @click="viewProductDetails(product.id)" class="text-gray-500 hover:text-blue-600">
                            <v-icon name="fa-ellipsis-v" class="h-5 w-5" />
                        </button>
                    </td>
                </tr>
            </tbody>
        </table>

        <!-- Pagination and Delete Button -->
        <div class="p-4 flex justify-between items-center border-t">
            <div class="flex items-center space-x-4">
                <span>{{ selectedProducts.length }} Products selected</span>
                <button v-if="selectedProducts.length > 0"
                    class="px-4 py-2 bg-red-600 text-white rounded-lg font-medium hover:bg-red-700"
                    @click="deleteSelectedProducts">
                    Delete Products
                </button>
            </div>
            <div class="flex items-center space-x-2">
                <button v-for="page in totalPages" :key="page" @click="changePage(page)" :class="{
                    'bg-blue-600 text-white px-3 py-1 rounded-lg': currentPage === page,
                    'bg-gray-100 text-gray-600 px-3 py-1 rounded-lg hover:bg-gray-200': currentPage !== page,
                }">
                    {{ page }}
                </button>
            </div>
        </div>
    </div>

    <!-- Empty State -->
    <div v-else class="text-center py-10 bg-white shadow-md rounded-lg">
        <img src="@/assets/No-Product.png" alt="Empty State" class="mx-auto mb-4 w-48 h-auto" />
        <h3 class="text-lg font-semibold text-gray-800">You don’t have any product yet</h3>
        <p class="text-sm text-gray-600 mb-4">
            Add your first product and enjoy smooth transaction experience by pressing the button below.
        </p>
        <button class="px-4 py-2 bg-blue-600 text-white rounded-lg font-medium hover:bg-blue-700" @click="addProduct">
            <v-icon name="io-add-circle" class="h-5 w-5 mr-2" /> Add Product
        </button>
    </div>

    <!-- Modal -->
    <div v-if="showModal" class="fixed inset-0 bg-black bg-opacity-50 flex justify-center items-center z-50">
        <div class="bg-white rounded-lg p-6 w-full max-w-4xl">
            <div class="flex justify-between items-center mb-4">
                <h2 class="text-xl font-bold">Product Information</h2>
                <button @click="closeModal" class="text-gray-500 hover:text-gray-700"><v-icon name="io-close-sharp"
                        class="h-5 w-5 mr-2" /></button>
            </div>
            <div v-if="selectedProduct">
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div>
                        <img :src="selectedProduct.photo_product[0] || 'https://placehold.co/600x400'"
                            alt="Product Image" class="rounded-lg w-full h-auto mb-4" />
                    </div>
                    <div class="col-span-2">
                        <p><strong>Product Name:</strong> {{ selectedProduct.product_name }}</p>
                        <p><strong>SKU:</strong> {{ selectedProduct.sku }}</p>
                        <p><strong>Price:</strong> Rp{{ selectedProduct.price }}</p>
                        <p><strong>Status:</strong> {{ selectedProduct.status }}</p>
                        <p><strong>Stock:</strong> {{ selectedProduct.stock }}</p>
                        <p><strong>Description:</strong> {{ selectedProduct.description }}</p>
                    </div>
                </div>
                <div class="flex justify-end mt-6 space-x-4">
                    <button class="px-4 py-2 bg-blue-600 text-white rounded-lg font-medium hover:bg-blue-700"
                        @click="editProduct(selectedProduct.product_id)">
                        Edit Product
                    </button>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue';
import { useAuthStore } from '@/stores/Auth';
import { useRouter } from 'vue-router';
import customFetch from '@/utils/customFetch';

import { addIcons } from 'oh-vue-icons';
import { IoAddCircle, FaEllipsisV, IoCloseSharp } from 'oh-vue-icons/icons';

addIcons(IoAddCircle, FaEllipsisV, IoCloseSharp);

// States
const AuthStore = useAuthStore();
const products = ref([]);
const selectedProducts = ref([]);
const selectAll = ref(false);
const loading = ref(false); // Loading state
const currentPage = ref(1);
const perPage = 10; // Number of items per page
const totalPages = computed(() => Math.ceil(products.value.length / perPage));
const showModal = ref(false);
const selectedProduct = ref({
    photo_product: [], // Default value to prevent errors
});
const router = useRouter();

// Computed: Paginated products
const paginatedProducts = computed(() => {
    const start = (currentPage.value - 1) * perPage;
    return products.value.slice(start, start + perPage);
});

// Fetch data from API
const fetchData = async () => {
    loading.value = true; // Show loader
    try {
        const response = await customFetch.get('/product', {
            headers: { Authorization: `Bearer ${AuthStore.tokenUser}` },
        });

        if (response?.data?.data) {
            products.value = response.data.data.map(product => {
                let photos = [];
                try {
                    photos = JSON.parse(product.photo_product || '[]');
                } catch (error) {
                    console.error('Error parsing photo_product:', error);
                }

                return {
                    id: product.id,
                    name: product.product_name,
                    image: photos[0] || 'https://placehold.co/600x400',
                    status: product.status ? 'Active' : 'Draft',
                    stock: product.stock,
                    price: product.price,
                };
            });
        } else {
            console.error('Unexpected API response:', response);
        }
    } catch (error) {
        console.error('Error fetching products:', error);
    } finally {
        loading.value = false; // Hide loader
    }
};

// Toggle select all checkboxes
const toggleSelectAll = () => {
    if (selectAll.value) {
        selectedProducts.value = products.value.map(product => product.id);
    } else {
        selectedProducts.value = [];
    }
};

// Delete selected products
const deleteSelectedProducts = async () => {
    if (selectedProducts.value.length === 0) {
        alert('No products selected for deletion.');
        return;
    }

    const confirmed = confirm('Are you sure you want to delete the selected products? This action cannot be undone.');

    if (!confirmed) {
        return;
    }

    try {
        const response = await customFetch.delete('/product', {
            headers: { Authorization: `Bearer ${AuthStore.tokenUser}` },
            data: { ids: selectedProducts.value },
        });

        if (response.status === 200) {
            alert(response.data.message || 'Products deleted successfully.');
            selectedProducts.value = [];
            fetchData(); // Refresh data setelah penghapusan
        } else {
            console.error('Error deleting products:', response);
        }
    } catch (error) {
        console.error('Error deleting products:', error);
        alert('Failed to delete products. Please try again.');
    }
};

// View product details
const viewProductDetails = async (id) => {
    try {
        const response = await customFetch.get(`/product/${id}`, {
            headers: { Authorization: `Bearer ${AuthStore.tokenUser}` },
        });
        const productData = response.data.data;

        // Parse `photo_product` field directly here
        productData.photo_product = JSON.parse(productData.photo_product || '[]');

        // Set selected product and show modal
        selectedProduct.value = productData;
        showModal.value = true;
    } catch (error) {
        console.error('Error fetching product details:', error);
    }
};

const closeModal = () => {
    showModal.value = false;
    selectedProduct.value = null;
};

const changePage = (page) => {
    currentPage.value = page;
};

const addProduct = () => {
    router.push('/dashboard/product-add');
};

const editProduct = (id) => {
    if (!id) {
        console.error('Product ID is missing!');
        return;
    }
    router.push({ name: 'ProductAdminOwner-EditProduct', params: { id } });
};

// Lifecycle Hook
onMounted(fetchData);
</script>
