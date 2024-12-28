<template>
    <div class="container mx-auto py-8">
        <div class="flex flex-col lg:flex-row gap-8">
            <!-- Product List -->
            <div class="lg:w-2/3 bg-white rounded-lg shadow-md p-6">
                <h2 class="text-2xl font-bold mb-6">Product List</h2>
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    <div v-for="product in products" :key="product.id"
                        class="bg-white rounded-lg shadow-md overflow-hidden">
                        <img :src="product.image" :alt="product.name" class="w-full h-64 object-cover">
                        <div class="p-4">
                            <h3 class="text-lg font-semibold">{{ product.name }}</h3>
                            <p class="text-blue-600 font-semibold mt-2">
                                Rp{{ product.price.toLocaleString() }}
                            </p>
                            <button @click="addToCart(product)"
                                class="w-full mt-4 bg-blue-600 text-white py-2 rounded hover:bg-blue-700">
                                Add to Cart
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Shopping Cart -->
            <div class="lg:w-1/3">
                <div class="bg-white rounded-lg shadow-md p-6">
                    <div class="flex justify-between items-center mb-4 border-b pb-4">
                        <h2 class="text-xl font-bold">Shopping Cart</h2>
                        <!-- Only show Clear button if cart is not empty -->
                        <button v-if="cart.length > 0" @click="clearCart" class="text-blue-600 text-sm">
                            Clear
                        </button>
                    </div>

                    <!-- Customer Selection -->
                    <div class="mb-4 border-b pb-4">
                        <input v-model="customerSearchQuery" type="text" placeholder="Customer's name"
                            class="w-full px-4 py-2 border rounded">

                        <!-- Customer List -->
                        <div v-if="customerSearchQuery && !selectedCustomer"
                            class="mt-2 border rounded max-h-60 overflow-y-auto">
                            <div v-for="customer in filteredCustomers" :key="customer.email"
                                @click="selectCustomer(customer)" class="p-2 hover:bg-gray-100 cursor-pointer">
                                <div class="font-semibold">{{ customer.name }}</div>
                                <div class="text-sm text-gray-600">{{ customer.email }}</div>
                            </div>
                        </div>

                        <!-- Selected Customer -->
                        <div v-if="selectedCustomer" class="mt-2">
                            <div class="flex justify-between items-center">
                                <div>
                                    <div class="font-semibold">{{ selectedCustomer.name }}</div>
                                    <div class="text-sm text-gray-600">{{ selectedCustomer.email }}</div>
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
                    <div class="space-y-4 mb-6">
                        <div v-for="item in cart" :key="item.id"
                            class="flex items-center justify-between border-b pb-4">
                            <div>
                                <h3 class="font-semibold">{{ item.name }}</h3>
                                <div class="flex items-center gap-4 mt-2">
                                    <button @click="updateQuantity(item, false)"
                                        class="px-2 py-1 border rounded">−</button>
                                    <span>{{ item.quantity }}</span>
                                    <button @click="updateQuantity(item, true)"
                                        class="px-2 py-1 border rounded">+</button>
                                </div>
                            </div>
                            <div class="text-right">
                                <p class="font-semibold">
                                    Rp{{ (item.price * item.quantity).toLocaleString() }}
                                </p>
                            </div>
                        </div>
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
        <div v-if="showAddCustomer"
            class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center border">
            <div class="bg-white rounded-lg p-6 w-full max-w-md">
                <h2 class="text-xl font-bold mb-4">Add Customer</h2>

                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Customer's Name</label>
                        <input v-model="newCustomer.name" type="text" placeholder="ex. Charles Timothee"
                            class="mt-1 w-full px-4 py-2 border rounded">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700">Email Address
                            <span class="text-gray-500">(optional)</span>
                        </label>
                        <input v-model="newCustomer.email" type="email" placeholder="ex. yourname@gmail.com"
                            class="mt-1 w-full px-4 py-2 border rounded">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700">Address</label>
                        <input v-model="newCustomer.address" type="text" placeholder="ex. East Jakarta"
                            class="mt-1 w-full px-4 py-2 border rounded">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700">Phone Number</label>
                        <input v-model="newCustomer.phone" type="tel" placeholder="ex. 087367099238"
                            class="mt-1 w-full px-4 py-2 border rounded">
                    </div>
                </div>

                <div class="mt-6 flex justify-end gap-4">
                    <button @click="showAddCustomer = false" class="px-4 py-2 text-gray-600">
                        Cancel
                    </button>
                    <button @click="addNewCustomer" class="px-4 py-2 bg-blue-900 text-white rounded hover:bg-blue-800">
                        Add Customer
                    </button>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref, computed } from 'vue'
import customFetch from '@/utils/customFetch'
import { useAuthStore } from '@/stores/Auth';

const AuthStore = useAuthStore()

// State management
const customers = ref([
    {
        name: 'Charles Edgar',
        email: 'edgarc@gmail.com'
    },
    {
        name: 'Emmanuel Charles',
        email: 'charles.emmanuel@gmail.com'
    },
    {
        name: 'Charles Nicolas',
        email: 'charlescc2237@gmail.com'
    },
    {
        name: 'Patrick Charles',
        email: 'patrickch@gmail.com'
    },
    {
        name: 'Charles Richard',
        email: 'richardc@gmail.com'
    }
])

const products = ref([
    {
        id: 1,
        name: "Men's UA Storm Armour Down 2.0 Sweater",
        price: 120000,
        image: 'https://placehold.co/200x250'
    },
    {
        id: 2,
        name: "Women's Turtleneck Sweater",
        price: 450000,
        image: 'https://placehold.co/200x250'
    },
    {
        id: 3,
        name: "Women's Grey T-Shirt",
        price: 460000,
        image: 'https://placehold.co/200x250'
    },
    {
        id: 4,
        name: "Women's Stripe Sweater",
        price: 300000,
        image: 'https://placehold.co/200x250'
    },
    {
        id: 5,
        name: "Women's Stripe Sweater",
        price: 200000,
        image: 'https://placehold.co/200x250'
    },
    {
        id: 6,
        name: "Women's Stripe Sweater",
        price: 530000,
        image: 'https://placehold.co/200x250'
    },
    {
        id: 7,
        name: "Women's Stripe Sweater",
        price: 110000,
        image: 'https://placehold.co/200x250'
    },
    {
        id: 8,
        name: "Women's Stripe Sweater",
        price: 820000,
        image: 'https://placehold.co/200x250'
    }
])

const cart = ref([])
const showAddCustomer = ref(false)
const selectedCustomer = ref(null)
const customerSearchQuery = ref('')
const newCustomer = ref({
    name: '',
    email: '',
    address: '',
    phone: ''
})

// Computed values
const filteredCustomers = computed(() => {
    if (!customerSearchQuery.value) return customers.value
    const query = customerSearchQuery.value.toLowerCase()
    return customers.value.filter(customer =>
        customer.name.toLowerCase().includes(query) ||
        customer.email.toLowerCase().includes(query)
    )
})

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

const selectCustomer = (customer) => {
    selectedCustomer.value = customer
    customerSearchQuery.value = ''
}

const addNewCustomer = () => {
    if (newCustomer.value.name && newCustomer.value.email) {
        customers.value.push({
            name: newCustomer.value.name,
            email: newCustomer.value.email,
            address: newCustomer.value.address,
            phone: newCustomer.value.phone
        })
        newCustomer.value = { name: '', email: '', address: '', phone: '' }
        showAddCustomer.value = false
    }
}

const clearCart = () => {
    cart.value = []
}

const handlePayment = async () => {
    if (!selectedCustomer.value || cart.value.length === 0) {
        alert('Please select a customer and add items to cart')
        return
    }

    try {
        const transactionData = {
            customer_name: selectedCustomer.value.name,
            customer_email: selectedCustomer.value.email,
            customer_phone: selectedCustomer.value.phone || '',
            customer_address: selectedCustomer.value.address || '',
            items: cart.value.map(item => ({
                id: item.id,
                name: item.name,
                price: item.price,
                quantity: item.quantity
            })),
            total_amount: total.value
        }

        // Get snap token from backend
        const response = await customFetch.post('/transaction/pay', transactionData, { headers: { Authorization: `Bearer ${AuthStore.tokenUser}` } })
        const { snap_token } = response.data

        // Load Midtrans Snap and handle payment
        window.snap.pay(snap_token, {
            onSuccess: async (result) => {
                try {
                    cart.value = []
                    alert('Payment successful!')
                    console.log('Payment successful:', result)
                } catch (error) {
                    console.error('Error saving order:', error)
                    alert('Payment successful but error saving order details.')
                }
            },
            onPending: (result) => {
                alert('Waiting for payment!')
                console.log('Payment pending:', result)
            },
            onError: (result) => {
                alert('Payment failed!')
                console.error('Payment error:', result)
            },
            onClose: () => {
                console.log('Customer closed the popup without finishing the payment')
            }
        })
    } catch (error) {
        console.error('Error processing payment:', error)
        alert('Error processing payment. Please try again.')
    }
}
</script>