<script setup>
import { Head } from '@inertiajs/vue3';
import UserLayout from '@/Layouts/UserLayout.vue';
import { ShieldCheck } from '@lucide/vue';

defineOptions({ layout: UserLayout });

defineProps({
  versi: { type: Object, default: null },
});

const formatDate = (d) => d ? new Date(d).toLocaleDateString('id-ID', { day: 'numeric', month: 'long', year: 'numeric' }) : '-';
</script>

<template>
  <Head title="Tata Tertib Peminjaman" />

  <div class="max-w-4xl mx-auto px-8 py-10 lg:px-12 flex flex-col gap-8">
    <!-- Header -->
    <div class="mb-2">
      <div class="flex items-center gap-3 mb-2">
        <div class="w-10 h-10 bg-primary/10 rounded-lg flex items-center justify-center">
          <ShieldCheck class="h-5 w-5 text-primary" />
        </div>
        <div>
          <h1 class="text-2xl font-bold tracking-tight text-foreground">Tata Tertib Peminjaman</h1>
          <p class="text-xs text-muted-foreground" v-if="versi">
            Versi {{ versi.versi }} — berlaku sejak {{ formatDate(versi.berlaku_sejak) }}
          </p>
        </div>
      </div>
    </div>

    <div v-if="versi" class="bg-card border border-border rounded-xl shadow-card p-6">
      <p class="text-sm text-foreground leading-relaxed whitespace-pre-wrap">{{ versi.konten }}</p>
    </div>
    <div v-else class="rounded-xl border-2 border-dashed border-border p-10 text-center text-muted-foreground text-sm">
      Tata tertib belum ditetapkan oleh admin.
    </div>
  </div>
</template>
