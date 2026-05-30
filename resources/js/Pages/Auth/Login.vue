<script setup>
import { Head, useForm, usePage, Link } from '@inertiajs/vue3';
import { ref, computed, onMounted, onUnmounted } from 'vue';
import gsap from 'gsap';
import { Mail, Lock, Eye, EyeOff, LogIn } from '@lucide/vue';

const page = usePage();
const showPassword = ref(false);
const selectedRole = ref('mahasiswa');
const shapeContainerRef = ref(null);

const form = useForm({
  email: '',
  password: '',
  remember: false,
  role: 'user',
});

const setRole = (role) => {
  selectedRole.value = role;
  form.role = role === 'admin' ? 'admin' : 'user';
  form.email = role === 'admin' ? 'admin@sipinjam.test' : 'user@sipinjam.test';
  form.password = 'password';
};

// ── Background color based on role ────────────
const panelBgClass = computed(() => {
  if (selectedRole.value === 'admin') return 'bg-orange-50/50';
  if (selectedRole.value === 'mahasiswa') return 'bg-blue-50/50';
  return 'bg-background';
});

const submit = () => {
  form.post('/login', { preserveScroll: true });
};

// ── GSAP Shapes ──────────────────────────
const shapeColors = ['#3b82f6', '#6366f1', '#8b5cf6', '#06b6d4', '#10b981', '#f59e0b', '#ef4444', '#ec4899'];
const shapeTypes = ['square', 'circle'];

function generateShapes() {
  const result = [];
  for (let i = 0; i < 15; i++) {
    result.push({
      id: i,
      type: shapeTypes[i % shapeTypes.length],
      color: shapeColors[i % shapeColors.length],
      size: 20 + Math.random() * 40,
      x: 5 + Math.random() * 90,
      y: 5 + Math.random() * 90,
      rotation: Math.random() * 45 - 22,
    });
  }
  return result;
}

const shapes = ref(generateShapes());

onMounted(() => {
  if (!shapeContainerRef.value) return;
  const shapeEls = shapeContainerRef.value.querySelectorAll('.gsap-shape');
  if (!shapeEls.length) return;

  gsap.from(shapeEls, {
    scale: 0, rotation: -180, opacity: 0, duration: 0.6,
    stagger: { amount: 1, from: 'random' }, ease: 'back.out(1.7)',
  });

  shapeEls.forEach((el) => {
    gsap.to(el, {
      y: `+=${10 + Math.random() * 20}`,
      x: `+=${-8 + Math.random() * 16}`,
      rotation: `+=${-6 + Math.random() * 12}`,
      duration: 3 + Math.random() * 2, ease: 'sine.inOut',
      repeat: -1, yoyo: true,
    });

    el.addEventListener('mouseenter', () => {
      gsap.to(el, { scale: 1.3, boxShadow: '0 8px 30px rgba(0,0,0,0.12)', duration: 0.25, ease: 'power2.out' });
    });
    el.addEventListener('mouseleave', () => {
      gsap.to(el, { scale: 1, boxShadow: '0 2px 8px rgba(0,0,0,0.06)', duration: 0.25, ease: 'power2.out' });
    });
    el.addEventListener('click', () => {
      const newColor = shapeColors[Math.floor(Math.random() * shapeColors.length)];
      gsap.timeline()
        .to(el, { scale: 0.8, rotation: '+=90', duration: 0.1 })
        .to(el, { scale: 1.15, backgroundColor: newColor, duration: 0.5, ease: 'elastic.out(1, 0.3)' })
        .to(el, { scale: 1, duration: 0.3, ease: 'power2.out' });
    });
  });
});
</script>

<template>
  <Head title="Masuk ke SiPinjam" />

  <div :class="['flex min-h-screen font-sans antialiased transition-colors duration-500', panelBgClass]">
    <!-- ── Left Panel — GSAP Shapes (hidden on small screens) ── -->
    <div ref="shapeContainerRef"
      class="hidden lg:flex lg:w-1/2 relative overflow-hidden bg-gradient-to-br from-primary/5 via-background/10 to-primary/10 items-center justify-center">

      <div v-for="shape in shapes" :key="shape.id"
        class="gsap-shape absolute cursor-pointer transition-shadow"
        :style="{
          left: shape.x + '%', top: shape.y + '%',
          width: shape.size + 'px', height: shape.size + 'px',
          backgroundColor: shape.color, transform: `rotate(${shape.rotation}deg)`,
          borderRadius: shape.type === 'circle' ? '50%' : '6px',
          opacity: 0.25, boxShadow: '0 2px 8px rgba(0,0,0,0.06)',
        }"
      />

      <!-- Center Branding -->
      <div class="relative z-10 text-center space-y-3 px-12">
        <h2 class="text-5xl font-extrabold tracking-tight text-foreground">SiPinjam</h2>
        <p class="text-sm font-medium tracking-wider text-muted-foreground uppercase">STITEK Bontang</p>
        <p class="text-sm text-muted-foreground max-w-xs mx-auto leading-relaxed">
          Sistem Peminjaman Aset Kampus — Praktis, Terintegrasi, Terpantau.
        </p>
      </div>
    </div>

    <!-- ── Right Panel — Form ─────────────────────── -->
    <div class="flex flex-1 items-center justify-center p-6 sm:p-12">
      <div class="w-full max-w-md space-y-8">
        <!-- Mobile Brand -->
        <div class="lg:hidden text-center space-y-1">
          <h2 class="text-3xl font-extrabold tracking-tight text-foreground">SiPinjam</h2>
          <p class="text-xs tracking-widest uppercase text-muted-foreground font-medium">STITEK Bontang</p>
        </div>

        <div class="space-y-1.5">
          <h1 class="text-2xl font-bold text-foreground">Masuk ke akun Anda</h1>
          <p class="text-sm text-muted-foreground">Gunakan email institusi yang telah terdaftar.</p>
        </div>

        <!-- Flash Error (blocked, etc.) -->
        <div v-if="page.props.flash?.error" class="flex items-start gap-3 bg-destructive/10 border border-destructive/20 rounded-lg p-4 text-sm text-destructive font-medium">
          <span>{{ page.props.flash.error }}</span>
        </div>

        <!-- Role Toggle -->
        <div class="bg-muted rounded-lg p-1 flex gap-1">
          <button v-for="role in [{key:'mahasiswa',label:'Mahasiswa'},{key:'admin',label:'Admin'}]"
            :key="role.key" @click="setRole(role.key)"
            :class="['flex-1 py-2.5 text-sm font-semibold rounded-md transition-all duration-200',
              selectedRole === role.key ? 'bg-card text-foreground shadow-sm' : 'text-muted-foreground hover:text-foreground']">
            {{ role.label }}
          </button>
        </div>

        <!-- Google Login -->
        <a href="/auth/google" id="btn-google-login"
          class="flex items-center justify-center gap-3 w-full border border-border bg-card text-foreground font-medium text-sm px-4 py-3 rounded-lg shadow-sm transition-all duration-200 hover:bg-muted">
          <svg class="w-5 h-5" viewBox="0 0 24 24"><path d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92a5.06 5.06 0 0 1-2.2 3.32v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.1z" fill="#4285F4"/><path d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z" fill="#34A853"/><path d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.07H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.93l2.85-2.22.81-.62z" fill="#FBBC05"/><path d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.07l3.66 2.84c.87-2.6 3.3-4.53 6.16-4.53z" fill="#EA4335"/></svg>
          Masuk dengan Google
        </a>

        <div class="relative flex items-center">
          <span class="flex-1 border-t border-border" />
          <span class="mx-4 text-xs text-muted-foreground font-medium">atau masuk dengan email</span>
          <span class="flex-1 border-t border-border" />
        </div>

        <!-- Login Form -->
        <form @submit.prevent="submit" class="space-y-5">
          <!-- Email -->
          <div class="space-y-2">
            <label for="email" class="text-sm font-medium text-foreground">Email</label>
            <div class="relative">
              <Mail class="absolute left-3 top-1/2 -translate-y-1/2 h-4 w-4 text-muted-foreground" />
              <input id="email" v-model="form.email" type="email" required autocomplete="email" placeholder="nama@stitek.ac.id"
                class="w-full pl-10 pr-4 py-2.5 border border-input bg-background text-sm rounded-lg placeholder:text-muted-foreground focus:outline-none focus:ring-2 focus:ring-ring/20 focus:border-ring transition-all" />
            </div>
            <p v-if="form.errors.email" class="text-xs text-destructive">{{ form.errors.email }}</p>
          </div>

          <!-- Password -->
          <div class="space-y-2">
            <div class="flex items-center justify-between">
              <label for="password" class="text-sm font-medium text-foreground">Kata Sandi</label>
              <Link href="/forgot-password" class="text-xs text-primary hover:text-primary/80 font-medium transition-colors">Lupa kata sandi?</Link>
            </div>
            <div class="relative">
              <Lock class="absolute left-3 top-1/2 -translate-y-1/2 h-4 w-4 text-muted-foreground" />
              <input id="password" v-model="form.password" :type="showPassword ? 'text' : 'password'" required placeholder="••••••••"
                class="w-full pl-10 pr-10 py-2.5 border border-input bg-background text-sm rounded-lg placeholder:text-muted-foreground focus:outline-none focus:ring-2 focus:ring-ring/20 focus:border-ring transition-all" />
              <button type="button" @click="showPassword = !showPassword" class="absolute right-3 top-1/2 -translate-y-1/2 text-muted-foreground hover:text-foreground transition-colors">
                <EyeOff v-if="showPassword" class="h-4 w-4" /><Eye v-else class="h-4 w-4" />
              </button>
            </div>
            <p v-if="form.errors.password" class="text-xs text-destructive">{{ form.errors.password }}</p>
          </div>

          <!-- Remember -->
          <div class="flex items-center gap-2">
            <input id="remember" v-model="form.remember" type="checkbox" class="rounded border-input text-primary focus:ring-ring/20" />
            <label for="remember" class="text-sm text-muted-foreground select-none">Ingat saya</label>
          </div>

          <!-- Submit -->
          <button type="submit" :disabled="form.processing" id="btn-login-submit"
            class="w-full bg-primary text-primary-foreground font-semibold text-sm py-3 rounded-lg shadow-sm transition-all duration-200 hover:opacity-90 disabled:opacity-50 flex items-center justify-center gap-2">
            <LogIn class="h-4 w-4" />
            {{ form.processing ? 'Memproses...' : 'Masuk' }}
          </button>
        </form>

        <!-- Footer -->
        <p class="text-center text-xs text-muted-foreground pt-4">
          Butuh bantuan? Hubungi admin di
          <a href="https://wa.me/628123456789" target="_blank" class="text-primary font-medium hover:underline">WhatsApp</a>.
        </p>
      </div>
    </div>
  </div>
</template>
