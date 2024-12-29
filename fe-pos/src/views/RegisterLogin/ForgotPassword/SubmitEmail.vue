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

        <!-- Right Section -->
        <div class="w-full md:w-1/2 p-8">
            <!-- Back Button -->
            <button @click="goBack" class="text-blue-600 flex items-center mb-6">
                <v-icon name="md-keyboardbackspace-round" class="h-6 w-6 mr-2" />
                Back
            </button>

            <!-- Heading -->
            <h1 class="text-2xl font-bold mb-2">Forgot password</h1>
            <p class="text-gray-600 mb-6">
                Please use an active email address to send the OTP code.
            </p>

            <!-- Email Input -->
            <div class="mb-6">
                <label class="block text-sm font-medium text-gray-700 mb-1" for="email">Email</label>
                <input v-model="email" type="email" id="email" placeholder="example@domain.com"
                    class="w-full p-3 border rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none"
                    :class="{ 'border-red-500': emailError }" />
                <p v-if="emailError" class="text-sm text-red-500 mt-1">
                    {{ emailErrorMsg }}
                </p>
            </div>

            <!-- Send Button -->
            <button @click="submitForgotPassword" :disabled="isLoading"
                class="w-full bg-blue-600 text-white rounded-lg py-3 font-medium hover:bg-blue-700 transition-colors disabled:bg-blue-400 disabled:cursor-not-allowed flex items-center justify-center">
                <span v-if="isLoading" class="animate-spin mr-2">⌛</span>
                Send
            </button>
        </div>
    </div>

    <!-- Toast Alerts -->
    <div class="toast toast-top toast-end" v-if="successMessage">
        <div class="alert alert-success shadow-lg">
            <div>
                <v-icon name="ai-preregistered" class="h-6 w-6 mr-2" />
                <span>{{ successMessage }}</span>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref } from 'vue';
import { useRouter } from 'vue-router';
import customFetch from '@/utils/customFetch';

import { addIcons } from "oh-vue-icons";
import { AiPreregistered,MdKeyboardbackspaceRound } from "oh-vue-icons/icons";

addIcons(AiPreregistered,MdKeyboardbackspaceRound);

/// State
const email = ref('');
const emailError = ref(false);
const emailErrorMsg = ref('');
const successMessage = ref('');
const isLoading = ref(false); // State untuk loading
const router = useRouter();

// Go back function
const goBack = () => {
    router.push('/login');
};

// Submit Forgot Password
const submitForgotPassword = async () => {
    isLoading.value = true; // Aktifkan loader
    emailError.value = false;
    emailErrorMsg.value = '';

    // Validasi email
    if (!email.value || !/^[\w-\.]+@([\w-]+\.)+[\w-]{2,4}$/.test(email.value)) {
        emailError.value = true;
        emailErrorMsg.value = 'Please enter a valid email address.';
        isLoading.value = false;
        return;
    }

    try {
        const response = await customFetch.post('/forgot-password/send-email', { email: email.value });

        if (response.status === 200) {
            // Simpan email pengguna di localStorage
            localStorage.setItem('userEmail', email.value);
            localStorage.setItem('accessForgotPassword', 'true');
            successMessage.value = 'Email Verified! Redirecting to verify OTP...';
            setTimeout(() => router.push('/verifikasiOTP-forgotPassword'), 2000); // Redirect setelah 2 detik
        }
    } catch (error) {
        // Tampilkan error
        emailError.value = true;
        emailErrorMsg.value = error.response?.data?.message || 'Failed to send OTP. Please try again.';
    } finally {
        isLoading.value = false; // Matikan loader
    }
};

// Slides Data
const currentSlide = ref(0);
const slides = [
    {
        image: 'https://res.cloudinary.com/dasfdo5kq/image/upload/v1735478349/web-pos/Image/h2hiijgbuvgw1el3g8pi.png',
        quote: 'Fashion is like eating, you shouldn\'t stick to the same menu.'
    },
    {
        image: 'https://res.cloudinary.com/dasfdo5kq/image/upload/v1735478349/web-pos/Image/i6z52tpfg9p3hib11ym9.png',
        quote: 'Fashion is like eating, you shouldn\'t stick to the same menu.'
    },
    {
        image: 'https://res.cloudinary.com/dasfdo5kq/image/upload/v1735478347/web-pos/Image/pkhhx2itqmnxcqa7etdx.png',
        quote: 'Fashion is like eating, you shouldn\'t stick to the same menu.'
    }
];
</script>