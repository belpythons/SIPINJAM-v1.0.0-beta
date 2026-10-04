<script setup>
import { Head, useForm, usePage, Link } from '@inertiajs/vue3';
import { ref, computed } from 'vue';
import { Eye, EyeOff, MessageCircle, ShieldCheck, Sparkles, CheckCircle2 } from '@lucide/vue';

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

  <div class="flex min-h-screen font-sans antialiased bg-background overflow-hidden">
    <!-- ── Left Panel (With Continuous Idle Floating & Spring Motion Physics) ── -->
    <div
      :class="[
        'hidden lg:flex lg:w-[55%] min-h-screen flex-col justify-between p-12 relative overflow-hidden transition-all duration-700 border-r border-border',
        isAdmin ? 'from-orange-600 via-amber-600 to-amber-800 bg-gradient-to-br' : 'from-blue-600 via-indigo-700 to-slate-900 bg-gradient-to-br',
      ]"
    >
      <!-- Background Grid Overlay -->
      <div
        class="absolute inset-0 pointer-events-none opacity-20"
        style="
          background-image: linear-gradient(rgba(255,255,255,0.06) 2px, transparent 2px),
            linear-gradient(90deg, rgba(255,255,255,0.06) 2px, transparent 2px);
          background-size: 48px 48px;
        "
      />

      <!-- Floating Animated Glassmorphic Spheres (Idle Movement) -->
      <div class="absolute -top-12 -right-16 w-80 h-80 rounded-full bg-white/10 backdrop-blur-md border border-white/20 animate-float-sphere-1 pointer-events-none shadow-xl" />
      <div class="absolute bottom-16 -left-16 w-60 h-60 rounded-full bg-white/10 backdrop-blur-md border border-white/20 animate-float-sphere-2 pointer-events-none shadow-xl" />
      <div class="absolute top-1/2 right-12 w-28 h-28 rounded-full bg-white/15 backdrop-blur-md border border-white/25 animate-float-sphere-3 pointer-events-none shadow-md" />

      <!-- Top Branding Logo -->
      <div class="relative z-10 flex items-center gap-4 animate-fade-in">
        <div class="w-14 h-14 bg-white/15 backdrop-blur-md border border-white/25 rounded-2xl shadow-md flex items-center justify-center overflow-hidden transition-transform duration-300 hover:scale-105">
          <img src="/image/logo-sp.png" alt="Logo SP" class="w-10 h-10 object-contain drop-shadow-sm" />
        </div>
        <div>
          <div class="text-white font-extrabold text-2xl tracking-wide flex items-center gap-1.5">
            SIPINJAM
            <span class="text-[10px] font-bold px-2 py-0.5 rounded-full bg-white/20 border border-white/30 text-white uppercase tracking-wider">
              {{ isAdmin ? 'Admin Portal' : 'Sivitas' }}
            </span>
          </div>
          <div class="text-white/80 text-xs font-medium tracking-wider uppercase">Sistem Informasi Peminjaman Aset</div>
        </div>
      </div>

      <!-- Hero Content & Dynamic Role Showcase -->
      <div class="relative z-10 space-y-6 max-w-lg">
        <div class="inline-flex items-center gap-2 bg-white/20 text-white border border-white/30 backdrop-blur-md px-4 py-1.5 text-xs font-semibold tracking-wider uppercase rounded-full shadow-xs">
          <Sparkles class="h-3.5 w-3.5 text-white animate-spin-slow" />
          {{ isAdmin ? 'Panel Kontrol Manajemen Kampus' : 'Platform Peminjaman Terpadu' }}
        </div>

        <h1 class="text-5xl font-black text-white leading-tight tracking-tight drop-shadow-sm transition-all duration-300">
          <template v-if="isAdmin">
            Kelola & Pantau<br />Aset Kampus<br />Secara Real-Time
          </template>
          <template v-else>
            Kelola<br />Peminjaman<br />dengan Mudah
          </template>
        </h1>

        <p class="text-white/90 text-base font-normal leading-relaxed">
          <template v-if="isAdmin">
            Pusat kendali persetujuan peminjaman ruangan, stok inventaris barang, penegakan sanksi tata tertib, dan pelaporan statistik kampus.
          </template>
          <template v-else>
            Temukan dan pinjam ruangan kelas, laboratorium, atau perlengkapan praktikum kampus dalam satu platform terintegrasi.
          </template>
        </p>

        <!-- Feature List (Animated Glass Card) -->
        <div class="bg-white/10 backdrop-blur-md border border-white/20 p-6 rounded-2xl shadow-xl space-y-3.5 animate-card-breath">
          <div v-for="(feat, idx) in (isAdmin ? [
            'Validasi & Approval Peminjaman Cepat',
            'Manajemen Inventaris Ruangan & Barang',
            'Penegakan Disiplin & Sanksi Sivitas'
          ] : [
            'Booking Online & Real-time 24/7',
            'Surat Permohonan Self-Service Berkop',
            'Riwayat & Status Peminjaman Transparan'
          ])" :key="idx" class="flex items-center gap-3.5">
            <div class="w-7 h-7 rounded-lg bg-white/20 border border-white/30 flex items-center justify-center shrink-0 shadow-xs">
              <CheckCircle2 class="w-4 h-4 text-white" />
            </div>
            <span class="text-white font-medium text-sm">{{ feat }}</span>
          </div>
        </div>
      </div>

      <!-- Footer -->
      <div class="relative z-10 text-white/70 text-xs font-medium flex items-center justify-between border-t border-white/10 pt-4">
        <span>© 2026 SIPINJAM. STITEK Bontang.</span>
        <span class="flex items-center gap-1 font-mono text-[10px]">
          <ShieldCheck class="w-3.5 h-3.5 text-emerald-300" /> Secure SSO & RBAC Guard
        </span>
      </div>
    </div>

    <!-- ── Right Panel — Form ─────────────────────── -->
    <div class="flex flex-1 items-center justify-center p-6 sm:p-12 bg-background overflow-y-auto">
      <div class="w-full max-w-md space-y-7">
        <!-- Mobile Brand -->
        <div class="lg:hidden text-center space-y-1 mb-6">
          <div class="inline-block mx-auto w-14 h-14 bg-card border border-border rounded-xl shadow-xs flex items-center justify-center overflow-hidden mb-3">
            <img src="/image/logo-sp.png" alt="Logo SP" class="w-10 h-10 object-contain" />
          </div>
          <h2 class="text-3xl font-bold tracking-tight text-foreground">SiPinjam</h2>
          <p class="text-xs tracking-widest uppercase text-muted-foreground font-semibold">STITEK Bontang</p>
        </div>

        <!-- Form Header -->
        <div class="text-center">
          <h2 class="text-2xl font-bold text-foreground tracking-tight">Selamat Datang di SIPINJAM</h2>
          <p class="text-xs sm:text-sm text-muted-foreground mt-1">Masukkan kredensial akun kampus Anda untuk melanjutkan</p>
        </div>

        <!-- Role Toggle with Active Slider Animation -->
        <div class="flex bg-muted/70 border border-border p-1 gap-1 rounded-xl shadow-xs">
          <button
            type="button"
            @click="setRole('user')"
            :class="[
              'flex-1 py-2.5 rounded-lg text-xs sm:text-sm font-semibold transition-all duration-300 cursor-pointer',
              selectedRole === 'user'
                ? 'bg-primary text-primary-foreground shadow-xs scale-[1.01]'
                : 'text-muted-foreground hover:text-foreground',
            ]"
          >
            Sivitas / Mahasiswa
          </button>
          <button
            type="button"
            @click="setRole('admin')"
            :class="[
              'flex-1 py-2.5 rounded-lg text-xs sm:text-sm font-semibold transition-all duration-300 cursor-pointer',
              selectedRole === 'admin'
                ? 'bg-orange-600 text-white shadow-xs scale-[1.01]'
                : 'text-muted-foreground hover:text-foreground',
            ]"
          >
            Administrator
          </button>
        </div>

        <!-- Flash Error Alert -->
        <div
          v-if="page.props.flash?.error"
          class="bg-destructive/10 border border-destructive/20 rounded-lg p-4 text-xs sm:text-sm text-destructive font-medium shadow-xs"
        >
          {{ page.props.flash.error }}
        </div>

        <!-- Login Form -->
        <form @submit.prevent="submit" class="space-y-5">
          <!-- Email -->
          <div class="space-y-1.5">
            <label for="email" class="text-xs font-semibold text-muted-foreground uppercase tracking-wider">Email Kampus</label>
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
          <div class="space-y-1.5">
            <label for="password" class="text-xs font-semibold text-muted-foreground uppercase tracking-wider">Kata Sandi</label>
            <div class="relative">
              <input
                id="password"
                v-model="form.password"
                :type="showPassword ? 'text' : 'password'"
                required
                placeholder="Masukkan kata sandi"
                class="w-full px-4 py-2.5 pr-12 rounded-lg border border-border text-sm font-medium bg-background text-foreground placeholder:text-muted-foreground outline-none transition-all focus:ring-2 focus:ring-ring focus:ring-offset-2"
              />
              <button
                type="button"
                @click="showPassword = !showPassword"
                class="absolute right-4 top-1/2 -translate-y-1/2 text-muted-foreground hover:text-foreground transition-colors cursor-pointer"
              >
                <EyeOff v-if="showPassword" class="h-4 w-4" />
                <Eye v-else class="h-4 w-4" />
              </button>
            </div>
            <p v-if="form.errors.password" class="text-xs text-destructive font-medium">{{ form.errors.password }}</p>
          </div>

          <!-- Remember / Forgot -->
          <div class="flex items-center justify-between text-xs">
            <label class="flex items-center gap-2 cursor-pointer">
              <input
                id="remember_me"
                v-model="form.remember"
                type="checkbox"
                class="w-4 h-4 rounded border-border text-primary focus:ring-ring"
              />
              <span class="text-muted-foreground font-medium">Ingat Saya</span>
            </label>
            <Link
              href="/forgot-password"
              class="font-semibold text-primary hover:text-primary/80 transition-colors"
            >
              Lupa Kata Sandi?
            </Link>
          </div>

          <!-- Submit Button -->
          <button
            type="submit"
            :disabled="form.processing"
            id="btn-login-submit"
            :class="[
              'w-full py-2.5 px-4 rounded-lg font-semibold text-sm shadow-xs transition-all duration-200 cursor-pointer focus:ring-2 focus:ring-ring focus:ring-offset-2 disabled:opacity-50 disabled:cursor-not-allowed',
              form.role === 'admin'
                ? 'bg-orange-600 hover:bg-orange-700 text-white'
                : 'bg-primary hover:bg-primary/90 text-primary-foreground'
            ]"
          >
            {{ form.processing ? 'Memproses Masuk...' : 'Masuk Sekarang' }}
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
          class="flex items-center justify-center gap-3 w-full border border-border bg-card text-foreground font-semibold text-xs sm:text-sm rounded-lg px-4 py-2.5 shadow-xs hover:bg-muted transition-all duration-200 cursor-pointer"
        >
          <svg class="w-4 h-4" viewBox="0 0 18 18" fill="none">
            <path d="M17.64 9.2c0-.637-.057-1.251-.164-1.84H9v3.481h4.844c-.209 1.125-.843 2.078-1.796 2.717v2.258h2.908c1.702-1.567 2.684-3.875 2.684-6.615z" fill="#4285F4"/>
            <path d="M9 18c2.43 0 4.467-.806 5.956-2.18l-2.908-2.259c-.806.54-1.837.86-3.048.86-2.344 0-4.328-1.584-5.036-3.711H.957v2.332A8.997 8.997 0 0 0 9 18z" fill="#34A853"/>
            <path d="M3.964 10.71A5.41 5.41 0 0 1 3.682 9c0-.593.102-1.71.282-1.71V4.958H.957A8.996 8.996 0 0 0 0 9c0 1.452.348 2.827.957 4.042l3.007-2.332z" fill="#FBBC05"/>
            <path d="M9 3.58c1.321 0 2.508.454 3.44 1.345l2.582-2.58C13.463.891 11.426 0 9 0A8.997 8.997 0 0 0 .957 4.958L3.964 7.29C4.672 5.163 6.656 3.58 9 3.58z" fill="#EA4335"/>
          </svg>
          Masuk dengan Google (Akun Kampus)
        </a>

        <!-- WhatsApp Contact -->
        <div class="pt-2 text-center">
          <p class="text-xs text-muted-foreground mb-2 font-medium">Memerlukan bantuan aktivasi atau login?</p>
          <a
            :href="`https://wa.me/${adminWaNumber}?text=Halo%20Admin%2C%20saya%20butuh%20bantuan%20dengan%20akun%20SiPinjam%20saya.`"
            target="_blank"
            class="inline-flex items-center gap-2 bg-emerald-600 text-white font-semibold text-xs rounded-lg px-5 py-2.5 shadow-xs hover:bg-emerald-700 transition-all duration-200 cursor-pointer"
          >
            <MessageCircle class="h-4 w-4" />
            Hubungi Pengelola via WhatsApp
          </a>
        </div>
      </div>
    </div>
  </div>
</template>

<style scoped>
/* ── Framer Motion-style Keyframes & Physics ───────── */
@keyframes floatSphere1 {
  0%, 100% { transform: translateY(0px) rotate(12deg) scale(1); }
  50% { transform: translateY(-20px) rotate(18deg) scale(1.03); }
}

@keyframes floatSphere2 {
  0%, 100% { transform: translateY(0px) rotate(-12deg) scale(1); }
  50% { transform: translateY(18px) rotate(-6deg) scale(0.97); }
}

@keyframes floatSphere3 {
  0%, 100% { transform: translate(0px, 0px) scale(1); }
  50% { transform: translate(-12px, -14px) scale(1.05); }
}

@keyframes cardBreath {
  0%, 100% { transform: translateY(0px); box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.15); }
  50% { transform: translateY(-4px); box-shadow: 0 20px 35px -5px rgba(0, 0, 0, 0.25); }
}

@keyframes fadeIn {
  from { opacity: 0; transform: translateY(-10px); }
  to { opacity: 1; transform: translateY(0); }
}

@keyframes spinSlow {
  from { transform: rotate(0deg); }
  to { transform: rotate(360deg); }
}

.animate-float-sphere-1 {
  animation: floatSphere1 9s ease-in-out infinite;
}

.animate-float-sphere-2 {
  animation: floatSphere2 11s ease-in-out infinite;
}

.animate-float-sphere-3 {
  animation: floatSphere3 7s ease-in-out infinite;
}

.animate-card-breath {
  animation: cardBreath 6s ease-in-out infinite;
}

.animate-fade-in {
  animation: fadeIn 0.8s cubic-bezier(0.16, 1, 0.3, 1) forwards;
}

.animate-spin-slow {
  animation: spinSlow 12s linear infinite;
}
</style>
