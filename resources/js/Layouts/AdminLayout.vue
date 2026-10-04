<script setup>
import AdminSidebar from '@/Components/AdminSidebar.vue';
import PwaUpdatePrompt from '@/Components/PwaUpdatePrompt.vue';
</script>

<template>
    <div class="min-h-screen bg-slate-50/50">
        <AdminSidebar />
        <main class="ml-64 min-h-screen">
            <slot />
        </main>

        <!-- PWA Auto-Update Notification -->
        <PwaUpdatePrompt />
    </div>
</template>
