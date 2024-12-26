<template>
    <div class="flex h-screen bg-base-100">
        <!-- Main Content -->
        <div class="flex-grow flex flex-col transition-all duration-300">
            <!-- Header -->
            <Header />

            <!-- Main Content -->
            <main class="flex-grow overflow-auto p-4">
                <RouterView />
            </main>
        </div>
    </div>
</template>

<script setup>
import { RouterView } from "vue-router";
import Header from "@/components/Header.vue";
</script>