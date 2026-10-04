<script setup>
import AdminSidebar from '@/Components/AdminSidebar.vue';
</script>

<template>
    <div class="min-h-screen bg-slate-50/50">
        <AdminSidebar />
        <main class="ml-64 min-h-screen">
            <slot />
        </main>
    </div>
</template>
