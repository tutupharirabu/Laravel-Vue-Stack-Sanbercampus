<template>
    <!-- Header -->
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold text-gray-800">Products</h1>
        <button class="flex items-center px-4 py-2 bg-blue-600 text-white rounded-lg font-medium hover:bg-blue-700">
            <v-icon name="io-add-circle" class="h-5 w-5 mr-2" /> Add Product
        </button>
    </div>

    <!-- Product List -->
    <div v-if="products.length > 0" class="bg-white shadow-md rounded-lg overflow-hidden">
        <div class="p-4 border-b">
            <span class="font-semibold text-gray-700">Product List</span>
        </div>
        <table class="w-full text-left text-sm">
            <thead class="bg-gray-100 text-gray-600 font-medium">
                <tr>
                    <th class="p-4"><input type="checkbox" v-model="selectAll" /></th>
                    <th class="p-4">PRODUCT</th>
                    <th class="p-4">CATEGORY</th>
                    <th class="p-4">STATUS</th>
                    <th class="p-4">STOCK</th>
                    <th class="p-4">PRICE</th>
                </tr>
            </thead>
            <tbody>
                <tr v-for="product in products" :key="product.id" class="hover:bg-gray-50 border-t">
                    <td class="p-4">
                        <input type="checkbox" v-model="selectedProducts" :value="product.id" />
                    </td>
                    <td class="p-4 flex items-center space-x-4">
                        <img :src="product.image" alt="Product Image" class="w-10 h-10 rounded-lg" />
                        <span>{{ product.name }}</span>
                    </td>
                    <td class="p-4">{{ product.category }}</td>
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
                </tr>
            </tbody>
        </table>

        <!-- Pagination -->
        <div class="p-4 flex justify-between items-center border-t">
            <span>{{ selectedProducts.length }} Products selected</span>
            <div class="flex items-center space-x-2">
                <button v-for="page in totalPages" :key="page" @click="currentPage = page" :class="{
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
        <button class="px-4 py-2 bg-blue-600 text-white rounded-lg font-medium hover:bg-blue-700">
            <v-icon name="io-add-circle" class="h-5 w-5 mr-2" /> Add Product
        </button>
    </div>
</template>

<script setup>
import { ref } from 'vue';
import { addIcons } from "oh-vue-icons";
import { IoAddCircle } from "oh-vue-icons/icons";

addIcons(IoAddCircle);

// States
const products = ref([]); // Simulate product data or fetch from API
const selectedProducts = ref([]);
const selectAll = ref(false);
const currentPage = ref(1);
const totalPages = 5; // Assume pagination
</script>

<style>
/* Add your custom styles here */
</style>