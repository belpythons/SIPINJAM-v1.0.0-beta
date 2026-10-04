<script setup>
import { ref, computed } from 'vue';
import { Head, useForm, usePage } from '@inertiajs/vue3';
import UserLayout from '@/Layouts/UserLayout.vue';
import {
  User, Mail, Lock, Camera, CheckCircle2, Sparkles,
} from '@lucide/vue';
import { Card, CardContent } from '@/components/ui/card';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';

defineOptions({ layout: UserLayout });

const page = usePage();
const user = computed(() => page.props.auth?.user);

const form = useForm({
  _method: 'POST',
  name: user.value?.name || '',
  nickname: user.value?.nickname || '',
  email: user.value?.email || '',
  avatar: null,
  password: '',
  password_confirmation: '',
});

const avatarPreview = ref(user.value?.avatar || null);

const handleAvatarChange = (e) => {
  const file = e.target.files[0];
  if (file) {
    form.avatar = file;
    const reader = new FileReader();
    reader.onload = (event) => { avatarPreview.value = event.target.result; };
    reader.readAsDataURL(file);
  }
};

const submit = () => {
  form.post('/profile/edit', {
    preserveScroll: true,
    onSuccess: () => { form.password = ''; form.password_confirmation = ''; },
  });
};

const getInitials = (name) => {
  if (!name) return '?';
  return name.split(' ').map(w => w[0]).join('').toUpperCase().slice(0, 2);
};
</script>

<template>
  <Head title="Pengaturan Profil" />

  <div class="px-6 py-8 lg:px-10 max-w-3xl">
    <!-- Page Header -->
    <div class="mb-8">
      <div class="flex items-center gap-2 mb-1">
        <User class="h-5 w-5 text-primary" />
        <h1 class="text-2xl font-bold tracking-tight text-foreground">Pengaturan Profil</h1>
      </div>
      <p class="text-sm text-muted-foreground">Kelola informasi data diri dan keamanan akun Anda.</p>
    </div>

    <!-- Success Alert -->
    <div v-if="page.props.flash?.success"
      class="mb-6 flex items-center gap-2.5 rounded-lg border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-800">
      <CheckCircle2 class="h-4.5 w-4.5 text-emerald-600 shrink-0" />
      <span>{{ page.props.flash.success }}</span>
    </div>

    <!-- Form Card -->
    <Card class="border border-border shadow-card bg-card overflow-hidden mb-8">
      <CardContent class="p-6">
        <form @submit.prevent="submit" class="space-y-6">
          <!-- Avatar -->
          <div class="flex flex-col sm:flex-row items-center gap-5 pb-6 border-b border-border">
            <div class="relative group">
              <div class="w-24 h-24 rounded-full border-4 border-muted overflow-hidden bg-muted flex items-center justify-center shadow-sm transition-colors group-hover:border-primary/20">
                <img v-if="avatarPreview" :src="avatarPreview" :alt="user?.name" class="w-full h-full object-cover" />
                <span v-else class="text-2xl font-bold text-muted-foreground">{{ getInitials(form.name) }}</span>
              </div>
              <button type="button" @click="$refs.avatarInput.click()"
                class="absolute bottom-0 right-0 p-2 rounded-full bg-primary text-primary-foreground shadow-md transition-transform active:scale-95">
                <Camera class="h-3.5 w-3.5" />
              </button>
            </div>
            <div class="text-center sm:text-left space-y-1">
              <h3 class="text-sm font-bold text-foreground">Foto Profil</h3>
              <p class="text-xs text-muted-foreground">JPEG, PNG, WEBP (Maks. 5MB).</p>
              <input ref="avatarInput" type="file" accept="image/*" class="hidden" @change="handleAvatarChange" />
              <p v-if="form.errors.avatar" class="text-xs text-destructive mt-1">{{ form.errors.avatar }}</p>
            </div>
          </div>

          <!-- Fields -->
          <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
            <div class="space-y-2">
              <Label for="name" class="text-xs font-semibold text-muted-foreground">Nama Lengkap</Label>
              <div class="relative">
                <User class="absolute left-3 top-1/2 -translate-y-1/2 h-4 w-4 text-muted-foreground" />
                <Input id="name" v-model="form.name" type="text" required class="pl-10 text-sm" />
              </div>
              <p v-if="form.errors.name" class="text-xs text-destructive">{{ form.errors.name }}</p>
            </div>
            <div class="space-y-2">
              <Label for="nickname" class="text-xs font-semibold text-muted-foreground">Nickname</Label>
              <div class="relative">
                <Sparkles class="absolute left-3 top-1/2 -translate-y-1/2 h-4 w-4 text-muted-foreground" />
                <Input id="nickname" v-model="form.nickname" type="text" class="pl-10 text-sm" />
              </div>
            </div>
            <div class="space-y-2 md:col-span-2">
              <Label for="email" class="text-xs font-semibold text-muted-foreground">Email</Label>
              <div class="relative">
                <Mail class="absolute left-3 top-1/2 -translate-y-1/2 h-4 w-4 text-muted-foreground" />
                <Input id="email" v-model="form.email" type="email" required class="pl-10 text-sm" />
              </div>
              <p v-if="form.errors.email" class="text-xs text-destructive">{{ form.errors.email }}</p>
            </div>
          </div>

          <!-- Password -->
          <div class="pt-4 border-t border-border space-y-4">
            <h3 class="text-xs font-bold text-foreground uppercase tracking-wider flex items-center gap-1.5">
              <Lock class="h-4 w-4 text-muted-foreground" />Ubah Kata Sandi
            </h3>
            <p class="text-xs text-muted-foreground">Biarkan kosong jika tidak ingin mengubah kata sandi.</p>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
              <div class="space-y-2">
                <Label for="password" class="text-xs font-semibold text-muted-foreground">Kata Sandi Baru</Label>
                <Input id="password" v-model="form.password" type="password" class="text-sm" />
                <p v-if="form.errors.password" class="text-xs text-destructive">{{ form.errors.password }}</p>
              </div>
              <div class="space-y-2">
                <Label for="password_confirmation" class="text-xs font-semibold text-muted-foreground">Konfirmasi</Label>
                <Input id="password_confirmation" v-model="form.password_confirmation" type="password" class="text-sm" />
              </div>
            </div>
          </div>

          <!-- Submit -->
          <div class="pt-4 flex justify-end">
            <Button type="submit" :disabled="form.processing" class="bg-primary text-primary-foreground font-semibold min-w-[140px] shadow-sm">
              {{ form.processing ? 'Menyimpan...' : 'Simpan Perubahan' }}
            </Button>
          </div>
        </form>
      </CardContent>
    </Card>
  </div>
</template>
