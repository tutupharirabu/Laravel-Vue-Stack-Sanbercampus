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

        <!-- Right Section - Success Change Password -->
        <div v-if="isChange" class="w-full md:w-1/2 p-8 flex flex-col items-center">
            <!-- Illustration -->
            <img src="@/assets/Change-New-Password.png" alt="Password Changed" class="w-1/2 max-w-xs mb-8" />

            <!-- Heading -->
            <h1 class="text-2xl font-bold mb-4 text-center">Successfully changed password</h1>
            <p class="text-gray-600 text-center mb-6">
                Your password has been successfully changed. <br />
                Please proceed to the login page by pressing the button below.
            </p>

            <!-- Login Button -->
            <button @click="redirectToLogin"
                class="w-full max-w-sm bg-blue-600 text-white rounded-lg py-3 font-medium hover:bg-blue-700 transition-colors">
                Login
            </button>
        </div>

        <!-- Right Section - Input Password-->
        <div v-else class="w-full md:w-1/2 p-8">
            <!-- Heading -->
            <h1 class="text-2xl font-bold mb-2">Forgot password</h1>
            <p class="text-gray-600 mb-6">
                Please enter new password using combination of character, number and symbol.
            </p>

            <!-- Password Input -->
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-1" for="password">Password</label>
                <div class="relative">
                    <input v-model="password" :type="showPassword ? 'text' : 'password'" id="password"
                        placeholder="Type your password here"
                        class="w-full p-3 border rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none"
                        required />
                    <button type="button" class="absolute inset-y-0 right-3 flex items-center"
                        @click="toggleShowPassword">
                        <span v-if="showPassword">👁️</span>
                        <span v-else>👁️‍🗨️</span>
                    </button>
                </div>
            </div>

            <!-- Confirm Password Input -->
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-1" for="confirmPassword">Confirm
                    Password</label>
                <div class="relative">
                    <input v-model="confirmPassword" :type="showConfirmPassword ? 'text' : 'password'"
                        id="confirmPassword" placeholder="Confirm your password"
                        class="w-full p-3 border rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none"
                        required />
                    <button type="button" class="absolute inset-y-0 right-3 flex items-center"
                        @click="toggleShowConfirmPassword">
                        <span v-if="showConfirmPassword">👁️</span>
                        <span v-else>👁️‍🗨️</span>
                    </button>
                </div>
            </div>

            <!-- Send Button -->
            <button @click="submitResetPassword" :disabled="isSending"
                class="w-full bg-blue-600 text-white rounded-lg py-3 font-medium hover:bg-blue-700 transition-colors disabled:bg-blue-400 disabled:cursor-not-allowed flex items-center justify-center">
                <span v-if="isSending" class="animate-spin mr-2">⌛</span>
                Send
            </button>
        </div>

        <!-- Toast Alerts -->
        <div class="toast toast-top toast-end" v-if="errorMessage">
            <div class="alert alert-error shadow-lg">
                <div>
                    <v-icon name="ri-alert-line" class="h-6 w-6 mr-2" />
                    <span>{{ errorMessage }}</span>
                </div>
            </div>
        </div>

    </div>
</template>

<script setup>
import { ref } from 'vue';
import customFetch from '@/utils/customFetch';
import router from '@/router';

import { addIcons } from "oh-vue-icons";
import { RiAlertLine } from "oh-vue-icons/icons";

addIcons(RiAlertLine);

// State
const password = ref('');
const confirmPassword = ref('');
const showPassword = ref(false);
const showConfirmPassword = ref(false);
const isSending = ref(false);
const errorMessage = ref(''); // Toast untuk error
const isChange = ref(localStorage.getItem('isChange') === 'true')

// Fungsi toggle untuk password
const toggleShowPassword = () => {
    showPassword.value = !showPassword.value;
};

// Fungsi toggle untuk confirm password
const toggleShowConfirmPassword = () => {
    showConfirmPassword.value = !showConfirmPassword.value;
};

// Validasi Password
const validatePassword = () => {
    if (password.value !== confirmPassword.value) {
        errorMessage.value = 'Passwords do not match!';
        return false;
    } else if (password.value.length < 8) {
        errorMessage.value = 'Password must be at least 8 characters.';
        return false;
    }
    errorMessage.value = ''; // Reset error jika validasi lolos
    return true;
};

// Submit Reset Password
const submitResetPassword = async () => {
    if (!validatePassword()) {
        setTimeout(() => {
            errorMessage.value = ''; // Reset toast error setelah 5 detik
        }, 5000);
        return;
    }

    isSending.value = true; // Aktifkan loader
    try {
        const email = localStorage.getItem('userEmail');
        const resetToken = localStorage.getItem('resetToken');

        if (!email || !resetToken) {
            errorMessage.value = 'Email or reset token is missing. Please try again.';
            setTimeout(() => {
                errorMessage.value = ''; // Reset toast error setelah 5 detik
            }, 5000);
            return;
        }

        const response = await customFetch.post('/forgot-password/reset-password', {
            email,
            reset_token: resetToken,
            password: password.value,
            password_confirmation: confirmPassword.value,
        });

        if (response.status === 200) {
            isChange.value = true;
        }
    } catch (error) {
        errorMessage.value = error.response?.data?.message || 'An error occurred during password reset.';
        setTimeout(() => {
            errorMessage.value = ''; // Reset toast error setelah 5 detik
        }, 5000);
    } finally {
        isSending.value = false; // Matikan loader
    }
};

// Redirect to login page
const redirectToLogin = () => {
    localStorage.removeItem('userEmail');
    localStorage.removeItem('resetToken');
    localStorage.removeItem('accessForgotPassword');
    localStorage.removeItem('isChange');
    router.push('/login'); // Redirect to the login page
};

// Slides Data
const currentSlide = ref(0);
const slides = [
    {
        image: 'src/assets/Image/Image-01.png',
        quote: 'Fashion is like eating, you shouldn\'t stick to the same menu.',
    },
    {
        image: 'src/assets/Image/Image-02.png',
        quote: 'Explore new horizons, one step at a time.',
    },
    {
        image: 'src/assets/Image/Image-03.png',
        quote: 'Innovation is the key to success.',
    },
];
</script>