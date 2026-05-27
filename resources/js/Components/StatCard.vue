<script setup>
import { computed } from 'vue';

const props = defineProps({
  label: {
    type: String,
    required: true,
  },
  value: {
    type: [Number, String],
    required: true,
  },
  icon: {
    type: Object,
    required: true,
  },
  color: {
    type: String,
    default: 'from-blue-500 to-blue-600',
  },
  bgLight: {
    type: String,
    default: 'bg-blue-50',
  },
  textColor: {
    type: String,
    default: 'text-blue-600',
  },
});
</script>

<template>
  <div class="group relative overflow-hidden rounded-2xl border border-slate-200/80 bg-white p-5 shadow-sm transition-all duration-300 hover:shadow-md hover:-translate-y-0.5">
    <!-- Top Gradient Accent -->
    <div :class="['absolute inset-x-0 top-0 h-1 bg-gradient-to-r', color]" />
    
    <div class="flex items-start justify-between">
      <div class="space-y-1">
        <p class="text-xs font-semibold uppercase tracking-wider text-slate-400">
          {{ label }}
        </p>
        <p class="text-2xl font-bold tabular-nums text-slate-900 sm:text-3xl">
          {{ value }}
        </p>
      </div>
      <div :class="['flex h-10 w-10 items-center justify-center rounded-xl transition-transform group-hover:scale-110 shrink-0', bgLight]">
        <component :is="icon" :class="['h-5 w-5', textColor]" />
      </div>
    </div>
  </div>
</template>
