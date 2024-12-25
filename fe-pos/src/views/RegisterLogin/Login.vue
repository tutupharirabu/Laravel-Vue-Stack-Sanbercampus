<template>
    <div class="flex h-screen items-center justify-center bg-neutral">
        <div class="bg-white w-full max-w-8xl">
            <FormAuth :isRegister="false" />
        </div>
    </div>
</template>

<script setup>
import { onMounted } from 'vue';
import FormAuth from '@/components/RegisterLogin/FormAuth.vue';

onMounted(() => {
    localStorage.removeItem('accessForgotPassword');
    localStorage.removeItem('accessRegister');
    localStorage.removeItem('continueToBusiness');
    localStorage.removeItem('isSuccessOTP');
    localStorage.removeItem('isSuccessBusiness');
    localStorage.removeItem('token');
    localStorage.removeItem('user');
});
</script>