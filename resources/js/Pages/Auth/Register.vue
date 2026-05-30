<script setup>
import { Head, useForm, Link } from '@inertiajs/vue3';
import { Eye, EyeOff, UserPlus, ArrowLeft } from '@lucide/vue';
import { ref } from 'vue';

const showPassword = ref(false);
const showConfirmPassword = ref(false);

const form = useForm({
  name: '',
  email: '',
  password: '',
  password_confirmation: '',
});

const submit = () => {
  form.post('/register', {
    onFinish: () => {
      form.reset('password', 'password_confirmation');
    },
  });
};
</script>

<template>
  <Head title="Daftar Akun" />

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
        class="inline-flex items-center gap-2 text-sm text-black font-black uppercase tracking-wider hover:text-violet-600 transition-colors border-b-4 border-black pb-1"
      >
        <ArrowLeft class="h-4 w-4 stroke-[3]" />
        Kembali ke Login
      </Link>

      <!-- Card -->
      <div class="bg-white border-4 border-black shadow-[8px_8px_0px_rgba(0,0,0,1)] overflow-hidden">
        <!-- Header -->
        <div class="bg-violet-600 border-b-4 border-black p-6">
          <div class="w-16 h-16 bg-white border-4 border-black shadow-[4px_4px_0px_rgba(0,0,0,0.3)] flex items-center justify-center mb-4 mx-auto">
            <UserPlus class="h-8 w-8 text-violet-600 stroke-[2.5]" />
          </div>
          <h1 class="text-2xl font-black text-white text-center drop-shadow-[2px_2px_0px_rgba(0,0,0,0.4)]">
            Buat Akun Baru
          </h1>
          <p class="text-sm text-white/90 text-center mt-2 font-medium">
            Daftarkan diri Anda untuk mengakses layanan peminjaman kampus
          </p>
        </div>

        <div class="p-6">
          <form @submit.prevent="submit" class="space-y-5">
            <!-- Name -->
            <div class="space-y-2">
              <label for="name" class="text-sm font-black text-black uppercase tracking-wider">Nama Lengkap</label>
              <input
                id="name"
                v-model="form.name"
                type="text"
                required
                autofocus
                autocomplete="name"
                placeholder="Masukkan nama lengkap"
                class="w-full px-4 py-3 border-4 border-black text-sm font-medium bg-white placeholder:text-gray-400 outline-none focus:ring-4 focus:ring-violet-300 focus:border-violet-500 transition-all"
              />
              <p v-if="form.errors.name" class="text-xs text-red-600 font-bold">{{ form.errors.name }}</p>
            </div>

            <!-- Email -->
            <div class="space-y-2">
              <label for="email" class="text-sm font-black text-black uppercase tracking-wider">Email</label>
              <input
                id="email"
                v-model="form.email"
                type="email"
                required
                autocomplete="username"
                placeholder="nama@stitek.ac.id"
                class="w-full px-4 py-3 border-4 border-black text-sm font-medium bg-white placeholder:text-gray-400 outline-none focus:ring-4 focus:ring-violet-300 focus:border-violet-500 transition-all"
              />
              <p v-if="form.errors.email" class="text-xs text-red-600 font-bold">{{ form.errors.email }}</p>
            </div>

            <!-- Password -->
            <div class="space-y-2">
              <label for="password" class="text-sm font-black text-black uppercase tracking-wider">Password</label>
              <div class="relative">
                <input
                  id="password"
                  v-model="form.password"
                  :type="showPassword ? 'text' : 'password'"
                  required
                  autocomplete="new-password"
                  placeholder="Buat password baru"
                  class="w-full px-4 py-3 pr-12 border-4 border-black text-sm font-medium bg-white placeholder:text-gray-400 outline-none focus:ring-4 focus:ring-violet-300 focus:border-violet-500 transition-all"
                />
                <button type="button" @click="showPassword = !showPassword" class="absolute right-4 top-1/2 -translate-y-1/2 text-gray-500 hover:text-black transition-colors">
                  <EyeOff v-if="showPassword" class="h-5 w-5" />
                  <Eye v-else class="h-5 w-5" />
                </button>
              </div>
              <p v-if="form.errors.password" class="text-xs text-red-600 font-bold">{{ form.errors.password }}</p>
            </div>

            <!-- Confirm Password -->
            <div class="space-y-2">
              <label for="password_confirmation" class="text-sm font-black text-black uppercase tracking-wider">Konfirmasi Password</label>
              <div class="relative">
                <input
                  id="password_confirmation"
                  v-model="form.password_confirmation"
                  :type="showConfirmPassword ? 'text' : 'password'"
                  required
                  autocomplete="new-password"
                  placeholder="Ulangi password"
                  class="w-full px-4 py-3 pr-12 border-4 border-black text-sm font-medium bg-white placeholder:text-gray-400 outline-none focus:ring-4 focus:ring-violet-300 focus:border-violet-500 transition-all"
                />
                <button type="button" @click="showConfirmPassword = !showConfirmPassword" class="absolute right-4 top-1/2 -translate-y-1/2 text-gray-500 hover:text-black transition-colors">
                  <EyeOff v-if="showConfirmPassword" class="h-5 w-5" />
                  <Eye v-else class="h-5 w-5" />
                </button>
              </div>
              <p v-if="form.errors.password_confirmation" class="text-xs text-red-600 font-bold">{{ form.errors.password_confirmation }}</p>
            </div>

            <!-- Submit -->
            <button
              type="submit"
              :disabled="form.processing"
              class="w-full py-3.5 bg-violet-600 text-white font-black text-sm uppercase tracking-wider border-4 border-black shadow-[4px_4px_0px_rgba(0,0,0,1)] transition-all duration-150 hover:translate-x-[2px] hover:translate-y-[2px] hover:shadow-[2px_2px_0px_rgba(0,0,0,1)] active:translate-x-[4px] active:translate-y-[4px] active:shadow-none disabled:opacity-50"
            >
              {{ form.processing ? 'Mendaftarkan...' : 'Daftar Sekarang' }}
            </button>
          </form>

          <!-- Footer -->
          <div class="mt-5 text-center border-t-4 border-black pt-5">
            <span class="text-sm text-gray-600 font-medium">Sudah punya akun?</span>
            <Link href="/login" class="text-sm font-black text-violet-600 hover:text-violet-800 ml-1 border-b-2 border-violet-600">
              Masuk di sini
            </Link>
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
