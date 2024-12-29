import { ref } from 'vue';

export const active = ref(''); // State aktif untuk navigasi

// List navigasi
export const listNav = [
    { name: 'Dashboard', route: '/dashboard' },
    { name: 'Cashier', route: '/dashboard/cashier' },
    { name: 'Product', route: '/dashboard/product' },
    { name: 'Transactions', route: '/dashboard/transactions' },
];

// Fungsi untuk mengatur link aktif
export const setActive = (link) => {
    active.value = link; // Set navigasi aktif
};

// Fungsi untuk menentukan default navigasi berdasarkan role
export const setDefaultActiveByRole = (role) => {
    if (role === 'Cashier') {
        active.value = 'Cashier';
    } else {
        active.value = 'Dashboard'; // Default untuk role lainnya
    }
};

// Inisialisasi navigasi aktif (dipanggil di awal aplikasi)
export const initializeActive = (authUser) => {
    const role = authUser?.role || null; // Periksa apakah authUser memiliki role
    setDefaultActiveByRole(role);
};