<script setup>
import { Head, useForm } from '@inertiajs/vue3';
import { Mail, ArrowLeft, Send, CheckCircle2 } from '@lucide/vue';

defineProps({
  status: String,
});

const form = useForm({
  email: '',
});

const submit = () => {
  form.post('/forgot-password');
};
</script>

<template>
  <Head title="Lupa Kata Sandi" />

  <div class="flex min-h-screen items-center justify-center bg-background px-4 py-12 font-sans antialiased">
    <div class="w-full max-w-md space-y-6">
      <!-- Back to Login -->
      <a href="/login" class="inline-flex items-center gap-1.5 text-sm text-muted-foreground hover:text-foreground font-medium transition-colors">
        <ArrowLeft class="h-4 w-4" />
        Kembali ke halaman masuk
      </a>

      <!-- Card -->
      <div class="bg-card border border-border rounded-xl shadow-card overflow-hidden">
        <!-- Header -->
        <div class="p-6 pb-0 space-y-2">
          <div class="w-11 h-11 bg-primary/10 rounded-lg flex items-center justify-center mb-4">
            <Mail class="h-5 w-5 text-primary" />
          </div>
          <h1 class="text-xl font-bold text-foreground">Lupa Kata Sandi</h1>
          <p class="text-sm text-muted-foreground leading-relaxed">
            Masukkan email institusi Anda. Kami akan mengirimkan tautan untuk mereset kata sandi.
          </p>
        </div>

        <!-- Success Message -->
        <div v-if="status" class="mx-6 mt-4 flex items-start gap-3 bg-emerald-50 border border-emerald-200 rounded-lg p-4">
          <CheckCircle2 class="h-5 w-5 text-emerald-600 shrink-0 mt-0.5" />
          <p class="text-sm text-emerald-800 font-medium">{{ status }}</p>
        </div>

        <!-- Form -->
        <form @submit.prevent="submit" class="p-6 space-y-5">
          <div class="space-y-2">
            <label for="email" class="text-sm font-medium text-foreground">Alamat Email</label>
            <div class="relative">
              <Mail class="absolute left-3 top-1/2 -translate-y-1/2 h-4 w-4 text-muted-foreground" />
              <input
                id="email"
                v-model="form.email"
                type="email"
                required
                autofocus
                autocomplete="email"
                placeholder="nama@stitek.ac.id"
                class="w-full pl-10 pr-4 py-2.5 border border-input bg-background text-sm rounded-lg placeholder:text-muted-foreground focus:outline-none focus:ring-2 focus:ring-ring/20 focus:border-ring transition-all"
              />
            </div>
            <p v-if="form.errors.email" class="text-xs text-destructive">{{ form.errors.email }}</p>
          </div>

          <button
            type="submit"
            :disabled="form.processing"
            class="w-full bg-primary text-primary-foreground font-semibold text-sm py-3 rounded-lg shadow-sm transition-all duration-200 hover:opacity-90 disabled:opacity-50 flex items-center justify-center gap-2"
          >
            <Send class="h-4 w-4" />
            {{ form.processing ? 'Mengirim...' : 'Kirim Link Reset' }}
          </button>
        </form>
      </div>

      <!-- Footer -->
      <p class="text-center text-xs text-muted-foreground">
        &copy; 2026 SiPinjam — STITEK Bontang
      </p>
    </div>
  </div>
</template>
