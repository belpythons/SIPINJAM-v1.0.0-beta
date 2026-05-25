<script setup>
import Sidebar from '@/Components/Sidebar.vue';
</script>

<template>
  <div class="min-h-screen bg-slate-50/50">
    <!-- Fixed Sidebar -->
    <Sidebar />

    <!-- Main Content Area (offset by sidebar width) -->
    <main class="ml-64 min-h-screen">
      <slot />
    </main>
  </div>
</template>
