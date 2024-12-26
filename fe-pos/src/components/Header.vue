<template>
    <header class="bg-white shadow-md px-6 py-4 flex items-center justify-between z-50">
        <!-- Left Section: Logo -->
        <div class="flex items-center space-x-3">
            <div class="flex items-center p-2 border rounded-lg">
                <v-icon name="bi-shop" class="h-6 w-6 text-gray-700" />
                <span class="ml-2 font-semibold text-gray-800">Website PoS.</span>
            </div>
        </div>

        <!-- Center Section: Search and Navigation -->
        <div class="flex items-center space-x-8 flex-1 mx-8">
            <!-- Search Bar -->
            <div class="relative w-full max-w-md">
                <input type="text" placeholder="Search"
                    class="w-full px-4 py-2 text-sm border rounded-lg focus:ring-2 focus:ring-blue-500 focus:outline-none bg-gray-50" />
                <v-icon name="bi-search" class="absolute right-3 top-1/2 transform -translate-y-1/2 text-gray-400" />
            </div>

            <!-- Navigation Links -->
            <nav class="flex space-x-6">
                <nav class="flex space-x-6">
                    <router-link v-for="(item, index) in filterNavItems" :key="index" :to="item.route"
                        class="text-gray-700 font-medium hover:text-blue-500"
                        :class="{ 'text-blue-600 border-b-2 border-blue-600 pb-1': active === item.name }"
                        @click.prevent="setActive(item.name)">
                        {{ item.name }}
                    </router-link>
                </nav>
            </nav>
        </div>

        <!-- Right Section: Profile -->
        <div class="relative" ref="profileDropdown">
            <button @click="toggleProfileMenu" class="relative focus:outline-none flex items-center">
                <div class="w-10 h-10 rounded-full overflow-hidden bg-gray-200 flex items-center justify-center">
                    <img v-if="profileImage" :src="profileImage" alt="Profile Picture"
                        class="object-cover w-full h-full" />
                    <v-icon v-else name="bi-person" class="h-8 w-8 text-gray-400" />
                </div>
            </button>

            <!-- Profile Dropdown -->
            <transition name="fade">
                <div v-if="isProfileMenuOpen"
                    class="absolute right-0 mt-3 w-48 bg-white shadow-lg rounded-lg overflow-hidden z-10">
                    <!-- Profile Header -->
                    <div class="p-4 border-b flex items-center justify-between">
                        <div>
                            <h3 class="font-semibold text-gray-800">{{ currentUser.name || 'Unknown User' }}</h3>
                            <p class="text-sm text-gray-500">{{ currentUser.role || 'No Role Assigned' }}</p>
                        </div>
                        <router-link to="/profile" class="text-gray-500 hover:text-gray-700">
                            <v-icon name="bi-chevron-right" class="h-5 w-5" />
                        </router-link>
                    </div>

                    <!-- Menu Items -->
                    <div>
                        <!-- Profile Menu -->
                        <div :class="filterProfileMenu.length > 0 ? 'border-b' : ''">
                            <router-link v-for="menu in filterProfileMenu" :key="menu.name" :to="menu.route"
                                class="flex items-center px-4 py-2 my-2 hover:bg-gray-100 text-gray-700">
                                <v-icon :name="menu.icon" class="h-5 w-5 mr-3" />
                                {{ menu.name }}
                            </router-link>
                        </div>

                        <!-- Setting -->
                        <router-link to="/setting"
                            class="flex items-center px-4 py-2 mt-2 hover:bg-gray-100 text-gray-700">
                            <v-icon name="bi-gear" class="h-5 w-5 mr-3" />
                            Setting
                        </router-link>

                        <!-- End My Shift -->
                        <button @click="endShift"
                            class="flex items-center px-4 py-2 mb-2 hover:bg-gray-100 text-red-600 w-full text-left">
                            <v-icon name="bi-box-arrow-left" class="h-5 w-5 mr-3" />
                            End My Shift
                        </button>
                    </div>
                </div>
            </transition>
        </div>
    </header>
</template>


<script setup>
import { ref, onMounted, onBeforeUnmount, computed } from 'vue';
import { useAuthStore } from "@/stores/Auth";
import { listMenu } from '@/utils/listMenu.js';

import { addIcons } from 'oh-vue-icons';
import { BiShop, BiSearch, BiPerson, BiGear, BiBoxArrowLeft, BiPersonCircle, BiChevronRight } from 'oh-vue-icons/icons';

addIcons(BiShop, BiSearch, BiPerson, BiGear, BiBoxArrowLeft, BiPersonCircle, BiChevronRight);

const AuthStore = useAuthStore();
const currentUser = computed(() => AuthStore.currentUser); // Mengakses state `currentUser`
const { logoutUser } = AuthStore;

const profileImage = ref(''); // Path gambar profil, kosong untuk default
const isProfileMenuOpen = ref(false); // Toggle untuk menu profil
const profileDropdown = ref(null); // Referensi ke elemen dropdown

import { listNav, active, setActive, initializeActive } from '@/utils/listNav'

initializeActive(AuthStore.currentUser);

const filterNavItems = computed(() => {
    return listNav.filter((item) => {
        // Sembunyikan navigasi "Cashier" jika role bukan "Cashier"
        if (item.name === "Cashier" && currentUser?.role !== "Cashier") {
            return false;
        }

        // Sembunyikan navigasi selain "Cashier" jika role adalah "Cashier"
        if (item.name !== "Cashier" && currentUser?.role === "Cashier") {
            return false;
        }

        // Tampilkan semua navigasi lainnya
        return true;
    });
});

const filterProfileMenu = computed(() => {
    return listMenu.filter((menu) => {
        // Menyembunyikan 'User Role' untuk Cashier
        if (menu.name === 'User Role' && currentUser.value.role === 'Cashier') {
            return false;
        }
        return true;
    });
});

const toggleProfileMenu = () => {
    isProfileMenuOpen.value = !isProfileMenuOpen.value;
};

const closeProfileMenu = () => {
    isProfileMenuOpen.value = false;
};

// Klik di luar dropdown untuk menutup
const handleClickOutside = (event) => {
    if (profileDropdown.value && !profileDropdown.value.contains(event.target)) {
        closeProfileMenu();
    }
};

// Tambahkan event listener saat komponen dimuat
onMounted(() => {
    document.addEventListener('click', handleClickOutside);
});

// Hapus event listener saat komponen dihancurkan
onBeforeUnmount(() => {
    document.removeEventListener('click', handleClickOutside);
});

const endShift = () => {
    logoutUser();
};
</script>