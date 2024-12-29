<template>
    <div class="p-6">
        <!-- Header -->
        <div class="flex items-center justify-between mb-6">
            <div class="flex items-center space-x-4">
                <div class="text-xl font-medium">Transactions</div>
            </div>
        </div>

        <!-- Transaction List -->
        <div class="bg-white rounded-lg border border-gray-200">
            <!-- Loading State -->
            <div v-if="isLoading" class="py-32">
                <div class="flex flex-col items-center justify-center">
                    <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-blue-500 mb-4"></div>
                    <p class="text-gray-500 text-sm">Loading transactions...</p>
                </div>
            </div>

            <div v-else-if="transactions.length > 0">
                <!-- Table Header -->
                <div class="grid grid-cols-6 gap-4 p-4 border-b border-gray-200 bg-gray-50 text-sm text-gray-500">
                    <div class="col-span-1">TRANSACTION ID</div>
                    <div class="col-span-1">CUSTOMER</div>
                    <div class="col-span-2">ITEMS</div>
                    <div class="col-span-1">AMOUNT</div>
                    <div class="col-span-1">STATUS</div>
                </div>

                <!-- Table Body -->
                <div class="divide-y divide-gray-200">
                    <div v-for="transaction in transactions" :key="transaction.transaction_id"
                        class="grid grid-cols-6 gap-4 p-4 items-center">
                        <div class="col-span-1 text-sm">{{ transaction.transaction_id }}</div>
                        <div class="col-span-1 text-sm">{{ transaction.customer?.name }}</div>
                        <div class="col-span-2">
                            <div class="flex space-x-2">
                                <div v-for="(item, index) in transaction.items" :key="index" class="relative">
                                    <img :src="item.product?.product_photo || 'https://placehold.co/600x400'"
                                        :alt="item.value.name" class="w-16 h-16 rounded-md object-cover" />
                                </div>
                            </div>
                        </div>
                        <div class="col-span-1 text-sm">{{ formatCurrency(transaction.amount) }}</div>
                        <div class="col-span-1">
                            <span :class="[
                                'px-3 py-1 rounded-full text-sm',
                                getStatusClass(transaction.status)
                            ]">
                                {{ transaction.status }}
                            </span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Empty State -->
            <div v-else class="py-16 flex flex-col items-center justify-center text-center">
                <div class="w-32 h-32 mb-6">
                    <img src="../assets/No-Transaction.png" alt="Empty state" class="w-full h-full" />
                </div>
                <h3 class="text-lg font-medium text-gray-900 mb-1">You don't have any transactions</h3>
                <p class="text-sm text-gray-500">You have not received any transactions from your customers.</p>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import { useAuthStore } from '@/stores/Auth';
import customFetch from '@/utils/customFetch'

const AuthStore = useAuthStore()
const transactions = ref([])
const error = ref(null)
const isLoading = ref(true)

const getStatusClass = (status) => {
    const classes = {
        'pending': 'bg-yellow-100 text-yellow-600',
        'completed': 'bg-blue-100 text-blue-600',
        'cancelled': 'bg-gray-100 text-gray-600',
        'failed': 'bg-red-100 text-red-600'
    }
    return classes[status.toLowerCase()] || 'bg-gray-100 text-gray-600'
}

const formatCurrency = (amount) => {
    return new Intl.NumberFormat('id-ID', {
        style: 'currency',
        currency: 'IDR'
    }).format(amount)
}

const fetchTransactions = async () => {
    isLoading.value = true;
    try {
        const response = await customFetch.get('/transaction', {
            headers: { Authorization: `Bearer ${AuthStore.tokenUser}` },
        });

        // Menyesuaikan logika berdasarkan response dari server
        if (response.status === 200) {
            transactions.value = response.data.data.map((transaction) => ({
                transaction_id: transaction.transaction_id,
                customer: {
                    name: transaction.customer.full_name,
                    email: transaction.customer.email,
                    phone_number: transaction.customer.phone_number,
                    address: transaction.customer.address,
                },
                cashier: {
                    name: transaction.cashier.full_name,
                    email: transaction.cashier.email,
                },
                items: JSON.parse(transaction.items),
                amount: transaction.amount,
                status: transaction.status === 1 ? 'Completed' : 'Pending',
                created_at: transaction.created_at,
            }));

            console.log('Response data:', transactions.value);
        } else {
            error.value = response.data.message || 'Failed to fetch transactions.';
        }
    } catch (e) {
        console.error('Error fetching transactions:', e);
        error.value = e.response?.data?.message || 'Failed to fetch transactions.';
    } finally {
        isLoading.value = false;
    }
};

onMounted(() => {
    fetchTransactions()
})
</script>