<template>
    <div class="flex h-screen items-center justify-center bg-neutral overflow-hidden">
        <!-- Show loading spinner while data is being fetched -->

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

        <!-- Right Section - Admin/Owner Choose -->
        <div v-if="isAdminorOwner" class="w-full md:w-1/2 p-8">
            <!-- Back Button -->
            <button @click="goBack" class="text-blue-600 flex items-center mb-6">
                <v-icon name="md-keyboardbackspace-round" class="h-6 w-6 mr-2" />
                Back
            </button>

            <h1 class="text-2xl font-bold mb-2">Enter Verification Code</h1>
            <p class="text-gray-600 mb-4">
                We have sent a verification code to
                <span class="font-medium">{{ AuthStore.currentUser?.email || 'your email' }}</span>.
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

            <!-- Resend OTP Section -->
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
                <span v-else>Verify</span>
            </button>
        </div>

        <!-- Right Section - Cashier Choose -->
        <div v-else-if="isCashier" class="w-full md:w-1/2 p-8">
            <!-- Back Button -->
            <button @click="goBack" class="text-blue-600 flex items-center mb-6">
                <v-icon name="md-keyboardbackspace-round" class="h-6 w-6 mr-2" />
                Back
            </button>

            <!-- Logo -->
            <div class="flex items-center mb-8">
                <img src="https://cdn2.f-cdn.com/contestentries/392089/3249793/57192d913e1c7_thumb900.jpg"
                    alt="Sanber Fashion Logo" class="h-8 mr-4" />
                <span class="text-lg font-semibold">Sanber Fashion.</span>
            </div>

            <!-- Heading -->
            <h1 class="text-2xl font-bold mb-2">Shift Management</h1>
            <p class="text-gray-600 mb-6">Choose your account to start to work.</p>

            <!-- Notes -->
            <div class="mb-6 p-4 border rounded-lg border-blue-300 bg-blue-50">
                <strong class="block text-sm font-medium mb-1">Notes!</strong>
                <p class="text-sm text-gray-600">
                    If you don't see your account, please contact the admin or the person in charge where you work.
                </p>
            </div>

            <!-- Account List -->
            <div class="space-y-4 mb-6">
                <div v-for="(account, index) in accounts" :key="index" @click="selectAccount(index)"
                    class="flex items-center justify-between p-4 border rounded-lg cursor-pointer hover:border-blue-500"
                    :class="selectedAccount === index ? 'border-blue-500 bg-blue-50' : 'border-gray-300'">
                    <div class="flex items-center">
                        <div class="w-10 h-10 bg-gray-300 rounded-full flex items-center justify-center mr-4">
                            <span class="text-gray-500 text-sm">👤</span>
                        </div>
                        <div>
                            <h3 class="font-medium">{{ account.full_name }}</h3>
                            <p class="text-sm text-gray-600">{{ account.phone_number }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Continue Button -->
            <button @click="continueHandlerPassword" :disabled="selectedAccount === null"
                class="w-full bg-blue-600 text-white rounded-lg py-3 font-medium hover:bg-blue-700 transition-colors disabled:bg-blue-400 disabled:cursor-not-allowed">
                Continue
            </button>
        </div>

        <!-- Right Section - Cashier Choose - Pin Password -->
        <div v-else-if="isCashierPassword" class="w-full md:w-1/2 p-8">
            <!-- Back Button -->
            <button @click="goBack" class="text-blue-600 flex items-center mb-6">
                <v-icon name="md-keyboardbackspace-round" class="h-6 w-6 mr-2" />
                Back
            </button>

            <!-- Header -->
            <h1 class="text-2xl font-bold mb-2">Shift Management</h1>
            <p class="text-gray-600 mb-6">Please input your PIN to validate yourself.</p>

            <!-- PIN Input -->
            <div class="flex justify-between mb-6">
                <input v-for="(pin, index) in pinArray" :key="index" type="password" maxlength="1"
                    class="w-12 h-12 text-center text-lg font-medium border rounded-lg focus:ring-2 focus:ring-blue-500 focus:outline-none"
                    v-model="pinArray[index]" @input="handlePinInput(index, $event)" />
            </div>

            <!-- Continue Button -->
            <button @click="submitPin" :disabled="!isPinValid || isSending"
                class="w-full bg-blue-600 text-white rounded-lg py-3 font-medium hover:bg-blue-700 transition-colors disabled:bg-blue-400 disabled:cursor-not-allowed flex items-center justify-center">
                <span v-if="isSending" class="animate-spin mr-2">⌛</span>
                Continue
            </button>
        </div>

        <!-- Right Section - Role Choose -->
        <div v-else class="w-full md:w-1/2 p-8">
            <div v-if="isLoading" class="flex items-center justify-center">
                <span class="animate-spin border-4 border-blue-600 border-t-transparent rounded-full w-8 h-8"></span>
            </div>

            <div v-else>
                <!-- Logo -->
                <img src="https://cdn2.f-cdn.com/contestentries/392089/3249793/57192d913e1c7_thumb900.jpg"
                    alt="Sanber POS Logo" class="h-8 mb-8" />

                <!-- Heading -->
                <h1 class="text-2xl font-bold mb-2">Welcome to Website PoS.</h1>
                <p class="text-gray-600 mb-4">{{ locationText }}</p>

                <!-- Login Option -->
                <h2 class="text-lg font-medium mb-2">I want login as:</h2>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <!-- Cashier Option -->
                    <label class="block p-4 border rounded-lg cursor-pointer hover:border-blue-500"
                        :class="selectedRole === 'cashier' ? 'border-blue-500 bg-blue-50' : 'border-gray-300'"
                        @click="selectRole('cashier')">
                        <div class="flex items-center mb-2">
                            <input type="radio" name="role" value="cashier" class="mr-2" v-model="selectedRole" />
                            <span class="font-semibold">Cashier</span>
                        </div>
                        <p class="text-sm text-gray-600">
                            If you working as a cashier, select this to continue to your workspace.
                        </p>
                    </label>

                    <!-- Admin/Owner Option -->
                    <label class="block p-4 border rounded-lg cursor-pointer hover:border-blue-500"
                        :class="selectedRole === 'admin' ? 'border-blue-500 bg-blue-50' : 'border-gray-300'"
                        @click="selectRole('admin')">
                        <div class="flex items-center mb-2">
                            <input type="radio" name="role" value="admin" class="mr-2" v-model="selectedRole" />
                            <span class="font-semibold">Admin/Owner</span>
                        </div>
                        <p class="text-sm text-gray-600">
                            If you are an admin/owner, select this menu to access all menu available.
                        </p>
                    </label>
                </div>

                <!-- Continue Button -->
                <div v-if="!isLoading">
                    <button @click="continueHandler" :disabled="!selectedRole || isSending"
                        class="w-full bg-blue-600 text-white rounded-lg py-3 font-medium hover:bg-blue-700 transition-colors mt-6 disabled:bg-blue-400 disabled:cursor-not-allowed flex items-center justify-center">
                        <span v-if="isSending" class="animate-spin mr-2">⌛</span>
                        Continue
                    </button>
                </div>
            </div>
        </div>

        <!-- Toast Alert -->
        <div class="toast toast-top toast-end" v-if="AuthStore.isError">
            <div class="alert alert-error shadow-lg">
                <div>
                    <v-icon name="ri-alert-line" class="h-6 w-6 mr-2" />
                    <span>{{ AuthStore.errMsg || "An unexpected error occurred. Please try again." }}</span>
                </div>
            </div>
        </div>

        <!-- Toast Alert -->
        <div class="toast toast-top toast-end" v-if="isSuccessToast">
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
import { ref, onMounted, watch, computed } from 'vue';
import { useRouter } from 'vue-router';
import { useAuthStore } from '@/stores/Auth';
import customFetch from '@/utils/customFetch';

import { addIcons } from "oh-vue-icons";
import { RiAlertLine, MdKeyboardbackspaceRound, AiPreregistered } from "oh-vue-icons/icons";

addIcons(RiAlertLine, MdKeyboardbackspaceRound, AiPreregistered);

const selectedRole = ref(''); // State untuk menyimpan pilihan role
const router = useRouter();
const AuthStore = useAuthStore();
const isAdminorOwner = ref(false)
const isCashier = ref(false)
const isCashierPassword = ref(false)

// State
const user = ref(null);
const isLoading = ref(true); // Loading state
const locationText = computed(() => {
    if (user.value?.business) {
        const city = user.value.business.city || 'Unknown City';
        const district = user.value.business.district || 'Unknown District';
        return `${city}, ${district}`;
    }
    return 'Location not available';
});

// Fetch user data
const fetchUserData = async () => {
    try {
        const response = await customFetch.get('/auth/me', { headers: { Authorization: `Bearer ${AuthStore.tokenUser}` } });
        user.value = response.data.data;
    } catch (error) {
        console.error('Failed to fetch user data:', error.response?.data?.message || error.message);
    } finally {
        isLoading.value = false; // Turn off loading spinner once the fetch is complete
    }
};

// Pilih role (Cashier atau Admin/Owner)
const selectRole = (role) => {
    selectedRole.value = role;
};

const isSending = ref(false);

// Redirect berdasarkan role yang dipilih
const continueHandler = async () => {
    if (isSending.value) return; // Prevent multiple clicks while fetching
    isSending.value = true;

    try {
        if (selectedRole.value === 'cashier') {
            // Fetch cashiers when the role is cashier
            await fetchCashiers();
            isCashier.value = true; // Proceed to the cashier view
        } else if (selectedRole.value === 'admin') {
            // Fetch OTP for admin
            const response = await customFetch.post('/auth/login/generate-otp-code', {
                email: AuthStore.currentUser.email,
            }, { headers: { Authorization: `Bearer ${AuthStore.tokenUser}` } });

            if (response.status === 200) {
                isAdminorOwner.value = true; // Proceed to admin/owner view
            }
        }
    } catch (error) {
        // Handle errors for both scenarios
        AuthStore.isError = true;
        AuthStore.errMsg = error.response?.data?.message || 'An error occurred. Please try again.';
    } finally {
        isSending.value = false; // Reset loading state
    }
};

const currentSlide = ref(0);
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

// State OTP
const otpArray = ref(["", "", "", "", "", ""]); // OTP input array
const otpError = ref(false); // Error state untuk OTP
const countdown = ref(0); // Timer countdown
const isVerifying = ref(false);
const isResending = ref(false);
const isResendDisabled = ref(false);
const isSuccessToast = ref(false);
const successMessage = ref(''); // Pesan sukses untuk toast

// Handle OTP Input
const handleOtpInput = (index, event) => {
    if (event.target.value.trim() && index < otpArray.value.length - 1) {
        event.target.value = event.target.value.slice(0, 1); // Limit to 1 digit
        const nextInput = event.target.nextElementSibling;
        if (nextInput) nextInput.focus();
    }
};

// Submit Verification
const submitVerification = async () => {
    isVerifying.value = true;
    otpError.value = false; // Reset OTP error state

    try {
        const otpCode = otpArray.value.join(''); // Gabungkan array OTP menjadi string
        const response = await customFetch.post(
            '/auth/login/verification-login',
            { otp_code: otpCode },
            { headers: { Authorization: `Bearer ${AuthStore.tokenUser}` } }
        );

        if (response.status === 200) {
            setTimeout(() => {
                router.push('/dashboard');
            }, 2000); // Delay 2 detik (2000 ms)
        }
    } catch (error) {
        otpError.value = true; // Aktifkan pesan error
        console.error('Verification failed:', error.response?.data?.message || error.message);
    } finally {
        isVerifying.value = false;
    }
};

// Resend OTP
const resendOtp = async () => {
    if (isResendDisabled.value || isResending.value) return;

    isResending.value = true;
    try {
        const response = await customFetch.post('/auth/login/generate-otp-code', {
            email: AuthStore.currentUser.email,
        });

        isSuccessToast.value = true; // Tampilkan toast sukses
        successMessage.value = response.data.message || 'OTP code resent!';
        startCountdown(120); // Mulai hitungan mundur
    } catch (error) {
        AuthStore.isError = true;
        AuthStore.errMsg = error.response?.data?.message || 'Failed to resend OTP.';
    } finally {
        isResending.value = false;
    }
};

// Continue to PIN validation
const continueHandlerPassword = () => {
    if (selectedAccount.value) {
        isCashier.value = false; // Matikan tampilan sebelumnya
        isCashierPassword.value = true; // Aktifkan tampilan PIN
    }
};

// Handle account selection
const selectAccount = (index) => {
    selectedAccount.value = accounts.value[index]; // Simpan akun yang dipilih
};

const accounts = ref([]); // Store the fetched accounts
const selectedAccount = ref(null); // Selected account state

// Fetch cashiers connected to the same business
const fetchCashiers = async () => {
    try {
        const response = await customFetch.get('/userRole', { headers: { Authorization: `Bearer ${AuthStore.tokenUser}` } }); // Endpoint to fetch cashiers
        const allUsers = response.data.data.data || [];
        accounts.value = allUsers.filter(user => user.role_name === 'cashier');
    } catch (error) {
        console.error('Failed to fetch cashiers:', error.response?.data?.message || error.message);
        alert('Failed to fetch accounts. Please try again.');
    }
};

const pinArray = ref(["", "", "", "", "", ""]); // Input PIN

// Validasi PIN
const isPinValid = computed(() => {
    return pinArray.value.every(pin => pin.trim() !== '') && pinArray.value.join('').length === 6;
});

// Handle PIN input
const handlePinInput = (index, event) => {
    const value = event.target.value.replace(/\D/g, ''); // Hanya angka
    pinArray.value[index] = value.slice(0, 1);

    if (value && index < pinArray.value.length - 1) {
        const nextInput = event.target.nextElementSibling;
        if (nextInput) nextInput.focus();
    }
};

const { loginUser } = AuthStore;

// Submit PIN
const submitPin = async () => {
    isSending.value = true;
    try {
        const pin = pinArray.value.join(''); // Gabungkan PIN jadi string
        const inputForm = {
            password: pin, // PIN yang dimasukkan
            email: selectedAccount.value.email, // Kirim ID akun yang dipilih
        };

        localStorage.removeItem('accessLogin');
        await loginUser(inputForm);
    } catch (error) {
        AuthStore.isError = true;
        AuthStore.errMsg = error.response?.data?.message || 'Invalid PIN. Please try again.';
    } finally {
        isSending.value = false;
    }
};

watch(
    () => AuthStore.isError,
    (newValue) => {
        if (newValue) {
            setTimeout(() => {
                AuthStore.isError = false;
            }, 5000); // Toast akan hilang setelah 5 detik
        }
    }
);

watch(
    () => isSuccessToast.value,
    (newValue) => {
        if (newValue) {
            setTimeout(() => {
                isSuccessToast.value = false; // Hilangkan toast sukses
            }, 5000);
        }
    }
);

// Back handler
const goBack = () => {
    isAdminorOwner.value = false
    if (isCashierPassword.value) {
        pinArray.value = ["", "", "", "", "", ""]; // Reset PIN input
        isCashierPassword.value = false;
        isCashier.value = true; // Kembali ke daftar akun
    } else {
        isCashier.value = false; // Kembali ke layar sebelumnya
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

// Fetch data when the component is mounted
onMounted(() => {
    fetchUserData();
});
</script>