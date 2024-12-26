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

        <!-- Right Section After OTP -->
        <div v-if="isSuccessOTP" class="w-full md:w-1/2 p-8">
            <h1 class="text-2xl font-bold mb-4">Additional Information</h1>
            <p class="text-gray-600 mb-6">
                Please fill all required data to continue using this app smoothly.
            </p>

            <!-- Radio Button Options -->
            <div>
                <h2 class="text-lg font-medium mb-2">What do I use this app for?</h2>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <!-- Personal Option -->
                    <label class="block p-4 border rounded-lg cursor-pointer hover:border-blue-500"
                        :class="selectedOption === 'personal' ? 'border-blue-500 bg-blue-50' : 'border-gray-300'"
                        @click="selectOption('personal')">
                        <div class="flex items-center mb-2">
                            <input type="radio" name="appUsage" value="personal" class="mr-2"
                                v-model="selectedOption" />
                            <span class="font-semibold">Personal</span>
                        </div>
                        <p class="text-sm text-gray-600">
                            For those who are just want to explore the app’s feature.
                        </p>
                    </label>

                    <!-- Business Owner Option -->
                    <label class="block p-4 border rounded-lg cursor-pointer hover:border-blue-500"
                        :class="selectedOption === 'business' ? 'border-blue-500 bg-blue-50' : 'border-gray-300'"
                        @click="selectOption('business')">
                        <div class="flex items-center mb-2">
                            <input type="radio" name="appUsage" value="business" class="mr-2"
                                v-model="selectedOption" />
                            <span class="font-semibold">Business Owner</span>
                        </div>
                        <p class="text-sm text-gray-600">
                            For those who already have a business and want to use our product.
                        </p>
                    </label>
                </div>
            </div>

            <!-- Continue Button -->
            <button @click="continueHandler" :disabled="!selectedOption"
                class="w-full bg-blue-600 text-white rounded-lg py-3 font-medium hover:bg-blue-700 transition-colors mt-6 disabled:bg-blue-400 disabled:cursor-not-allowed">
                Continue
            </button>
        </div>

        <!-- Right Section -->
        <div v-else-if="continueToBusiness" class="w-full md:w-1/2 p-8">
            <h1 class="text-2xl font-bold mb-2">Business Information</h1>
            <p class="text-gray-600 mb-6">
                Please fill all required data to continue using this app smoothly.
            </p>

            <!-- Business Name -->
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-1">Business Name</label>
                <input v-model="form.businessName" type="text" placeholder="Enter your business name"
                    class="w-full p-3 border rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none"
                    required />
            </div>

            <!-- City -->
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-1">City</label>
                <select v-model="form.city"
                    class="w-full p-3 border rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none">
                    <option disabled value="">Select city</option>
                    <option v-for="city in cities" :key="city" :value="city">{{ city }}</option>
                </select>
            </div>

            <!-- District and Postal Code -->
            <div class="grid grid-cols-2 gap-4 mb-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">District</label>
                    <select v-model="form.district"
                        class="w-full p-3 border rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none">
                        <option disabled value="">Select District</option>
                        <option v-for="district in districts" :key="district" :value="district">{{ district }}</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Postal Code</label>
                    <input v-model="form.postalCode" type="text" placeholder="Postal code"
                        class="w-full p-3 border rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none" />
                </div>
            </div>

            <!-- Upload Image -->
            <div class="mb-6">
                <label class="block text-sm font-medium text-gray-700 mb-1">Owner's ID Card</label>
                <input type="file" class="hidden" id="upload" @change="handleFileUpload" required />
                <label for="upload"
                    class="flex items-center justify-center border-2 border-dashed border-gray-300 rounded-lg p-6 cursor-pointer">
                    <span v-if="!previewImage" class="text-blue-600 font-medium flex items-center">
                        <v-icon name="bi-cloud-upload-fill" class="h-6 w-6 mr-2" />
                        Upload Image
                    </span>
                    <div v-else class="flex flex-col items-center">
                        <img v-if="previewImageType === 'image'" :src="previewImage" alt="Preview"
                            class="w-20 h-20 object-cover rounded mb-2" />
                        <span v-if="previewImageType === 'pdf'" class="text-gray-600 text-sm">
                            {{ form.value.ownerIdCard.name }}
                        </span>
                        <button @click.prevent="removeFile"
                            class="text-red-600 text-xs mt-2 hover:underline">Remove</button>
                    </div>
                </label>
            </div>

            <!-- Continue Button -->
            <button @click="submitForm" :disabled="isSubmitting || !isFormValid"
                class="w-full bg-blue-600 text-white rounded-lg py-3 font-medium hover:bg-blue-700 transition-colors disabled:bg-blue-400 disabled:cursor-not-allowed flex items-center justify-center">
                <span v-if="isSubmitting" class="animate-spin mr-2">⌛</span>
                Continue
            </button>
        </div>

        <div v-else-if="isSuccessBusiness" class="w-full md:w-1/2 p-8">
            <!-- Success Icon -->
            <img src="@/assets/Change-Data-Success.png" alt="Success Icon" class="w-40 mx-auto mb-8" />

            <!-- Success Message -->
            <h1 class="text-2xl font-bold mb-4">
                Business information has been saved successfully
            </h1>
            <p class="text-gray-600 mb-8">
                You have successfully completed your business information. Please continue to the main page.
            </p>

            <!-- Continue Button -->
            <button @click="redirectToDashboardAdminOwner"
                class="bg-blue-600 text-white rounded-lg px-8 py-3 font-medium hover:bg-blue-700 transition-colors">
                Continue
            </button>
        </div>

        <!-- Toast Alert for Errors -->
        <div class="toast toast-top toast-end" v-if="isError">
            <div class="alert alert-error shadow-lg">
                <div>
                    <v-icon name="ri-alert-line" class="h-6 w-6 mr-2" />
                    <span>{{ errorMsg || "An unexpected error occurred. Please try again." }}</span>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref, computed } from 'vue';
import { useRouter } from 'vue-router';
import { useAuthStore } from '@/stores/Auth';
import customFetch from '@/utils/customFetch';

const AuthStore = useAuthStore();

import { addIcons } from "oh-vue-icons";
import { RiAlertLine, BiCloudUploadFill } from "oh-vue-icons/icons";

addIcons(RiAlertLine, BiCloudUploadFill);

// Form State
const form = ref({
    businessName: '',
    city: '',
    district: '',
    postalCode: '',
    ownerIdCard: null,
});

const isSubmitting = ref(false);

const isSuccessOTP = ref(localStorage.getItem('isSuccessOTP') === 'true');
const isSuccessBusiness = ref(localStorage.getItem('isSuccessBusiness') === 'true');
const continueToBusiness = ref(localStorage.getItem('continueToBusiness') === 'true');

// Dropdown Options
const cities = ['Jakarta', 'Surabaya', 'Bandung', 'Medan'];
const districts = ['Central', 'North', 'South', 'East', 'West'];

// Validasi Form
const isFormValid = computed(() => {
    return (
        form.value.businessName.trim() &&
        form.value.city &&
        form.value.district &&
        form.value.postalCode.trim() &&
        form.value.ownerIdCard // Validasi file terupload
    );
});

// Router
const router = useRouter();

// Upload Image Handler
const previewImage = ref(null);
const previewImageType = ref(''); // 'image' or 'pdf'

const handleFileUpload = (event) => {
    const file = event.target.files[0];
    if (file && ['image/jpeg', 'image/png', 'application/pdf'].includes(file.type) && file.size <= 2048 * 1024) {
        form.value.ownerIdCard = file; // Simpan file yang valid
        if (file.type === 'application/pdf') {
            previewImage.value = null; // Tidak ada preview untuk PDF
            previewImageType.value = 'pdf';
        } else {
            const reader = new FileReader();
            reader.onload = (e) => {
                previewImage.value = e.target.result; // Set preview
                previewImageType.value = 'image';
            };
            reader.readAsDataURL(file); // Generate preview
        }
    } else {
        alert('Invalid file. Please upload a jpg, png, or pdf file with a maximum size of 2MB.');
        event.target.value = ''; // Reset input file jika file tidak valid
        previewImage.value = null; // Reset preview
        previewImageType.value = '';
    }
};

const removeFile = () => {
    form.value.ownerIdCard = null;
    previewImage.value = null;
    previewImageType.value = '';
};

const isError = ref(false); // State untuk error status
const errorMsg = ref(''); // State untuk pesan error

const submitForm = async () => {
    if (!isFormValid.value) return;
    isSubmitting.value = true;
    isError.value = false; // Reset error state
    errorMsg.value = ''; // Reset error message
    try {
        const formData = new FormData();
        formData.append('business_name', form.value.businessName);
        formData.append('city', form.value.city);
        formData.append('district', form.value.district);
        formData.append('postal_code', form.value.postalCode);
        formData.append('photo_id_card', form.value.ownerIdCard);

        const response = await customFetch.post('/business/additionalData', formData, {
            headers: {
                Authorization: `Bearer ${AuthStore.tokenUser}`,
            },
        });

        localStorage.setItem('isSuccessBusiness', 'true');
        isSuccessBusiness.value = true;
        continueToBusiness.value = false;
    } catch (error) {
        isError.value = true; // Aktifkan toast error
        errorMsg.value = error.response?.data?.message || 'Failed to submit business data.';
        console.error('Failed to submit form:', error.response?.data?.message || error.message);
    } finally {
        isSubmitting.value = false; // Akhiri state loading
        setTimeout(() => {
            isError.value = false; // Hilangkan toast setelah 5 detik
        }, 5000);
    }
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

const selectedOption = ref('');

const continueHandler = () => {
    if (selectedOption.value === 'personal') {
        router.push('#'); // Redirect untuk opsi personal
    } else if (selectedOption.value === 'business') {
        localStorage.setItem('continueToBusiness', 'true');
        continueToBusiness.value = true;
        isSuccessOTP.value = false;
    }
};

const redirectToDashboardAdminOwner = () => {
    router.push('/dashboard'); // Redirect ke dashboard atau halaman lain
};
</script>