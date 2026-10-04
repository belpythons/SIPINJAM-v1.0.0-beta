<script setup>
import { needRefresh, updateServiceWorker } from '@/pwa';
import { RefreshCw, Sparkles, X } from '@lucide/vue';
import { Button } from '@/components/ui/button';

const handleUpdate = () => {
  if (updateServiceWorker.value) {
    updateServiceWorker.value();
  }
};

const dismissUpdate = () => {
  needRefresh.value = false;
};
</script>

<template>
  <Transition
    enter-active-class="transition duration-300 ease-out"
    enter-from-class="opacity-0 translate-y-4 sm:translate-y-0 sm:translate-x-8"
    enter-to-class="opacity-100 translate-y-0 sm:translate-x-0"
    leave-active-class="transition duration-200 ease-in"
    leave-from-class="opacity-100 translate-y-0 sm:translate-x-0"
    leave-to-class="opacity-0 translate-y-4 sm:translate-y-0 sm:translate-x-8"
  >
    <div
      v-if="needRefresh"
      class="fixed bottom-6 right-6 z-[70] max-w-sm w-[calc(100%-3rem)] rounded-2xl border border-blue-200 bg-white p-4 shadow-xl shadow-blue-500/10"
    >
      <div class="flex items-start gap-3">
        <div class="flex h-9 w-9 shrink-0 items-center justify-center rounded-xl bg-blue-50 text-blue-600 border border-blue-100">
          <Sparkles class="h-4 w-4" />
        </div>
        <div class="min-w-0 flex-1">
          <p class="text-sm font-bold text-slate-900">Pembaruan Tersedia</p>
          <p class="text-xs text-slate-500 mt-0.5 leading-relaxed">
            Versi terbaru SIPINJAM telah siap. Muat ulang sekarang untuk menikmati peningkatan performa.
          </p>
          <div class="mt-3 flex items-center gap-2">
            <Button
              size="sm"
              @click="handleUpdate"
              class="h-8 gap-1.5 rounded-lg bg-blue-600 hover:bg-blue-700 text-white text-xs font-semibold px-3"
            >
              <RefreshCw class="h-3 w-3" />
              Perbarui Sekarang
            </Button>
            <Button
              variant="ghost"
              size="sm"
              @click="dismissUpdate"
              class="h-8 text-xs text-slate-500 hover:text-slate-700"
            >
              Nanti
            </Button>
          </div>
        </div>
        <button
          @click="dismissUpdate"
          class="shrink-0 text-slate-400 hover:text-slate-600 transition-colors p-1"
        >
          <X class="h-4 w-4" />
        </button>
      </div>
    </div>
  </Transition>
</template>
