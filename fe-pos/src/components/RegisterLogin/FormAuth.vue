<template>
    <div class="flex h-screen items-center justify-center bg-neutral overflow-hidden">
        <!-- Left Section -->
        <div class="hidden lg:block w-1/2 h-full relative">
            <!-- Background Image -->
            <img :src="slides[currentSlide].image" alt="Promotional"
                class="absolute inset-0 w-full h-full object-cover" />

            <!-- Gradient Overlay -->
            <div
                class="absolute inset-0 bg-gradient-to-b from-transparent via-black/20 to-black/40 pointer-events-none">
            </div>

            <!-- Quote -->
            <div class="absolute bottom-16 left-4 right-4 p-4 md:p-8 bg-black/40 rounded-md shadow-lg">
                <p class="text-white text-lg md:text-xl font-medium text-center leading-relaxed">
                    {{ slides[currentSlide].quote }}
                </p>
            </div>

            <!-- Slider Navigation -->
            <div class="absolute bottom-6 flex justify-center gap-3 w-full">
                <button v-for="(slide, index) in slides" :key="index" @click="currentSlide = index"
                    class="w-4 h-4 rounded-full border border-white transition-all duration-300"
                    :class="currentSlide === index ? 'bg-white scale-125' : 'bg-transparent hover:bg-white/50'"></button>
            </div>
        </div>

        <!-- Right Section - Auth Form -->
        <div class="w-full md:w-1/2 p-8">
            <div class="mb-8 mt-16">
                <img src="https://cdn2.f-cdn.com/contestentries/392089/3249793/57192d913e1c7_thumb900.jpg"
                    alt="Website POS" class="h-6" />
                <h1 class="text-2xl font-bold mt-6 mb-2">
                    {{ props.isRegister ? 'Sign up POS account' : 'Login to POS account' }}
                </h1>
                <p class="text-gray-600">
                    {{ props.isRegister ? 'Welcome! Please register your account.' : 'Welcome back! Please login to your account.' }}
                </p>
            </div>

            <div class="toast toast-top toast-end" v-if="authStore.isError">
                <div class="alert alert-error">
                    <div class="flex items-center">
                        <v-icon name="ri-alert-line" class="h-6 w-6 mr-2" />
                        <strong class="font-bold mr-2">Verification Failed:</strong>
                    </div>
                    <ul class="text-sm list-disc pl-5">
                        <li v-for="(error, index) in authStore.errMsg.split(', ')" :key="index">
                            {{ error }}
                        </li>
                    </ul>
                </div>
            </div>

            <form @submit.prevent="handleSubmit" class="requ">
                <!-- Name Input (Only for Register) -->
                <div v-if="props.isRegister" class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Name</label>
                    <input v-model="inputForm.full_name" type="text" placeholder="Enter your name"
                        class="w-full p-3 border rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none"
                        required />
                </div>

                <!-- Email Input -->
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Email</label>
                    <input v-model="inputForm.email" type="email" placeholder="ex: yourname@gmail.com"
                        class="w-full p-3 border rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none"
                        required />
                </div>

                <!-- Phone Number Input (Only for Register) -->
                <div v-if="props.isRegister" class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Phone number</label>
                    <div class="flex">
                        <select v-model="countryCode"
                            class="w-20 p-3 border rounded-lg mr-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none"
                            @change="updatePhone">
                            <option value="+62">+62</option>
                        </select>
                        <input v-model="phoneNumber" type="tel" placeholder="ex: 85155040922"
                            class="flex-1 p-3 border rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none"
                            @input="updatePhone" required />
                    </div>
                </div>

                <!-- Password Input -->
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Password</label>
                    <div class="relative">
                        <input v-model="inputForm.password" :type="showPassword ? 'text' : 'password'"
                            placeholder="Type your password here"
                            class="w-full p-3 border rounded-lg pr-10 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none"
                            required />
                        <button type="button" @click="showPassword = !showPassword"
                            class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-500">
                            <span v-if="showPassword">👁️</span>
                            <span v-else>👁️‍🗨️</span>
                        </button>
                    </div>
                </div>

                <!-- Confirm Password Input (Only for Register) -->
                <div v-if="props.isRegister" class="mb-6">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Confirm Password</label>
                    <div class="relative">
                        <input v-model="inputForm.password_confirmation"
                            :type="showConfirmPassword ? 'text' : 'password'" placeholder="Confirm your password"
                            class="w-full p-3 border rounded-lg pr-10 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none"
                            required />
                        <button type="button" @click="showConfirmPassword = !showConfirmPassword"
                            class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-500">
                            <span v-if="showConfirmPassword">👁️</span>
                            <span v-else>👁️‍🗨️</span>
                        </button>
                    </div>
                </div>

                <!-- Forgot Password Link (Only for Login) -->
                <div v-if="!props.isRegister" class="flex justify-end mb-6">
                    <router-link to="/forgot-password" class="text-blue-600 hover:underline text-sm">
                        Forgot Password?
                    </router-link>
                </div>

                <!-- Submit Button -->
                <button type="submit" :disabled="isLoading"
                    class="w-full bg-blue-600 text-white rounded-lg p-3 font-medium hover:bg-blue-700 transition-colors disabled:bg-blue-400 disabled:cursor-not-allowed flex items-center justify-center">
                    <span v-if="isLoading" class="animate-spin mr-2">⌛</span>
                    {{ props.isRegister ? 'Register' : 'Login' }}
                </button>
            </form>

            <!-- Auth Switch Link -->
            <p class="text-center mt-6">
                {{ isRegister ? 'Already have an account?' : "Don't have an account?" }}
                <router-link :to="isRegister ? '/login' : '/register'"
                    class="text-blue-600 font-medium hover:underline">
                    {{ isRegister ? 'Login' : 'Register' }}
                </router-link>
            </p>
        </div>
    </div>
</template>

<script setup>
import { reactive, ref, onMounted, watch } from 'vue';
import { useAuthStore } from '@/stores/Auth';

import { addIcons } from "oh-vue-icons";
import { RiAlertLine } from "oh-vue-icons/icons";

addIcons(RiAlertLine);

const props = defineProps({
    isRegister: Boolean,
});

const isLoading = ref(false);
const authStore = useAuthStore();
const showPassword = ref(false);
const showConfirmPassword = ref(false);
const currentSlide = ref(0);
const countryCode = ref('+62');
const phoneNumber = ref('');

const { loginUser, registerUser } = authStore;

const slides = [
    {
        image: 'src/assets/Image/Image-01.png',
        quote: 'Fashion is like eating, you shouldn\'t stick to the same menu.'
    },
    {
        image: 'src/assets/Image/Image-02.png',
        quote: 'Fashion is like eating, you shouldn\'t stick to the same menu.'
    },
    {
        image: 'src/assets/Image/Image-03.png',
        quote: 'Fashion is like eating, you shouldn\'t stick to the same menu.'
    }
];

const inputForm = reactive({
    full_name: '',
    email: '',
    password: '',
    password_confirmation: '',
    phone_number: ''
});

const updatePhone = () => {
    inputForm.phone_number = countryCode.value + phoneNumber.value;
};

// Reset error message when switching between register and login
onMounted(() => {
    authStore.isError = false;
    authStore.errMsg = '';
});

watch(() => authStore.isError, (newValue) => {
    if (newValue) {
        setTimeout(() => {
            authStore.isError = false;
        }, 5000); // Toast akan hilang setelah 5 detik
    }
});

// Fungsi untuk menangani form submit
const handleSubmit = async () => {
    isLoading.value = true;
    authStore.isError = false; // Reset error

    try {
        if (props.isRegister) {
            await registerUser(inputForm);
            localStorage.setItem('accessRegister', 'true')
        } else {
            await loginUser(inputForm);
        }
    } catch (error) {
        console.error('Error:', error);
        authStore.isError = true;
        authStore.errMsg = error.response?.data?.message || 'An unexpected error occurred.';
    } finally {
        isLoading.value = false;
    }
};
</script>