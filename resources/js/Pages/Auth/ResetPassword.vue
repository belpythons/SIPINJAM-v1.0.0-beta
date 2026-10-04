<script setup>
import { Head, useForm } from '@inertiajs/vue3';
import { Eye, EyeOff, KeyRound } from '@lucide/vue';
import { ref } from 'vue';

const props = defineProps({
  email: String,
  token: String,
});

const showPassword = ref(false);
const showConfirmPassword = ref(false);

const form = useForm({
  token: props.token,
  email: props.email,
  password: '',
  password_confirmation: '',
});

const submit = () => {
  form.post('/reset-password', {
    onFinish: () => {
      form.reset('password', 'password_confirmation');
    },
  });
};
</script>

<template>
  <Head title="Reset Password" />

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
      <!-- Card -->
      <div class="bg-card border border-border rounded-2xl shadow-card overflow-hidden">
        <!-- Header -->
        <div class="bg-muted/50 border-b border-border p-6">
          <div class="w-16 h-16 bg-card border border-border rounded-xl shadow-sm flex items-center justify-center mb-4 mx-auto">
            <KeyRound class="h-8 w-8 text-primary" />
          </div>
          <h1 class="text-2xl font-bold text-foreground text-center">
            Reset Password
          </h1>
          <p class="text-sm text-muted-foreground text-center mt-2 font-medium">
            Buat password baru untuk akun Anda
          </p>
        </div>

        <div class="p-6">
          <form @submit.prevent="submit" class="space-y-5">
            <!-- Email (readonly) -->
            <div class="space-y-2">
              <label for="email" class="text-xs font-semibold text-muted-foreground uppercase tracking-wider">Email</label>
              <input
                id="email"
                v-model="form.email"
                type="email"
                required
                autocomplete="username"
                class="w-full px-4 py-2.5 rounded-lg border border-border text-sm font-medium bg-muted text-muted-foreground outline-none"
                readonly
              />
              <p v-if="form.errors.email" class="text-xs text-destructive font-medium">{{ form.errors.email }}</p>
            </div>

            <!-- Password -->
            <div class="space-y-2">
              <label for="password" class="text-xs font-semibold text-muted-foreground uppercase tracking-wider">Password Baru</label>
              <div class="relative">
                <input
                  id="password"
                  v-model="form.password"
                  :type="showPassword ? 'text' : 'password'"
                  required
                  autofocus
                  autocomplete="new-password"
                  placeholder="Masukkan password baru"
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
                  placeholder="Ulangi password baru"
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
              class="w-full py-2.5 px-4 rounded-lg bg-emerald-600 text-white font-semibold text-sm shadow-sm transition-all hover:bg-emerald-700 focus:ring-2 focus:ring-ring focus:ring-offset-2 disabled:opacity-50 disabled:cursor-not-allowed"
            >
              {{ form.processing ? 'Menyimpan...' : 'Reset Password' }}
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
