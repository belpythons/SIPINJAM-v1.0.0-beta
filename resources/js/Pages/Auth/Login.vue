<script setup>
import { Head, useForm, usePage, Link } from '@inertiajs/vue3';
import { ref, computed } from 'vue';
import { Eye, EyeOff, MessageCircle } from '@lucide/vue';

const page = usePage();
const showPassword = ref(false);
const selectedRole = ref('user');
const adminWaNumber = import.meta.env.VITE_ADMIN_WA_NUMBER || '628123456789';

const form = useForm({
  email: 'user@sipinjam.test',
  password: 'password',
  remember: false,
  role: 'user',
});

const setRole = (role) => {
  selectedRole.value = role;
  form.role = role;
  if (role === 'admin') {
    form.email = 'admin@sipinjam.test';
    form.password = 'password';
  } else {
    form.email = 'user@sipinjam.test';
    form.password = 'password';
  }
};

const isAdmin = computed(() => selectedRole.value === 'admin');

const submit = () => {
  form.post('/login', { preserveScroll: true });
};
</script>

<template>
  <Head title="Masuk ke SiPinjam" />

  <div class="flex min-h-screen font-sans antialiased bg-background">
    <!-- ── Left Panel ─────────────────────────────── -->
    <div
      :class="[
        'hidden lg:flex lg:w-[55%] min-h-screen flex-col justify-between p-12 relative overflow-hidden transition-all duration-500 border-r border-border',
        isAdmin ? 'from-orange-500 to-amber-600 bg-gradient-to-br' : 'from-blue-600 to-indigo-800 bg-gradient-to-br',
      ]"
    >
      <!-- Grid Pattern -->
      <div
        class="absolute inset-0 pointer-events-none"
        style="
          background-image: linear-gradient(rgba(255,255,255,0.04) 2px, transparent 2px),
            linear-gradient(90deg, rgba(255,255,255,0.04) 2px, transparent 2px);
          background-size: 48px 48px;
        "
      />

      <!-- Floating Shapes (Glassmorphic) -->
      <div class="absolute -top-10 -right-16 w-72 h-72 rounded-full bg-white/10 backdrop-blur-md border border-white/20 rotate-12 transition-all duration-500" />
      <div class="absolute bottom-20 -left-12 w-48 h-48 rounded-full bg-white/10 backdrop-blur-md border border-white/20 -rotate-12 transition-all duration-500" />
      <div class="absolute top-1/2 right-20 w-24 h-24 rounded-full bg-white/10 backdrop-blur-md border border-white/20 transition-all duration-500" />

      <!-- Logo -->
      <div class="relative z-10 flex items-center gap-4">
        <div class="w-14 h-14 bg-white/10 backdrop-blur-md border border-white/20 rounded-xl shadow-sm flex items-center justify-center overflow-hidden">
          <img src="/image/logo-sp.png" alt="Logo SP" class="w-10 h-10 object-contain" />
        </div>
        <div>
          <div class="text-white font-bold text-2xl tracking-wide">SIPINJAM</div>
          <div class="text-white/80 text-xs font-medium tracking-wider uppercase">Sistem Informasi Peminjaman</div>
        </div>
      </div>

      <!-- Hero Content -->
      <div class="relative z-10 space-y-6">
        <div class="inline-block bg-white/20 text-white border border-white/30 backdrop-blur-sm px-3.5 py-1 text-xs font-semibold tracking-wider uppercase rounded-full">
          Platform Peminjaman
        </div>
        <h1 class="text-5xl font-bold text-white leading-tight tracking-tight">
          Kelola<br />Peminjaman<br />dengan Mudah
        </h1>
        <p class="text-white/90 text-base font-medium max-w-md leading-relaxed">
          Platform terpadu untuk peminjaman ruangan dan barang kampus. Proses cepat, transparan, dan efisien.
        </p>

        <!-- Feature List (Glass Card) -->
        <div class="bg-white/10 backdrop-blur-md border border-white/20 p-6 max-w-md rounded-2xl shadow-lg">
          <div class="space-y-4">
            <div v-for="item in ['Booking Online & Real-time 24/7', 'Tracking Status Persetujuan Otomatis', 'Riwayat Peminjaman Terintegrasi']" :key="item" class="flex items-center gap-4">
              <div class="w-8 h-8 rounded-lg bg-white/20 border border-white/30 flex items-center justify-center shrink-0 shadow-sm">
                <svg width="14" height="14" viewBox="0 0 14 14" fill="none">
                  <path d="M2.5 7L5.5 10L11.5 4" stroke="white" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
              </div>
              <span class="text-white font-semibold text-sm">{{ item }}</span>
            </div>
          </div>
        </div>
      </div>

      <!-- Footer -->
      <div class="relative z-10 text-white/60 text-xs font-medium">
        © 2026 SIPINJAM. Sekolah Tinggi Teknologi Bontang.
      </div>
    </div>

    <!-- ── Right Panel — Form ─────────────────────── -->
    <div class="flex flex-1 items-center justify-center p-6 sm:p-12 bg-background">
      <div class="w-full max-w-md space-y-7">
        <!-- Mobile Brand -->
        <div class="lg:hidden text-center space-y-1 mb-6">
          <div class="inline-block mx-auto w-14 h-14 bg-card border border-border rounded-xl shadow-sm flex items-center justify-center overflow-hidden mb-3">
            <img src="/image/logo-sp.png" alt="Logo SP" class="w-10 h-10 object-contain" />
          </div>
          <h2 class="text-3xl font-bold tracking-tight text-foreground">SiPinjam</h2>
          <p class="text-xs tracking-widest uppercase text-muted-foreground font-semibold">STITEK Bontang</p>
        </div>

        <!-- Form Header -->
        <div class="text-center">
          <h2 class="text-2xl font-bold text-foreground tracking-tight">Welcome to SIPINJAM</h2>
          <p class="text-sm text-muted-foreground mt-1">Masukkan email dan password Anda untuk melanjutkan</p>
        </div>

        <!-- Role Toggle -->
        <div class="flex bg-muted/60 border border-border p-1 gap-1 rounded-xl shadow-sm">
          <button
            type="button"
            @click="setRole('user')"
            :class="[
              'flex-1 py-2.5 rounded-lg text-sm font-semibold transition-all duration-200',
              selectedRole === 'user'
                ? 'bg-primary text-primary-foreground shadow-sm'
                : 'text-muted-foreground hover:text-foreground',
            ]"
          >
            User
          </button>
          <button
            type="button"
            @click="setRole('admin')"
            :class="[
              'flex-1 py-2.5 rounded-lg text-sm font-semibold transition-all duration-200',
              selectedRole === 'admin'
                ? 'bg-orange-600 text-white shadow-sm'
                : 'text-muted-foreground hover:text-foreground',
            ]"
          >
            Admin
          </button>
        </div>

        <!-- Flash Error -->
        <div
          v-if="page.props.flash?.error"
          class="bg-destructive/10 border border-destructive/20 rounded-lg p-4 text-sm text-destructive font-medium shadow-sm"
        >
          {{ page.props.flash.error }}
        </div>

        <!-- Login Form -->
        <form @submit.prevent="submit" class="space-y-5">
          <!-- Email -->
          <div class="space-y-2">
            <label for="email" class="text-xs font-semibold text-muted-foreground uppercase tracking-wider">Email</label>
            <input
              id="email"
              v-model="form.email"
              type="email"
              required
              autofocus
              autocomplete="email"
              :placeholder="isAdmin ? 'admin@sipinjam.test' : 'user@sipinjam.test'"
              class="w-full px-4 py-2.5 rounded-lg border border-border text-sm font-medium bg-background text-foreground placeholder:text-muted-foreground outline-none transition-all focus:ring-2 focus:ring-ring focus:ring-offset-2"
            />
            <p v-if="form.errors.email" class="text-xs text-destructive font-medium">{{ form.errors.email }}</p>
          </div>

          <!-- Password -->
          <div class="space-y-2">
            <label for="password" class="text-xs font-semibold text-muted-foreground uppercase tracking-wider">Password</label>
            <div class="relative">
              <input
                id="password"
                v-model="form.password"
                :type="showPassword ? 'text' : 'password'"
                required
                placeholder="Masukkan password"
                class="w-full px-4 py-2.5 pr-12 rounded-lg border border-border text-sm font-medium bg-background text-foreground placeholder:text-muted-foreground outline-none transition-all focus:ring-2 focus:ring-ring focus:ring-offset-2"
              />
              <button
                type="button"
                @click="showPassword = !showPassword"
                class="absolute right-4 top-1/2 -translate-y-1/2 text-muted-foreground hover:text-foreground transition-colors"
              >
                <EyeOff v-if="showPassword" class="h-5 w-5" />
                <Eye v-else class="h-5 w-5" />
              </button>
            </div>
            <p v-if="form.errors.password" class="text-xs text-destructive font-medium">{{ form.errors.password }}</p>
          </div>

          <!-- Remember / Forgot -->
          <div class="flex items-center justify-between">
            <label class="flex items-center gap-2 cursor-pointer">
              <input
                id="remember_me"
                v-model="form.remember"
                type="checkbox"
                class="w-4 h-4 rounded border-border text-primary focus:ring-ring"
              />
              <span class="text-sm text-muted-foreground font-medium">Ingat Saya</span>
            </label>
            <Link
              href="/forgot-password"
              class="text-sm font-semibold text-primary hover:text-primary/80 transition-colors"
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
              'w-full py-2.5 px-4 rounded-lg font-semibold text-sm shadow-sm transition-all focus:ring-2 focus:ring-ring focus:ring-offset-2 disabled:opacity-50 disabled:cursor-not-allowed',
              form.role === 'admin'
                ? 'bg-orange-600 hover:bg-orange-700 text-white'
                : 'bg-primary hover:bg-primary/90 text-primary-foreground'
            ]"
          >
            {{ form.processing ? 'Memproses...' : 'Masuk' }}
          </button>
        </form>

        <!-- Divider -->
        <div class="flex items-center gap-3">
          <span class="flex-1 h-px bg-border" />
          <span class="text-[10px] font-semibold text-muted-foreground uppercase tracking-widest">Atau lanjutkan dengan</span>
          <span class="flex-1 h-px bg-border" />
        </div>

        <!-- Google Login -->
        <a
          href="/auth/google"
          id="btn-google-login"
          class="flex items-center justify-center gap-3 w-full border border-border bg-card text-foreground font-semibold text-sm rounded-lg px-4 py-2.5 shadow-sm hover:bg-muted transition-all duration-200"
        >
          <svg class="w-5 h-5" viewBox="0 0 18 18" fill="none">
            <path d="M17.64 9.2c0-.637-.057-1.251-.164-1.84H9v3.481h4.844c-.209 1.125-.843 2.078-1.796 2.717v2.258h2.908c1.702-1.567 2.684-3.875 2.684-6.615z" fill="#4285F4"/>
            <path d="M9 18c2.43 0 4.467-.806 5.956-2.18l-2.908-2.259c-.806.54-1.837.86-3.048.86-2.344 0-4.328-1.584-5.036-3.711H.957v2.332A8.997 8.997 0 0 0 9 18z" fill="#34A853"/>
            <path d="M3.964 10.71A5.41 5.41 0 0 1 3.682 9c0-.593.102-1.71.282-1.71V4.958H.957A8.996 8.996 0 0 0 0 9c0 1.452.348 2.827.957 4.042l3.007-2.332z" fill="#FBBC05"/>
            <path d="M9 3.58c1.321 0 2.508.454 3.44 1.345l2.582-2.58C13.463.891 11.426 0 9 0A8.997 8.997 0 0 0 .957 4.958L3.964 7.29C4.672 5.163 6.656 3.58 9 3.58z" fill="#EA4335"/>
          </svg>
          Masuk dengan Google
        </a>

        <!-- WhatsApp Contact -->
        <div class="pt-2 text-center">
          <p class="text-xs text-muted-foreground mb-2 font-medium">Ada masalah dengan akun Anda?</p>
          <a
            :href="`https://wa.me/${adminWaNumber}?text=Halo%20Admin%2C%20saya%20butuh%20bantuan%20dengan%20akun%20SiPinjam%20saya.`"
            target="_blank"
            class="inline-flex items-center gap-2 bg-emerald-600 text-white font-semibold text-xs rounded-lg px-5 py-2.5 shadow-sm hover:bg-emerald-700 transition-all duration-200"
          >
            <MessageCircle class="h-4 w-4" />
            Hubungi Admin via WhatsApp
          </a>
        </div>
      </div>
    </div>
  </div>
</template>
