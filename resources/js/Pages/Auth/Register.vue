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
            <UserPlus class="h-8 w-8 text-primary" />
          </div>
          <h1 class="text-2xl font-bold text-foreground text-center">
            Buat Akun Baru
          </h1>
          <p class="text-sm text-muted-foreground text-center mt-2 font-medium">
            Daftarkan diri Anda untuk mengakses layanan peminjaman kampus
          </p>
        </div>

        <div class="p-6">
          <form @submit.prevent="submit" class="space-y-5">
            <!-- Name -->
            <div class="space-y-2">
              <label for="name" class="text-xs font-semibold text-muted-foreground uppercase tracking-wider">Nama Lengkap</label>
              <input
                id="name"
                v-model="form.name"
                type="text"
                required
                autofocus
                autocomplete="name"
                placeholder="Masukkan nama lengkap"
                class="w-full px-4 py-2.5 rounded-lg border border-border text-sm font-medium bg-background text-foreground placeholder:text-muted-foreground outline-none transition-all focus:ring-2 focus:ring-ring focus:ring-offset-2"
              />
              <p v-if="form.errors.name" class="text-xs text-destructive font-medium">{{ form.errors.name }}</p>
            </div>

            <!-- Email -->
            <div class="space-y-2">
              <label for="email" class="text-xs font-semibold text-muted-foreground uppercase tracking-wider">Email</label>
              <input
                id="email"
                v-model="form.email"
                type="email"
                required
                autocomplete="username"
                placeholder="nama@stitek.ac.id"
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
                  autocomplete="new-password"
                  placeholder="Buat password baru"
                  class="w-full px-4 py-2.5 pr-12 rounded-lg border border-border text-sm font-medium bg-background text-foreground placeholder:text-muted-foreground outline-none transition-all focus:ring-2 focus:ring-ring focus:ring-offset-2"
                />
                <button type="button" @click="showPassword = !showPassword" class="absolute right-4 top-1/2 -translate-y-1/2 text-muted-foreground hover:text-foreground transition-colors">
                  <EyeOff v-if="showPassword" class="h-5 w-5" />
                  <Eye v-else class="h-5 w-5" />
                </button>
              </div>
              <p v-if="form.errors.password" class="text-xs text-destructive font-medium">{{ form.errors.password }}</p>
            </div>

            <!-- Confirm Password -->
            <div class="space-y-2">
              <label for="password_confirmation" class="text-xs font-semibold text-muted-foreground uppercase tracking-wider">Konfirmasi Password</label>
              <div class="relative">
                <input
                  id="password_confirmation"
                  v-model="form.password_confirmation"
                  :type="showConfirmPassword ? 'text' : 'password'"
                  required
                  autocomplete="new-password"
                  placeholder="Ulangi password"
                  class="w-full px-4 py-2.5 pr-12 rounded-lg border border-border text-sm font-medium bg-background text-foreground placeholder:text-muted-foreground outline-none transition-all focus:ring-2 focus:ring-ring focus:ring-offset-2"
                />
                <button type="button" @click="showConfirmPassword = !showConfirmPassword" class="absolute right-4 top-1/2 -translate-y-1/2 text-muted-foreground hover:text-foreground transition-colors">
                  <EyeOff v-if="showConfirmPassword" class="h-5 w-5" />
                  <Eye v-else class="h-5 w-5" />
                </button>
              </div>
              <p v-if="form.errors.password_confirmation" class="text-xs text-destructive font-medium">{{ form.errors.password_confirmation }}</p>
            </div>

            <!-- Submit -->
            <button
              type="submit"
              :disabled="form.processing"
              class="w-full py-2.5 px-4 rounded-lg bg-primary text-primary-foreground font-semibold text-sm shadow-sm transition-all hover:bg-primary/90 focus:ring-2 focus:ring-ring focus:ring-offset-2 disabled:opacity-50 disabled:cursor-not-allowed"
            >
              {{ form.processing ? 'Mendaftarkan...' : 'Daftar Sekarang' }}
            </button>
          </form>

          <!-- Footer -->
          <div class="mt-5 text-center border-t border-border pt-5">
            <span class="text-sm text-muted-foreground font-medium">Sudah punya akun?</span>
            <Link href="/login" class="text-sm font-semibold text-primary hover:text-primary/80 ml-1">
              Masuk di sini
            </Link>
          </div>
        </div>
      </div>

      <!-- Footer -->
      <p class="text-center text-xs text-muted-foreground font-medium">
        &copy; 2026 SiPinjam — STITEK Bontang
      </p>
    </div>
  </div>
</template>
