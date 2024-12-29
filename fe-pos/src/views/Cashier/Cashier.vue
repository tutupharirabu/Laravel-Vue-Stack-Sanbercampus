<template>
    <div class="container mx-auto py-8">
        <div class="flex flex-col lg:flex-row gap-8">
            <!-- Loader -->
            <div v-if="loading" class="lg:w-2/3 items-center py-36 bg-white shadow-md rounded-lg overflow-hidden">
                <div class="flex flex-col items-center justify-center">
                    <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-blue-500 mb-4"></div>
                    <p class="text-gray-500 text-sm">Loading product list...</p>
                </div>
            </div>

            <!-- Product List -->
            <div v-else class="lg:w-2/3 bg-white rounded-lg shadow-md p-6">
                <h2 class="text-2xl font-bold mb-6">Product List</h2>
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    <div v-for="product in products" :key="product.id"
                        class="bg-white rounded-lg shadow-md overflow-hidden">
                        <img :src="product.image" :alt="product.name" class="w-full h-64 object-cover">
                        <div class="p-4">
                            <h3 class="text-lg font-semibold">{{ product.name }}</h3>
                            <p class="text-gray-600 mt-1">Stock: {{ product.stock }}</p>
                            <p class="text-blue-600 font-semibold mt-2">
                                Rp{{ product.price.toLocaleString() }}
                            </p>
                            <button @click="addToCart(product)" :disabled="product.stock <= 0"
                                class="w-full mt-4 bg-blue-600 text-white py-2 rounded hover:bg-blue-700 disabled:bg-gray-400 disabled:cursor-not-allowed">
                                {{ product.stock <= 0 ? 'Out of Stock' : 'Add to Cart' }} </button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Shopping Cart -->
            <div class="lg:w-1/3">
                <div class="bg-white rounded-lg shadow-md p-6">
                    <div class="flex justify-between items-center mb-4 border-b pb-4">
                        <h2 class="text-xl font-bold">Shopping Cart</h2>
                        <button v-if="cart.length > 0" @click="clearCart" class="text-blue-600 text-sm">
                            Clear
                        </button>
                    </div>

                    <!-- Customer Selection -->
                    <div class="mb-4 border-b pb-4">
                        <input v-model="customerSearchQuery" type="text" placeholder="Search customer by name or email"
                            class="w-full px-4 py-2 border rounded">

                        <!-- Customer List -->
                        <div v-if="customerSearchQuery && !selectedCustomer"
                            class="mt-2 border rounded max-h-60 overflow-y-auto">
                            <div v-for="customer in filteredCustomers" :key="customer.id"
                                @click="selectCustomer(customer)" class="p-2 hover:bg-gray-100 cursor-pointer">
                                <div class="font-semibold">{{ customer.name }}</div>
                                <div class="text-sm text-gray-600">{{ customer.email }}</div>
                                <div v-if="customer.phone" class="text-sm text-gray-600">
                                    {{ customer.phone }}
                                </div>
                            </div>
                        </div>

                        <!-- Selected Customer -->
                        <div v-if="selectedCustomer" class="mt-2">
                            <div class="flex justify-between items-center">
                                <div>
                                    <div class="font-semibold">{{ selectedCustomer.name }}</div>
                                    <div class="text-sm text-gray-600">{{ selectedCustomer.email }}</div>
                                    <div v-if="selectedCustomer.phone" class="text-sm text-gray-600">
                                        {{ selectedCustomer.phone }}
                                    </div>
                                </div>
                                <button @click="selectedCustomer = null" class="text-blue-600 text-sm">
                                    Change
                                </button>
                            </div>
                        </div>

                        <!-- Add New Customer Button -->
                        <button v-if="!selectedCustomer" @click="showAddCustomer = true"
                            class="mt-2 text-blue-600 text-sm flex items-center gap-1">
                            + Add new customer
                        </button>
                    </div>

                    <!-- Cart Items -->
                    <div v-if="cart.length > 0" class="space-y-4 mb-6">
                        <div v-for="item in cart" :key="item.id"
                            class="flex items-center justify-between border-b pb-4">
                            <div class="flex-1">
                                <h3 class="font-semibold">{{ item.name }}</h3>
                                <div class="flex items-center gap-4 mt-2">
                                    <button @click="updateQuantity(item, false)"
                                        class="px-2 py-1 border rounded hover:bg-gray-100">−</button>
                                    <span>{{ item.quantity }}</span>
                                    <button @click="updateQuantity(item, true)" :disabled="item.quantity >= item.stock"
                                        class="px-2 py-1 border rounded hover:bg-gray-100 disabled:opacity-50 disabled:cursor-not-allowed">+</button>
                                </div>
                            </div>
                            <div class="text-right ml-4">
                                <p class="font-semibold">
                                    Rp{{ (item.price * item.quantity).toLocaleString() }}
                                </p>
                            </div>
                        </div>
                    </div>
                    <div v-else class="text-center text-gray-500 my-6">
                        Cart is empty
                    </div>

                    <!-- Total -->
                    <div class="font-bold text-xl mb-6">
                        <div class="flex justify-between">
                            <span>Total</span>
                            <span>Rp{{ total.toLocaleString() }}</span>
                        </div>
                    </div>

                    <!-- Place Order Button -->
                    <button @click="handlePayment" :disabled="!selectedCustomer || cart.length === 0" class="w-full bg-blue-900 text-white py-3 rounded hover:bg-blue-800 
                            disabled:bg-gray-400 disabled:cursor-not-allowed">
                        Place Order
                    </button>
                </div>
            </div>
        </div>

        <!-- Add Customer Modal -->
        <div v-if="showAddCustomer" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center">
            <div class="bg-white rounded-lg p-6 w-full max-w-md">
                <h2 class="text-xl font-bold mb-4">Add Customer</h2>
                <form @submit.prevent="handleAddCustomer" class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Customer's Name*</label>
                        <input v-model="newCustomer.full_name" type="text" required placeholder="ex. Charles Timothee"
                            class="mt-1 w-full px-4 py-2 border rounded">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700">
                            Email Address
                        </label>
                        <input v-model="newCustomer.email" type="email" placeholder="ex. yourname@gmail.com"
                            class="mt-1 w-full px-4 py-2 border rounded">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700">Phone Number</label>
                        <input v-model="newCustomer.phone_number" type="tel" placeholder="ex. 087367099238"
                            class="mt-1 w-full px-4 py-2 border rounded">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700">Address</label>
                        <textarea v-model="newCustomer.address" placeholder="ex. East Jakarta" rows="2"
                            class="mt-1 w-full px-4 py-2 border rounded"></textarea>
                    </div>

                    <div class="mt-6 flex justify-end gap-4">
                        <button type="button" @click="closeAddCustomerModal" class="px-4 py-2 text-gray-600">
                            Cancel
                        </button>
                        <button type="submit" @click="addNewCustomer()" class="px-4 py-2 bg-blue-900 text-white rounded hover:bg-blue-800">
                            Add Customer
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'
import customFetch from '@/utils/customFetch'
import { useAuthStore } from '@/stores/Auth';

const AuthStore = useAuthStore()

// State management
const products = ref([])
const loading = ref(false); // Loading state

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

const cart = ref([])
const customers = ref([])
const showAddCustomer = ref(false)
const selectedCustomer = ref(null)
const customerSearchQuery = ref('')
const newCustomer = ref({
    name: '',
    email: '',
    address: '',
    phone: ''
})

// Fetch customers from API
const fetchCustomers = async () => {
    loading.value = true
    try {
        const response = await customFetch.get('/customer', {
            headers: { Authorization: `Bearer ${AuthStore.tokenUser}` }
        })

        if (response?.data?.data) {
            customers.value = response.data.data.map(customer => ({
                id: customer.customer_id,
                name: customer.full_name,
                email: customer.email,
                phone: customer.phone_number,
                address: customer.address
            }))
        }
    } catch (error) {
        console.error('Error fetching customers:', error)
    } finally {
        loading.value = false
    }
}

// Computed values for customer search
const filteredCustomers = computed(() => {
    if (!customerSearchQuery.value) return customers.value
    const query = customerSearchQuery.value.toLowerCase()
    return customers.value.filter(customer =>
        customer.name.toLowerCase().includes(query) ||
        customer.email.toLowerCase().includes(query)
    )
})

// Add new customer method
const addNewCustomer = async () => {
    if (!newCustomer.value.full_name) {
        alert('Customer name is required')
        return
    }

    try {
        const response = await customFetch.post('/customer', {
            full_name: newCustomer.value.full_name,
            email: newCustomer.value.email,
            phone_number: newCustomer.value.phone_number,
            address: newCustomer.value.address
        }, {
            headers: { Authorization: `Bearer ${AuthStore.tokenUser}` }
        })

        if (response?.data?.status === 'success') {
            // Add new customer to local list
            const newCustomerData = response.data.data
            customers.value.push({
                id: newCustomerData.id,
                name: newCustomerData.full_name,
                email: newCustomerData.email,
                phone: newCustomerData.phone_number,
                address: newCustomerData.address
            })

            // Select the newly created customer
            selectCustomer({
                id: newCustomerData.id,
                name: newCustomerData.full_name,
                email: newCustomerData.email,
                phone: newCustomerData.phone_number,
                address: newCustomerData.address
            })

            // Reset form and close modal
            newCustomer.value = { full_name: '', email: '', phone_number: '', address: '' }
            showAddCustomer.value = false
        }
    } catch (error) {
        console.error('Error creating customer:', error)
        alert('Failed to create customer. Please try again.')
    }
}

// Update your selectCustomer method
const selectCustomer = (customer) => {
    selectedCustomer.value = {
        id: customer.id,
        name: customer.name,
        email: customer.email,
        phone: customer.phone || customer.phone_number,
        address: customer.address
    }
    customerSearchQuery.value = ''
}

const total = computed(() => {
    return cart.value.reduce((sum, item) => sum + (item.price * item.quantity), 0)
})

// Methods
const addToCart = (product) => {
    const existingItem = cart.value.find(item => item.id === product.id)
    if (existingItem) {
        existingItem.quantity++
    } else {
        cart.value.push({
            ...product,
            quantity: 1
        })
    }
}

const updateQuantity = (item, increment) => {
    const cartItem = cart.value.find(i => i.id === item.id)
    if (cartItem) {
        if (increment && cartItem.quantity < 10) {
            cartItem.quantity++
        } else if (!increment && cartItem.quantity > 1) {
            cartItem.quantity--
        }
    }
}

const clearCart = () => {
    cart.value = []
}

const currentUser = computed(() => AuthStore.currentUser);

const handlePayment = async () => {
    if (!selectedCustomer.value || cart.value.length === 0) {
        alert('Please select a customer and add items to the cart');
        return;
    }

    try {
        console.log('Starting payment process');

        const transactionData = {
            customer_name: selectedCustomer.value.name,
            customer_email: selectedCustomer.value.email,
            customer_phone: selectedCustomer.value.phone || '',
            customer_address: selectedCustomer.value.address || '',
            items: cart.value.map(item => ({
                id: item.id,
                name: item.name,
                price: item.price,
                quantity: item.quantity,
            })),
            total_amount: total.value,
        };

        console.log('Transaction data:', transactionData);

        // Get Snap token from the backend
        const response = await customFetch.post('/transaction/pay', transactionData, {
            headers: { Authorization: `Bearer ${AuthStore.tokenUser}` },
        });

        const { snap_token } = response.data;
        console.log('Snap token received:', snap_token);

        // Load Midtrans Snap and handle payment
        window.snap.pay(snap_token, {
            onSuccess: async (result) => {
                console.log('Payment successful:', result);

                try {
                    console.log('Saving transaction to backend');
                    const saveResponse = await customFetch.post('/transaction/save', {
                        transaction_id: result.transaction_id,
                        customer_id: selectedCustomer.value.id,
                        cashier_id: currentUser.value.id,
                        amount: total.value,
                        items: JSON.stringify(cart.value.map(item => ({
                            id: item.id,
                            name: item.name,
                            price: item.price,
                            quantity: item.quantity,
                        }))),
                        status: result.transaction_status === 'settlement',
                    }, {
                        headers: { Authorization: `Bearer ${AuthStore.tokenUser}` },
                    });

                    console.log('Transaction saved successfully:', saveResponse.data);

                    // Reduce item quantity in the backend
                    for (const item of cart.value) {
                        console.log(`Updating stock for item: ${item.id}`);
                        const stockResponse = await customFetch.put(`/product/reduce-quantity/${item.id}`, {
                            quantity: item.quantity,
                        }, {
                            headers: { Authorization: `Bearer ${AuthStore.tokenUser}` },
                        });
                        console.log(`Stock updated for item: ${item.id}`, stockResponse.data);
                    }

                    cart.value = [];
                    alert('Payment successful and transaction saved successfully!');
                    fetchData();
                } catch (error) {
                    console.error('Error saving transaction or updating stock:', error.response || error.message);
                    alert('Payment successful but failed to save transaction or update stock.');
                }
            },
            onPending: (result) => {
                console.log('Payment pending:', result);
                alert('Waiting for payment!');
            },
            onError: (result) => {
                console.error('Payment error:', result);
                alert('Payment failed!');
            },
            onClose: () => {
                console.log('Customer closed the popup without completing the payment');
            },
        });
    } catch (error) {
        console.error('Error processing payment:', error.response || error.message);
        alert('Error processing payment. Please try again.');
    }
};

onMounted(() => {
    fetchData()
    fetchCustomers()
})
</script>