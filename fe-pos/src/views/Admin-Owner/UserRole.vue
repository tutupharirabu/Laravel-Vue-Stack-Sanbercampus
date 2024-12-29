<template>
    <div>
        <!-- Header remains the same -->
        <div class="flex justify-between items-center mb-6">
            <h1 class="text-2xl font-bold text-gray-800">User Role</h1>
            <button @click="openAddModal" class="flex items-center px-4 py-2 bg-blue-600 text-white rounded-lg font-medium hover:bg-blue-700">
                <v-icon name="io-add-circle" class="h-5 w-5 mr-2" /> Add User
            </button>
        </div>

        <!-- Loader -->
        <div v-if="loading" class="text-center py-10 bg-white shadow-md rounded-lg overflow-hidden">
            <div class="flex justify-center items-center space-x-2">
                <div class="w-6 h-6 border-4 border-blue-500 border-t-transparent rounded-full animate-spin"></div>
                <span class="text-blue-600 font-medium">Loading users...</span>
            </div>
        </div>

        <!-- User List -->
        <div v-else-if="users.length > 0" class="bg-white shadow-md rounded-lg overflow-hidden">
            <div class="p-4 border-b">
                <span class="font-semibold text-gray-700">User</span>
            </div>
            <table class="w-full text-left text-sm">
                <thead class="bg-base-200 text-gray-600 font-medium">
                    <tr>
                        <th class="p-4"><input type="checkbox" v-model="selectAll" @change="toggleSelectAll" /></th>
                        <th class="p-4">USER NAME</th>
                        <th class="p-4">USER ROLE</th>
                        <th class="p-4">PHONE NUMBER</th>
                        <th class="p-4">DATE ADD</th>
                        <th class="p-4">ACTION</th>
                    </tr>
                </thead>
                <tbody>
                    <tr v-for="user in paginatedUsers" :key="user.user_id" class="hover:bg-gray-50 border-t">
                        <td class="p-4">
                            <input type="checkbox" v-model="selectedUsers" :value="user.user_id" />
                        </td>
                        <td class="p-4">{{ user.full_name }}</td>
                        <td class="p-4">
                            <span :class="{
                                'text-green-600 bg-green-50 px-2 py-1 rounded-full': user.role_name === 'owner',
                                'text-blue-600 bg-blue-50 px-2 py-1 rounded-full': user.role_name === 'admin',
                                'text-yellow-600 bg-yellow-50 px-2 py-1 rounded-full': user.role_name === 'cashier',
                            }">
                                {{ capitalize(user.role_name) }}
                            </span>
                        </td>
                        <td class="p-4">{{ user.phone_number }}</td>
                        <td class="p-4">{{ formatDate(user.created_at) }}</td>
                        <td class="p-4 flex space-x-4">
                            <button @click="editUser(user.user_id)" class="text-blue-600 hover:underline flex items-center">
                                <v-icon name="fa-edit" class="h-5 w-5 mr-1" /> Edit
                            </button>
                        </td>
                    </tr>
                </tbody>
            </table>

            <!-- Pagination -->
            <div class="p-4 flex justify-between items-center border-t">
                <div class="flex items-center space-x-4">
                    <span>{{ selectedUsers.length }} Users selected</span>
                    <button
                        v-if="selectedUsers.length > 0"
                        class="px-4 py-2 bg-red-600 text-white rounded-lg font-medium hover:bg-red-700"
                        @click="deleteSelectedUsers(selectedUsers)"
                    >
                        Delete Selected
                    </button>
                </div>
                <div class="flex items-center space-x-2">
                    <button
                        v-for="page in totalPages"
                        :key="page"
                        @click="changePage(page)"
                        :class="{
                            'bg-blue-600 text-white px-3 py-1 rounded-lg': currentPage === page,
                            'bg-gray-100 text-gray-600 px-3 py-1 rounded-lg hover:bg-gray-200': currentPage !== page,
                        }"
                    >
                        {{ page }}
                    </button>
                </div>
            </div>
        </div>

        <!-- Modal -->
        <div v-if="showModal" class="fixed inset-0 bg-black bg-opacity-50 flex justify-center items-center z-50">
            <div class="bg-white rounded-lg p-6 w-full max-w-xl">
                <div class="flex justify-between items-center mb-4">
                    <h2 class="text-xl font-bold">{{ isEdit ? 'Edit User Role' : 'Add User Role' }}</h2>
                    <button @click="closeModal" class="text-gray-500 hover:text-gray-700">
                        <v-icon name="io-close-sharp" class="h-5 w-5" />
                    </button>
                </div>
                <form @submit.prevent="validateAndSubmit">
                    <div class="space-y-4">
                        <!-- Full Name Field -->
                        <div>
                            <label class="block text-sm font-medium mb-1">Full Name</label>
                            <input
                                v-model="userForm.full_name"
                                type="text"
                                required
                                placeholder="Enter full name"
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg"
                            />
                        </div>
                        <!-- Phone Number Field with Validation -->
                        <div>
                            <label class="block text-sm font-medium mb-1">Phone Number</label>
                            <div class="relative">
                                <span class="absolute left-3 top-2 text-gray-500">+62</span>
                                <input
                                    v-model="userForm.phone_number"
                                    type="text"
                                    required
                                    placeholder="Enter phone number"
                                    class="w-full pl-12 px-4 py-2 border border-gray-300 rounded-lg"
                                    @input="validatePhoneNumber"
                                    maxlength="13"
                                />
                            </div>
                            <span v-if="phoneError" class="text-red-500 text-sm">{{ phoneError }}</span>
                        </div>
                        <!-- Email Field -->
                        <div>
                            <label class="block text-sm font-medium mb-1">Email</label>
                            <input
                                v-model="userForm.email"
                                type="email"
                                required
                                placeholder="Enter email address"
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg"
                            />
                        </div>
                        <!-- Role Selection -->
                        <div>
                            <label class="block text-sm font-medium mb-1">Role</label>
                            <select 
                                v-model="userForm.role_name" 
                                required
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg"
                            >
                                <option value="" disabled>Select role</option>
                                <option value="owner">Owner</option>
                                <option value="admin">Admin</option>
                                <option value="cashier">Cashier</option>
                            </select>
                        </div>
                        <!-- PIN Field with Validation -->
                        <div>
                            <label class="block text-sm font-medium mb-1">
                                PIN
                                <span v-if="isEdit" class="text-gray-500 text-sm">(Optional)</span>
                                <span v-else class="text-red-500">*</span>
                            </label>
                            <div class="relative">
                                <input
                                    :type="showPassword ? 'text' : 'password'"
                                    v-model="userForm.password"
                                    :required="!isEdit"
                                    :placeholder="pinPlaceholder"
                                    class="w-full px-4 py-2 border border-gray-300 rounded-lg"
                                    @input="validatePin"
                                    maxlength="6"
                                />
                                <button
                                    type="button"
                                    class="absolute right-4 top-2 text-gray-500 hover:text-gray-700"
                                    @click="togglePasswordVisibility"
                                >
                                    <v-icon :name="showPassword ? 'fa-eye-slash' : 'fa-eye'" class="h-5 w-5" />
                                </button>
                            </div>
                            <span v-if="pinError" class="text-red-500 text-sm">{{ pinError }}</span>
                        </div>
                    </div>
                    <div class="mt-6 flex justify-end space-x-4">
                        <button
                            @click="closeModal"
                            type="button"
                            class="px-4 py-2 text-gray-600 hover:bg-gray-100 rounded-lg"
                        >
                            Cancel
                        </button>
                        <button 
                            type="submit" 
                            class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700"
                            :disabled="!!phoneError || !!pinError"
                        >
                            {{ isEdit ? 'Update User Role' : 'Add User Role' }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue';
import { useAuthStore } from '@/stores/Auth';
import customFetch from '@/utils/customFetch';

import { addIcons } from 'oh-vue-icons';
import { IoAddCircle, FaEdit, IoCloseSharp, FaEye, FaEyeSlash } from 'oh-vue-icons/icons';

addIcons(IoAddCircle, FaEdit, IoCloseSharp, FaEye, FaEyeSlash);

const AuthStore = useAuthStore();
const users = ref([]);
const selectedUsers = ref([]);
const selectAll = ref(false);
const loading = ref(false);
const currentPage = ref(1);
const perPage = 10;
const showModal = ref(false);
const isEdit = ref(false);
const showPassword = ref(false);
const phoneError = ref('');
const pinError = ref('');

const userForm = ref({
    full_name: '',
    phone_number: '',
    email: '',
    role_name: '',
    password: '',
});

// Validation functions
const validatePhoneNumber = () => {
    // Hapus semua karakter non-digit
    let phoneNumber = userForm.value.phone_number.replace(/\D/g, '');
    
    // Hapus prefix 0 atau +62 jika ada
    phoneNumber = phoneNumber.replace(/^(0|62)/, '');
    
    // Batasi panjang nomor
    if (phoneNumber.length > 13) {
        phoneNumber = phoneNumber.slice(0, 13);
    }
    
    userForm.value.phone_number = phoneNumber;
    
    if (phoneNumber.length < 10) {
        phoneError.value = 'Phone number must be at least 10 digits';
    } else if (phoneNumber.length > 13) {
        phoneError.value = 'Phone number cannot exceed 13 digits';
    } else {
        phoneError.value = '';
    }
};

const validatePin = () => {
    // Clear any previous errors
    pinError.value = '';
    
    // If it's edit mode and the field is empty, it's valid
    if (isEdit.value && !userForm.value.password) {
        return;
    }
    
    // Remove any non-digit characters
    userForm.value.password = userForm.value.password.replace(/\D/g, '');
    
    // Only validate if there's a value
    if (userForm.value.password && userForm.value.password.length !== 6) {
        pinError.value = 'PIN must be exactly 6 digits';
    }
};

const validateAndSubmit = () => {
    validatePhoneNumber();
    validatePin();
    
    if (!phoneError.value && !pinError.value) {
        submitForm();
    }
};

// Computed properties
const paginatedUsers = computed(() => {
    const start = (currentPage.value - 1) * perPage;
    return users.value.slice(start, start + perPage);
});

const totalPages = computed(() => Math.ceil(users.value.length / perPage));

// API calls and form handling
const fetchUsers = async () => {
    loading.value = true;
    try {
        const { data } = await customFetch.get('/userRole', {
            headers: { Authorization: `Bearer ${AuthStore.tokenUser}` },
        });
        users.value = data.data.data.map((user) => ({
            user_id: user.user_id,
            full_name: user.full_name,
            phone_number: user.phone_number,
            email: user.email,
            role_name: user.role_name.toLowerCase(),
            created_at: user.created_at,
        }));
    } catch (error) {
        console.error('Error fetching users:', error);
    } finally {
        loading.value = false;
    }
};

const submitForm = async () => {
    try {
        const formData = {
            ...userForm.value,
            phone_number: '+62' + userForm.value.phone_number.replace(/^(0|62)/, '')
        };
        
        if (isEdit.value && !formData.password) {
            delete formData.password;
        }
        
        const endpoint = isEdit.value ? `/userRole/${userForm.value.user_id}` : '/userRole';
        const method = isEdit.value ? 'PUT' : 'POST';
        
        await customFetch({
            method,
            url: endpoint,
            headers: { Authorization: `Bearer ${AuthStore.tokenUser}` },
            data: formData
        });
        
        alert(isEdit.value ? 'User updated successfully!' : 'User added successfully!');
        await fetchUsers();
        closeModal();
    } catch (error) {
        console.error('Error submitting form:', error);
        alert('An error occurred. Please try again.');
    }
};

// UI handling functions
const openAddModal = () => {
    resetForm();
    showModal.value = true;
    isEdit.value = false;
};

const editUser = async (id) => {
    try {
        const { data } = await customFetch.get(`/userRole/${id}`, {
            headers: { Authorization: `Bearer ${AuthStore.tokenUser}` },
        });
        
        const userData = data.data;
        userForm.value = {
            user_id: userData.user_id,
            full_name: userData.full_name,
            phone_number: userData.phone_number.replace(/^\+62/, ''),
            email: userData.email,
            role_name: userData.role.role_name,
            password: userData.password || '' // Simpan PIN jika ada
        };
        
        showModal.value = true;
        isEdit.value = true;
    } catch (error) {
        console.error('Error fetching user for edit:', error);
    }
};

// Delete selected users
const deleteSelectedUsers = async () => {
    if (selectedUsers.value.length === 0) {
        alert('No users selected for deletion.');
        return;
    }

    const confirmed = confirm('Are you sure you want to delete the selected users? This action cannot be undone.');

    if (!confirmed) {
        return;
    }

    try {
        const response = await customFetch.delete('/userRole', {
            headers: { Authorization: `Bearer ${AuthStore.tokenUser}` },
            data: { ids: selectedUsers.value },
        });

        if (response.status === 200) {
            alert(response.data.message || 'Users deleted successfully.');
            selectedUsers.value = [];
            fetchUsers(); // Refresh data after deletion
        } else {
            console.error('Error deleting users:', response);
        }
    } catch (error) {
        console.error('Error deleting users:', error);
        alert('Failed to delete users. Please try again.');
    }
};

const closeModal = () => {
    showModal.value = false;
    phoneError.value = '';
    pinError.value = '';
};

const resetForm = () => {
    userForm.value = {
        full_name: '',
        phone_number: '',
        email: '',
        role_name: '',
        password: '',
    };
    phoneError.value = '';
    pinError.value = '';
};

const pinPlaceholder = computed(() =>
    isEdit.value
        ? 'Enter PIN (If you want to replace it)'
        : 'Enter PIN'
);

const togglePasswordVisibility = () => {
    showPassword.value = !showPassword.value;
};

const toggleSelectAll = () => {
    selectedUsers.value = selectAll.value ? users.value.map(user => user.user_id) : [];
};

const changePage = (page) => {
    currentPage.value = page;
};

const capitalize = (str) => {
    if (!str) return '';
    return str.charAt(0).toUpperCase() + str.slice(1);
};

const formatDate = (dateString) => {
    const options = { year: 'numeric', month: 'long', day: 'numeric' };
    return new Date(dateString).toLocaleDateString(undefined, options);
};

onMounted(() => {
    fetchUsers();
});
</script>