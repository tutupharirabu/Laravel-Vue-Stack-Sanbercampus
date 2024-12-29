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

        <div class="w-full md:w-1/2 p-8">
            <h1 class="text-2xl font-bold mb-2">Enter Verification Code</h1>
            <p class="text-gray-600 mb-4">
                We have sent a verification code to <span class="font-medium">{{ email || 'your email' }}</span>.
                Please check your email and enter the code below.
            </p>

            <!-- OTP Input Boxes -->
            <div class="flex justify-between mb-4">
                <input v-for="(digit, index) in otpArray" :key="index" type="password" maxlength="1"
                    class="w-12 h-12 text-center text-lg font-medium border rounded-lg focus:ring-2 focus:ring-blue-500 focus:outline-none"
                    :class="{ 'border-red-500 focus:ring-red-500': otpError }" v-model="otpArray[index]"
                    @input="handleOtpInput(index, $event)" />
            </div>
            <p v-if="otpError" class="text-red-600 text-sm font-medium mt-2">
                OTP code doesn’t match!
            </p>
            <p v-if="isResendDisabled" class="text-sm text-gray-600 mb-4 mt-4" :class="{ 'mt-6': otpError }">
                Please wait {{ countdown }} seconds to resend OTP.
            </p>
            <p v-if="!isResendDisabled" class="text-sm text-blue-600 font-medium cursor-pointer mb-4 mt-4"
                :class="{ 'mt-6': otpError }" @click="resendOtp">
                Resend OTP
            </p>

            <!-- Verify Button -->
            <button @click="submitVerification" :disabled="isVerifying || otpArray.some(digit => digit.trim() === '')"
                class="w-full bg-blue-600 text-white rounded-lg py-3 font-medium hover:bg-blue-700 transition-colors disabled:bg-blue-400 disabled:cursor-not-allowed flex items-center justify-center">
                <span v-if="isVerifying" class="animate-spin mr-2">⌛</span>
                Verify
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
        <div class="toast toast-top toast-end" v-if="successMessage">
            <div class="alert alert-success shadow-lg">
                <div>
                    <v-icon name="ai-preregistered" class="h-6 w-6 mr-2" />
                    <span>{{ successMessage }}</span>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref, watch } from 'vue';
import { useRouter } from 'vue-router';
import customFetch from '@/utils/customFetch';

import { addIcons } from "oh-vue-icons";
import { RiAlertLine, AiPreregistered } from "oh-vue-icons/icons";

addIcons(RiAlertLine, AiPreregistered);

// States
const otpArray = ref(["", "", "", "", "", ""]);
const otpError = ref(false);
const countdown = ref(0);
const isVerifying = ref(false);
const isResending = ref(false);
const isResendDisabled = ref(false);
const successMessage = ref('');
const errorMessage = ref('');
const email = ref(localStorage.getItem('userEmail')); // Ambil email dari localStorage

const router = useRouter();

let errorTimeout;

// Handle OTP Input
const handleOtpInput = (index, event) => {
    if (event.target.value.trim() && index < otpArray.value.length - 1) {
        event.target.value = event.target.value.slice(0, 1); // Limit to 1 digit
        const nextInput = event.target.nextElementSibling;
        if (nextInput) nextInput.focus();
    }
};

// Simulasikan error dan reset setelah 5 detik
const triggerError = () => {
    otpError.value = true;

    // Batasi hanya 5 detik
    if (errorTimeout) clearTimeout(errorTimeout); // Hapus timeout sebelumnya jika ada
    errorTimeout = setTimeout(() => {
        otpError.value = false; // Reset error state
    }, 5000);
};
// Submit Verification
const submitVerification = async () => {
    isVerifying.value = true;
    otpError.value = false; // Reset OTP error state
    errorMessage.value = ''; // Reset error message

    try {
        const otpCode = otpArray.value.join(''); // Gabungkan array OTP menjadi string
        const response = await customFetch.post('/forgot-password/verify-otp-code', {
            otp_code: otpCode,
            email: email.value,
        });

        if (response.status === 200) {
            const resetToken = response.data.reset_token;
            localStorage.setItem('resetToken', resetToken);
            successMessage.value = 'OTP Verified! Redirecting to reset password...';
            setTimeout(() => router.push('/resetPassword'), 2000); // Redirect setelah 2 detik
        }
    } catch (error) {
        triggerError();
        otpError.value = true; // Aktifkan pesan error
    } finally {
        isVerifying.value = false;
    }
};

// Resend OTP
const resendOtp = async () => {
    if (isResendDisabled.value || isResending.value) return;

    isResending.value = true;
    successMessage.value = ''; // Reset success message
    errorMessage.value = ''; // Reset error message

    try {
        const response = await customFetch.post('/forgot-password/send-email', {
            email: email.value,
        });

        successMessage.value = response.data.message || 'OTP code resent!';
        startCountdown(120); // Mulai hitungan mundur
    } catch (error) {
        errorMessage.value = error.response?.data?.message || 'Failed to resend OTP.';
    } finally {
        isResending.value = false;
    }
};

// Start Countdown
const startCountdown = (seconds) => {
    countdown.value = seconds;
    isResendDisabled.value = true;
    const timer = setInterval(() => {
        countdown.value -= 1;
        if (countdown.value <= 0) {
            clearInterval(timer);
            isResendDisabled.value = false;
        }
    }, 1000);
};

// Watchers
watch(
    () => successMessage.value,
    (newValue) => {
        if (newValue) {
            setTimeout(() => {
                successMessage.value = '';
            }, 5000); // Hilangkan pesan sukses setelah 5 detik
        }
    }
);

watch(
    () => errorMessage.value,
    (newValue) => {
        if (newValue) {
            setTimeout(() => {
                errorMessage.value = '';
            }, 5000); // Toast akan hilang setelah 5 detik
        }
    }
);

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