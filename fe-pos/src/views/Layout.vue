<template>
    <div>
        <!-- Main Content -->
        <div>
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