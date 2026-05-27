<script setup>
import { ref, computed } from 'vue';
import { Head, useForm, usePage } from '@inertiajs/vue3';
import UserLayout from '@/Layouts/UserLayout.vue';
import AdminLayout from '@/Layouts/AdminLayout.vue';
import {
  User,
  Mail,
  ShieldCheck,
  Lock,
  Camera,
  CheckCircle2,
  AlertCircle,
  Sparkles,
} from '@lucide/vue';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';

const page = usePage();
const user = computed(() => page.props.auth?.user);
const isAdmin = computed(() => user.value?.role === 'admin');

const form = useForm({
  _method: 'POST', // Use POST with _method spoofing if PUT doesn't support multipart/form-data
  name: user.value?.name || '',
  nickname: user.value?.nickname || '',
  email: user.value?.email || '',
  avatar: null,
  password: '',
  password_confirmation: '',
});

const avatarPreview = ref(user.value?.avatar || null);
const avatarInput = ref(null);

const handleAvatarChange = (e) => {
  const file = e.target.files[0];
  if (file) {
    form.avatar = file;
    const reader = new FileReader();
    reader.onload = (event) => {
      avatarPreview.value = event.target.result;
    };
    reader.readAsDataURL(file);
  }
};

const submit = () => {
  form.post('/profile/edit', {
    preserveScroll: true,
    onSuccess: () => {
      form.password = '';
      form.password_confirmation = '';
    },
  });
};

const getInitials = (name) => {
  if (!name) return '?';
  return name.split(' ').map(w => w[0]).join('').toUpperCase().slice(0, 2);
};
</script>

<template>
  <component :is="isAdmin ? AdminLayout : UserLayout">
    <Head title="Pengaturan Profil" />

    <div class="px-6 py-8 lg:px-10 max-w-3xl">
      <!-- Page Header -->
      <div class="mb-8">
        <div class="flex items-center gap-2 mb-1">
          <User :class="['h-5 w-5', isAdmin ? 'text-orange-500' : 'text-blue-500']" />
          <h1 class="text-2xl font-bold tracking-tight text-slate-900">Pengaturan Profil</h1>
        </div>
        <p class="text-sm text-slate-500">
          Kelola informasi data diri dan keamanan akun Anda di sini.
        </p>
      </div>

      <!-- Success / Error General Alert -->
      <div
        v-if="page.props.flash?.success"
        class="mb-6 flex items-center gap-2.5 rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-800"
      >
        <CheckCircle2 class="h-4.5 w-4.5 text-emerald-600 shrink-0" />
        <span>{{ page.props.flash.success }}</span>
      </div>

      <!-- Main Form Card -->
      <Card class="border-slate-200 shadow-sm bg-white overflow-hidden mb-8">
        <CardContent class="p-6">
          <form @submit.prevent="submit" class="space-y-6">
            
            <!-- Avatar Section -->
            <div class="flex flex-col sm:flex-row items-center gap-5 pb-6 border-b border-slate-100">
              <div class="relative group">
                <div :class="[
                  'w-24 h-24 rounded-full border-4 overflow-hidden bg-slate-100 flex items-center justify-center text-slate-400 shrink-0 select-none shadow-sm transition-colors',
                  isAdmin ? 'border-orange-100 group-hover:border-orange-200' : 'border-blue-100 group-hover:border-blue-200'
                ]">
                  <img v-if="avatarPreview" :src="avatarPreview" :alt="user?.name" class="w-full h-full object-cover" />
                  <span v-else class="text-2xl font-bold text-slate-400">{{ getInitials(form.name) }}</span>
                </div>
                <!-- Camera Overlay -->
                <button
                  type="button"
                  @click="$refs.avatarInput.click()"
                  :class="[
                    'absolute bottom-0 right-0 p-2 rounded-full text-white shadow-md transition-transform active:scale-95 shrink-0',
                    isAdmin ? 'bg-orange-500 hover:bg-orange-600' : 'bg-blue-500 hover:bg-blue-600'
                  ]"
                >
                  <Camera class="h-3.5 w-3.5" />
                </button>
              </div>

              <div class="text-center sm:text-left space-y-1">
                <h3 class="text-sm font-bold text-slate-800">Foto Profil</h3>
                <p class="text-xs text-slate-400">Pilih foto terbaik untuk profil Anda.</p>
                <p class="text-[10px] text-slate-400">Mendukung JPEG, PNG, WEBP (Maks. 5MB).</p>
                
                <input
                  ref="avatarInput"
                  type="file"
                  accept="image/*"
                  class="hidden"
                  @change="handleAvatarChange"
                />
                
                <p v-if="form.errors.avatar" class="text-xs text-red-500 mt-1">{{ form.errors.avatar }}</p>
              </div>
            </div>

            <!-- Profile Fields -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
              <!-- Full Name -->
              <div class="space-y-2">
                <Label for="name" class="text-xs font-semibold text-slate-600">Nama Lengkap</Label>
                <div class="relative">
                  <User class="absolute left-3 top-1/2 -translate-y-1/2 h-4 w-4 text-slate-400" />
                  <Input
                    id="name"
                    v-model="form.name"
                    type="text"
                    required
                    class="pl-10 text-sm border-slate-200 focus:ring-orange-500/20 focus:border-orange-500"
                    :class="[isAdmin ? 'focus:border-orange-500 focus:ring-orange-500/20' : 'focus:border-blue-500 focus:ring-blue-500/20']"
                  />
                </div>
                <p v-if="form.errors.name" class="text-xs text-red-500">{{ form.errors.name }}</p>
              </div>

              <!-- Nickname -->
              <div class="space-y-2">
                <Label for="nickname" class="text-xs font-semibold text-slate-600">Nama Panggilan / Nickname</Label>
                <div class="relative">
                  <Sparkles class="absolute left-3 top-1/2 -translate-y-1/2 h-4 w-4 text-slate-400" />
                  <Input
                    id="nickname"
                    v-model="form.nickname"
                    type="text"
                    class="pl-10 text-sm border-slate-200"
                    :class="[isAdmin ? 'focus:border-orange-500 focus:ring-orange-500/20' : 'focus:border-blue-500 focus:ring-blue-500/20']"
                  />
                </div>
                <p v-if="form.errors.nickname" class="text-xs text-red-500">{{ form.errors.nickname }}</p>
              </div>

              <!-- Email -->
              <div class="space-y-2 md:col-span-2">
                <Label for="email" class="text-xs font-semibold text-slate-600">Alamat Email</Label>
                <div class="relative">
                  <Mail class="absolute left-3 top-1/2 -translate-y-1/2 h-4 w-4 text-slate-400" />
                  <Input
                    id="email"
                    v-model="form.email"
                    type="email"
                    required
                    class="pl-10 text-sm border-slate-200"
                    :class="[isAdmin ? 'focus:border-orange-500 focus:ring-orange-500/20' : 'focus:border-blue-500 focus:ring-blue-500/20']"
                  />
                </div>
                <p v-if="form.errors.email" class="text-xs text-red-500">{{ form.errors.email }}</p>
              </div>
            </div>

            <!-- Password Fields -->
            <div class="pt-4 border-t border-slate-100 space-y-4">
              <h3 class="text-xs font-bold text-slate-800 uppercase tracking-wider flex items-center gap-1.5">
                <Lock class="h-4 w-4 text-slate-400" />
                Ubah Kata Sandi
              </h3>
              <p class="text-xs text-slate-400 leading-relaxed">
                Biarkan kolom sandi kosong jika Anda tidak ingin melakukan pembaruan kata sandi saat ini.
              </p>

              <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                <!-- New Password -->
                <div class="space-y-2">
                  <Label for="password" class="text-xs font-semibold text-slate-600">Kata Sandi Baru</Label>
                  <Input
                    id="password"
                    v-model="form.password"
                    type="password"
                    class="text-sm border-slate-200"
                    :class="[isAdmin ? 'focus:border-orange-500 focus:ring-orange-500/20' : 'focus:border-blue-500 focus:ring-blue-500/20']"
                  />
                  <p v-if="form.errors.password" class="text-xs text-red-500">{{ form.errors.password }}</p>
                </div>

                <!-- Password Confirmation -->
                <div class="space-y-2">
                  <Label for="password_confirmation" class="text-xs font-semibold text-slate-600">Konfirmasi Kata Sandi</Label>
                  <Input
                    id="password_confirmation"
                    v-model="form.password_confirmation"
                    type="password"
                    class="text-sm border-slate-200"
                    :class="[isAdmin ? 'focus:border-orange-500 focus:ring-orange-500/20' : 'focus:border-blue-500 focus:ring-blue-500/20']"
                  />
                </div>
              </div>
            </div>

            <!-- Submit Button -->
            <div class="pt-4 flex justify-end">
              <Button
                type="submit"
                :disabled="form.processing"
                :class="[
                  'font-semibold text-white min-w-[140px] transition-colors shadow-sm',
                  isAdmin ? 'bg-orange-600 hover:bg-orange-700' : 'bg-blue-600 hover:bg-blue-700'
                ]"
              >
                {{ form.processing ? 'Menyimpan...' : 'Simpan Perubahan' }}
              </Button>
            </div>
          </form>
        </CardContent>
      </Card>
    </div>
  </component>
</template>
