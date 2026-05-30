<script setup>
import { Head, useForm, usePage, Link } from '@inertiajs/vue3';
import { ref, computed } from 'vue';
import { Eye, EyeOff, MessageCircle } from '@lucide/vue';

const page = usePage();
const showPassword = ref(false);
const selectedRole = ref('user');
const adminWaNumber = import.meta.env.VITE_ADMIN_WA_NUMBER || '628123456789';

const form = useForm({
  email: '',
  password: '',
  remember: false,
  role: 'user',
});

const setRole = (role) => {
  selectedRole.value = role;
  form.role = role;
};

const isAdmin = computed(() => selectedRole.value === 'admin');

const submit = () => {
  form.post('/login', { preserveScroll: true });
};
</script>

<template>
  <Head title="Masuk ke SiPinjam" />

  <div class="flex min-h-screen font-sans antialiased">
    <!-- ── Left Panel ─────────────────────────────── -->
    <div
      :class="[
        'hidden lg:flex lg:w-[55%] min-h-screen flex-col justify-between p-12 relative overflow-hidden transition-colors duration-500 border-r-4 border-black',
        isAdmin ? 'bg-orange-500' : 'bg-blue-600',
      ]"
    >
      <!-- Grid Pattern -->
      <div
        class="absolute inset-0 pointer-events-none"
        style="
          background-image: linear-gradient(rgba(0,0,0,0.08) 2px, transparent 2px),
            linear-gradient(90deg, rgba(0,0,0,0.08) 2px, transparent 2px);
          background-size: 48px 48px;
        "
      />

      <!-- Floating Shapes (Neo-Brutalist: solid colors, thick borders) -->
      <div
        :class="[
          'absolute -top-10 -right-16 w-72 h-72 border-4 border-black rotate-12 transition-colors duration-500',
          isAdmin ? 'bg-yellow-400' : 'bg-cyan-400',
        ]"
      />
      <div
        :class="[
          'absolute bottom-20 -left-12 w-48 h-48 border-4 border-black -rotate-12 transition-colors duration-500',
          isAdmin ? 'bg-red-400' : 'bg-indigo-400',
        ]"
      />
      <div
        :class="[
          'absolute top-1/2 right-20 w-24 h-24 border-4 border-black transition-colors duration-500',
          isAdmin ? 'bg-amber-300' : 'bg-sky-300',
        ]"
      />

      <!-- Logo -->
      <div class="relative z-10 flex items-center gap-4">
        <div class="w-14 h-14 bg-white border-4 border-black shadow-[4px_4px_0px_rgba(0,0,0,1)] flex items-center justify-center overflow-hidden">
          <img src="/image/logo-sp.png" alt="Logo SP" class="w-10 h-10 object-contain" />
        </div>
        <div>
          <div class="text-white font-black text-2xl tracking-wide drop-shadow-[2px_2px_0px_rgba(0,0,0,0.5)]">SIPINJAM</div>
          <div class="text-white/80 text-xs font-bold tracking-wider uppercase">Sistem Informasi Peminjaman</div>
        </div>
      </div>

      <!-- Hero Content -->
      <div class="relative z-10 space-y-6">
        <div class="inline-block bg-black text-white px-4 py-1.5 text-xs font-black uppercase tracking-widest">
          Platform Peminjaman
        </div>
        <h1 class="text-5xl font-black text-white leading-tight drop-shadow-[3px_3px_0px_rgba(0,0,0,0.4)]">
          Kelola<br />Peminjaman<br />dengan Mudah
        </h1>
        <p class="text-white/90 text-base font-medium max-w-md leading-relaxed">
          Platform terpadu untuk peminjaman ruangan dan barang kampus. Proses cepat, transparan, dan efisien.
        </p>

        <!-- Feature List (Neo-Brutalist Glass Card) -->
        <div class="bg-white/10 border-4 border-black p-6 max-w-md shadow-[6px_6px_0px_rgba(0,0,0,1)]">
          <div class="space-y-4">
            <div v-for="item in ['Booking Online & Real-time 24/7', 'Tracking Status Persetujuan Otomatis', 'Riwayat Peminjaman Terintegrasi']" :key="item" class="flex items-center gap-4">
              <div class="w-8 h-8 bg-white border-2 border-black flex items-center justify-center shrink-0 shadow-[2px_2px_0px_rgba(0,0,0,1)]">
                <svg width="14" height="14" viewBox="0 0 14 14" fill="none">
                  <path d="M2.5 7L5.5 10L11.5 4" stroke="black" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
              </div>
              <span class="text-white font-bold text-sm">{{ item }}</span>
            </div>
          </div>
        </div>
      </div>

      <!-- Footer -->
      <div class="relative z-10 text-white/60 text-xs font-bold">
        © 2026 SIPINJAM. Sekolah Tinggi Teknologi Bontang.
      </div>
    </div>

    <!-- ── Right Panel — Form ─────────────────────── -->
    <div class="flex flex-1 items-center justify-center p-6 sm:p-12 bg-white">
      <div class="w-full max-w-md space-y-7">
        <!-- Mobile Brand -->
        <div class="lg:hidden text-center space-y-1 mb-6">
          <div class="inline-block mx-auto w-14 h-14 bg-white border-4 border-black shadow-[4px_4px_0px_rgba(0,0,0,1)] flex items-center justify-center overflow-hidden mb-3">
            <img src="/image/logo-sp.png" alt="Logo SP" class="w-10 h-10 object-contain" />
          </div>
          <h2 class="text-3xl font-black tracking-tight text-black">SiPinjam</h2>
          <p class="text-xs tracking-widest uppercase text-gray-500 font-bold">STITEK Bontang</p>
        </div>

        <!-- Form Header -->
        <div class="text-center">
          <h2 class="text-2xl font-black text-black tracking-tight">Welcome to SIPINJAM</h2>
          <p class="text-sm text-gray-500 mt-1">Masukkan email dan password Anda untuk melanjutkan</p>
        </div>

        <!-- Role Toggle -->
        <div class="flex bg-gray-100 border-4 border-black p-1 gap-1 shadow-[4px_4px_0px_rgba(0,0,0,1)]">
          <button
            type="button"
            @click="setRole('user')"
            :class="[
              'flex-1 py-3 text-sm font-black transition-all duration-200 border-2',
              selectedRole === 'user'
                ? 'bg-blue-600 text-white border-black shadow-[2px_2px_0px_rgba(0,0,0,1)]'
                : 'bg-transparent text-gray-500 border-transparent hover:text-gray-700',
            ]"
          >
            User
          </button>
          <button
            type="button"
            @click="setRole('admin')"
            :class="[
              'flex-1 py-3 text-sm font-black transition-all duration-200 border-2',
              selectedRole === 'admin'
                ? 'bg-orange-500 text-white border-black shadow-[2px_2px_0px_rgba(0,0,0,1)]'
                : 'bg-transparent text-gray-500 border-transparent hover:text-gray-700',
            ]"
          >
            Admin
          </button>
        </div>

        <!-- Flash Error -->
        <div
          v-if="page.props.flash?.error"
          class="bg-red-100 border-4 border-black p-4 text-sm text-red-800 font-bold shadow-[4px_4px_0px_rgba(0,0,0,1)]"
        >
          {{ page.props.flash.error }}
        </div>

        <!-- Login Form -->
        <form @submit.prevent="submit" class="space-y-5">
          <!-- Email -->
          <div class="space-y-2">
            <label for="email" class="text-sm font-black text-black uppercase tracking-wider">Email</label>
            <input
              id="email"
              v-model="form.email"
              type="email"
              required
              autofocus
              autocomplete="email"
              :placeholder="isAdmin ? 'admin@sipinjam.test' : 'user@sipinjam.test'"
              :class="[
                'w-full px-4 py-3 border-4 border-black text-sm font-medium bg-white placeholder:text-gray-400 outline-none transition-all',
                isAdmin
                  ? 'focus:ring-4 focus:ring-orange-300 focus:border-orange-500'
                  : 'focus:ring-4 focus:ring-blue-300 focus:border-blue-500',
              ]"
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
                placeholder="Masukkan password"
                :class="[
                  'w-full px-4 py-3 pr-12 border-4 border-black text-sm font-medium bg-white placeholder:text-gray-400 outline-none transition-all',
                  isAdmin
                    ? 'focus:ring-4 focus:ring-orange-300 focus:border-orange-500'
                    : 'focus:ring-4 focus:ring-blue-300 focus:border-blue-500',
                ]"
              />
              <button
                type="button"
                @click="showPassword = !showPassword"
                class="absolute right-4 top-1/2 -translate-y-1/2 text-gray-500 hover:text-black transition-colors"
              >
                <EyeOff v-if="showPassword" class="h-5 w-5" />
                <Eye v-else class="h-5 w-5" />
              </button>
            </div>
            <p v-if="form.errors.password" class="text-xs text-red-600 font-bold">{{ form.errors.password }}</p>
          </div>

          <!-- Remember / Forgot -->
          <div class="flex items-center justify-between">
            <label class="flex items-center gap-2 cursor-pointer">
              <input
                id="remember_me"
                v-model="form.remember"
                type="checkbox"
                class="w-5 h-5 border-2 border-black accent-blue-600 cursor-pointer"
              />
              <span class="text-sm text-gray-600 font-semibold">Ingat Saya</span>
            </label>
            <Link
              href="/forgot-password"
              :class="[
                'text-sm font-bold transition-colors',
                isAdmin ? 'text-orange-600 hover:text-orange-800' : 'text-blue-600 hover:text-blue-800',
              ]"
            >
              Lupa Password?
            </Link>
          </div>

          <!-- Submit -->
          <button
            type="submit"
            :disabled="form.processing"
            id="btn-login-submit"
            :class="[
              'w-full py-3.5 text-white font-black text-sm uppercase tracking-wider border-4 border-black shadow-[4px_4px_0px_rgba(0,0,0,1)] transition-all duration-150 hover:translate-x-[2px] hover:translate-y-[2px] hover:shadow-[2px_2px_0px_rgba(0,0,0,1)] active:translate-x-[4px] active:translate-y-[4px] active:shadow-none disabled:opacity-50',
              isAdmin ? 'bg-orange-500 hover:bg-orange-600' : 'bg-blue-600 hover:bg-blue-700',
            ]"
          >
            {{ form.processing ? 'Memproses...' : 'Masuk' }}
          </button>
        </form>

        <!-- Divider -->
        <div class="flex items-center gap-3">
          <span class="flex-1 h-1 bg-black" />
          <span class="text-[10px] font-black text-gray-500 uppercase tracking-widest">Atau lanjutkan dengan</span>
          <span class="flex-1 h-1 bg-black" />
        </div>

        <!-- Google Login -->
        <a
          href="/auth/google"
          id="btn-google-login"
          class="flex items-center justify-center gap-3 w-full border-4 border-black bg-white text-black font-bold text-sm px-4 py-3 shadow-[4px_4px_0px_rgba(0,0,0,1)] transition-all duration-150 hover:translate-x-[2px] hover:translate-y-[2px] hover:shadow-[2px_2px_0px_rgba(0,0,0,1)] active:translate-x-[4px] active:translate-y-[4px] active:shadow-none"
        >
          <svg class="w-5 h-5" viewBox="0 0 18 18" fill="none">
            <path d="M17.64 9.2c0-.637-.057-1.251-.164-1.84H9v3.481h4.844c-.209 1.125-.843 2.078-1.796 2.717v2.258h2.908c1.702-1.567 2.684-3.875 2.684-6.615z" fill="#4285F4"/>
            <path d="M9 18c2.43 0 4.467-.806 5.956-2.18l-2.908-2.259c-.806.54-1.837.86-3.048.86-2.344 0-4.328-1.584-5.036-3.711H.957v2.332A8.997 8.997 0 0 0 9 18z" fill="#34A853"/>
            <path d="M3.964 10.71A5.41 5.41 0 0 1 3.682 9c0-.593.102-1.17.282-1.71V4.958H.957A8.996 8.996 0 0 0 0 9c0 1.452.348 2.827.957 4.042l3.007-2.332z" fill="#FBBC05"/>
            <path d="M9 3.58c1.321 0 2.508.454 3.44 1.345l2.582-2.58C13.463.891 11.426 0 9 0A8.997 8.997 0 0 0 .957 4.958L3.964 7.29C4.672 5.163 6.656 3.58 9 3.58z" fill="#EA4335"/>
          </svg>
          Masuk dengan Google
        </a>

        <!-- WhatsApp Contact -->
        <div class="pt-2 text-center">
          <p class="text-xs text-gray-500 mb-2 font-semibold">Ada masalah dengan akun Anda?</p>
          <a
            :href="`https://wa.me/${adminWaNumber}?text=Halo%20Admin%2C%20saya%20butuh%20bantuan%20dengan%20akun%20SiPinjam%20saya.`"
            target="_blank"
            class="inline-flex items-center gap-2 bg-green-500 text-white font-black text-xs uppercase tracking-wider px-5 py-2.5 border-4 border-black shadow-[4px_4px_0px_rgba(0,0,0,1)] transition-all duration-150 hover:translate-x-[2px] hover:translate-y-[2px] hover:shadow-[2px_2px_0px_rgba(0,0,0,1)] active:translate-x-[4px] active:translate-y-[4px] active:shadow-none"
          >
            <MessageCircle class="h-4 w-4" />
            Hubungi Admin via WhatsApp
          </a>
        </div>
      </div>
    </div>
  </div>
</template>
