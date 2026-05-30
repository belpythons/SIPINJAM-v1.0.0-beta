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

  <div class="flex min-h-screen items-center justify-center bg-gray-100 px-4 py-12 font-sans antialiased">
    <!-- Grid Pattern Background -->
    <div
      class="fixed inset-0 pointer-events-none"
      style="
        background-image: linear-gradient(rgba(0,0,0,0.03) 2px, transparent 2px),
          linear-gradient(90deg, rgba(0,0,0,0.03) 2px, transparent 2px);
        background-size: 48px 48px;
      "
    />

    <div class="w-full max-w-md space-y-6 relative z-10">
      <!-- Back to Login -->
      <Link
        href="/login"
        class="inline-flex items-center gap-2 text-sm text-black font-black uppercase tracking-wider hover:text-blue-600 transition-colors border-b-4 border-black pb-1"
      >
        <ArrowLeft class="h-4 w-4 stroke-[3]" />
        Kembali ke Login
      </Link>

      <!-- Card -->
      <div class="bg-white border-4 border-black shadow-[8px_8px_0px_rgba(0,0,0,1)] overflow-hidden">
        <!-- Header -->
        <div class="bg-blue-600 border-b-4 border-black p-6">
          <div class="w-16 h-16 bg-white border-4 border-black shadow-[4px_4px_0px_rgba(0,0,0,0.3)] flex items-center justify-center mb-4 mx-auto">
            <img src="/image/logo-sp.png" alt="Logo SiPinjam" class="w-10 h-10 object-contain" />
          </div>
          <h1 class="text-2xl font-black text-white text-center drop-shadow-[2px_2px_0px_rgba(0,0,0,0.4)]">
            Lupa Password?
          </h1>
          <p class="text-sm text-white/90 text-center mt-2 font-medium max-w-sm mx-auto">
            Tidak masalah. Masukkan alamat email Anda dan kami akan mengirimkan link untuk mengatur ulang password.
          </p>
        </div>

        <div class="p-6 space-y-5">
          <!-- Success Message -->
          <div
            v-if="status"
            class="flex items-start gap-3 bg-green-100 border-4 border-black p-4 shadow-[4px_4px_0px_rgba(0,0,0,1)]"
          >
            <CheckCircle2 class="h-5 w-5 text-green-700 shrink-0 mt-0.5 stroke-[3]" />
            <p class="text-sm text-green-800 font-bold">{{ status }}</p>
          </div>

          <!-- Form -->
          <form @submit.prevent="submit" class="space-y-5">
            <div class="space-y-2">
              <label for="email" class="text-sm font-black text-black uppercase tracking-wider">Alamat Email</label>
              <input
                id="email"
                v-model="form.email"
                type="email"
                required
                autofocus
                autocomplete="email"
                placeholder="nama@sipinjam.test"
                class="w-full px-4 py-3 border-4 border-black text-sm font-medium bg-white placeholder:text-gray-400 outline-none focus:ring-4 focus:ring-blue-300 focus:border-blue-500 transition-all"
              />
              <p v-if="form.errors.email" class="text-xs text-red-600 font-bold">{{ form.errors.email }}</p>
            </div>

            <button
              type="submit"
              :disabled="form.processing"
              class="w-full py-3.5 bg-blue-600 text-white font-black text-sm uppercase tracking-wider border-4 border-black shadow-[4px_4px_0px_rgba(0,0,0,1)] transition-all duration-150 hover:translate-x-[2px] hover:translate-y-[2px] hover:shadow-[2px_2px_0px_rgba(0,0,0,1)] active:translate-x-[4px] active:translate-y-[4px] active:shadow-none disabled:opacity-50"
            >
              <span class="flex items-center justify-center gap-2">
                <Send class="h-4 w-4 stroke-[3]" />
                {{ form.processing ? 'Mengirim...' : 'Kirim Link Reset Password' }}
              </span>
            </button>
          </form>

          <!-- WhatsApp Contact -->
          <div class="border-t-4 border-black pt-5 text-center">
            <p class="text-xs text-gray-500 mb-3 font-semibold">Tidak bisa mengakses email? Hubungi Admin langsung:</p>
            <a
              :href="`https://wa.me/${adminWaNumber}?text=Halo%20Admin%2C%20saya%20lupa%20password%20akun%20SiPinjam%20saya.`"
              target="_blank"
              class="inline-flex items-center gap-2 bg-green-500 text-white font-black text-xs uppercase tracking-wider px-5 py-2.5 border-4 border-black shadow-[4px_4px_0px_rgba(0,0,0,1)] transition-all duration-150 hover:translate-x-[2px] hover:translate-y-[2px] hover:shadow-[2px_2px_0px_rgba(0,0,0,1)] active:translate-x-[4px] active:translate-y-[4px] active:shadow-none"
            >
              <MessageCircle class="h-4 w-4" />
              Hubungi Admin via WhatsApp
            </a>
          </div>
        </div>
      </div>

      <!-- Footer -->
      <p class="text-center text-xs text-gray-500 font-bold">
        &copy; 2026 SiPinjam — STITEK Bontang
      </p>
    </div>
  </div>
</template>
