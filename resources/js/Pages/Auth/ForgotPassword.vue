<script setup>
import { Head, useForm, Link } from '@inertiajs/vue3';
import { ArrowLeft, Send, CheckCircle2, MessageCircle } from '@lucide/vue';

defineProps({
  status: String,
});

const adminWaNumber = import.meta.env.VITE_ADMIN_WA_NUMBER || '628123456789';

const form = useForm({
  email: '',
});

const submit = () => {
  form.post('/forgot-password');
};
</script>

<template>
  <Head title="Lupa Password" />

  <div class="flex min-h-screen items-center justify-center bg-background px-4 py-12 font-sans antialiased">
    <!-- Grid Pattern Background -->
    <div
      class="fixed inset-0 pointer-events-none"
      style="
        background-image: linear-gradient(rgba(0,0,0,0.02) 2px, transparent 2px),
          linear-gradient(90deg, rgba(0,0,0,0.02) 2px, transparent 2px);
        background-size: 48px 48px;
      "
    />

    <div class="w-full max-w-md space-y-6 relative z-10">
      <!-- Back to Login -->
      <Link
        href="/login"
        class="inline-flex items-center gap-2 text-sm text-primary font-semibold hover:underline underline-offset-4 transition-colors"
      >
        <ArrowLeft class="h-4 w-4" />
        Kembali ke Login
      </Link>

      <!-- Card -->
      <div class="bg-card border border-border rounded-2xl shadow-card overflow-hidden">
        <!-- Header -->
        <div class="bg-muted/50 border-b border-border p-6">
          <div class="w-16 h-16 bg-card border border-border rounded-xl shadow-sm flex items-center justify-center mb-4 mx-auto">
            <img src="/image/logo-sp.png" alt="Logo SiPinjam" class="w-10 h-10 object-contain" />
          </div>
          <h1 class="text-2xl font-bold text-foreground text-center">
            Lupa Password?
          </h1>
          <p class="text-sm text-muted-foreground text-center mt-2 font-medium max-w-sm mx-auto">
            Tidak masalah. Masukkan alamat email Anda dan kami akan mengirimkan link untuk mengatur ulang password.
          </p>
        </div>

        <div class="p-6 space-y-5">
          <!-- Success Message -->
          <div
            v-if="status"
            class="flex items-start gap-3 bg-emerald-50 border border-emerald-200 p-4 rounded-lg shadow-sm"
          >
            <CheckCircle2 class="h-5 w-5 text-emerald-600 shrink-0 mt-0.5" />
            <p class="text-sm text-emerald-800 font-medium">{{ status }}</p>
          </div>

          <!-- Form -->
          <form @submit.prevent="submit" class="space-y-5">
            <div class="space-y-2">
              <label for="email" class="text-xs font-semibold text-muted-foreground uppercase tracking-wider">Alamat Email</label>
              <input
                id="email"
                v-model="form.email"
                type="email"
                required
                autofocus
                autocomplete="email"
                placeholder="nama@sipinjam.test"
                class="w-full px-4 py-2.5 rounded-lg border border-border text-sm font-medium bg-background text-foreground placeholder:text-muted-foreground outline-none transition-all focus:ring-2 focus:ring-ring focus:ring-offset-2"
              />
              <p v-if="form.errors.email" class="text-xs text-destructive font-medium">{{ form.errors.email }}</p>
            </div>

            <button
              type="submit"
              :disabled="form.processing"
              class="w-full py-2.5 px-4 rounded-lg bg-emerald-600 text-white font-semibold text-sm shadow-sm transition-all hover:bg-emerald-700 focus:ring-2 focus:ring-ring focus:ring-offset-2 disabled:opacity-50 disabled:cursor-not-allowed"
            >
              <span class="flex items-center justify-center gap-2">
                <Send class="h-4 w-4" />
                {{ form.processing ? 'Mengirim...' : 'Kirim Link Reset Password' }}
              </span>
            </button>
          </form>
        </div>
      </div>

      <!-- Footer -->
      <p class="text-center text-xs text-muted-foreground font-medium">
        &copy; 2026 SiPinjam — STITEK Bontang
      </p>
    </div>
  </div>
</template>
