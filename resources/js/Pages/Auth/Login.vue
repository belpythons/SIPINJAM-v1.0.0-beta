<script setup>
import { Head, useForm } from '@inertiajs/vue3';
import { ref, computed } from 'vue';

const form = useForm({
    email: '',
    password: '',
    remember: false,
});

const showPassword = ref(false);
const activeRole = ref(null);

const credentials = {
    admin: { email: 'admin@stitek.ac.id', password: 'admin123' },
    mahasiswa: { email: 'mahasiswa@stitek.ac.id', password: 'user123' },
};

const selectRole = (role) => {
    if (activeRole.value === role) {
        activeRole.value = null;
        form.email = '';
        form.password = '';
        return;
    }
    activeRole.value = role;
    form.email = credentials[role].email;
    form.password = credentials[role].password;
};

// ── Dynamic Hue ────────────────────────────────────
const isAdmin = computed(() => activeRole.value === 'admin');

const panelGradient = computed(() =>
    isAdmin.value
        ? 'from-orange-500 via-orange-600 to-amber-700'
        : 'from-blue-600 via-blue-700 to-indigo-800'
);

const accentBg = computed(() => isAdmin.value ? 'bg-orange-500' : 'bg-blue-500');
const accentText = computed(() => isAdmin.value ? 'text-orange-700' : 'text-blue-700');
const accentBorder = computed(() => isAdmin.value ? 'border-orange-500' : 'border-blue-500');
const accentBgLight = computed(() => isAdmin.value ? 'bg-orange-50' : 'bg-blue-50');
const accentShadow = computed(() => isAdmin.value ? 'shadow-orange-500/10' : 'shadow-blue-500/10');
const accentDot = computed(() => isAdmin.value ? 'bg-orange-500' : 'bg-blue-500');
const submitBg = computed(() => isAdmin.value ? 'bg-orange-600 hover:bg-orange-700' : 'bg-blue-600 hover:bg-blue-700');
const panelTextMuted = computed(() => isAdmin.value ? 'text-orange-200/50' : 'text-blue-200/50');
const panelTextSub = computed(() => isAdmin.value ? 'text-orange-100/90' : 'text-blue-100/90');
const panelTextItalic = computed(() => isAdmin.value ? 'text-orange-200/60' : 'text-blue-200/60');
const panelFeatureText = computed(() => isAdmin.value ? 'text-orange-100/70' : 'text-blue-100/70');

const submit = () => {
    form.post('/login', {
        onFinish: () => form.reset('password'),
    });
};
</script>

<template>
    <Head title="Login" />

    <div class="min-h-screen flex">
        <!-- Left Panel: Branding -->
        <div :class="['hidden lg:flex lg:w-1/2 relative overflow-hidden bg-gradient-to-br transition-all duration-500', panelGradient]">
            <div class="absolute inset-0">
                <div class="absolute top-0 left-0 w-96 h-96 bg-white/10 rounded-full blur-3xl -translate-x-1/2 -translate-y-1/2"></div>
                <div class="absolute bottom-0 right-0 w-80 h-80 bg-white/10 rounded-full blur-3xl translate-x-1/3 translate-y-1/3"></div>
            </div>
            <div class="absolute inset-0 opacity-[0.03]" style="background-image: url('data:image/svg+xml,%3Csvg width=&quot;60&quot; height=&quot;60&quot; viewBox=&quot;0 0 60 60&quot; xmlns=&quot;http://www.w3.org/2000/svg&quot;%3E%3Cg fill=&quot;none&quot; fill-rule=&quot;evenodd&quot;%3E%3Cg fill=&quot;%23ffffff&quot; fill-opacity=&quot;1&quot;%3E%3Cpath d=&quot;M36 34v-4h-2v4h-4v2h4v4h2v-4h4v-2h-4zm0-30V0h-2v4h-4v2h4v4h2V6h4V4h-4zM6 34v-4H4v4H0v2h4v4h2v-4h4v-2H6zM6 4V0H4v4H0v2h4v4h2V6h4V4H6z&quot;/%3E%3C/g%3E%3C/g%3E%3C/svg%3E');"></div>

            <div class="relative z-10 flex flex-col justify-center px-12 xl:px-16 text-white">
                <div class="mb-8">
                    <div class="w-16 h-16 bg-white/10 backdrop-blur-sm rounded-2xl flex items-center justify-center border border-white/20 shadow-lg">
                        <svg class="w-9 h-9 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 21v-8.25M15.75 21v-8.25M8.25 21v-8.25M3 9l9-6 9 6m-1.5 12V10.332A48.36 48.36 0 0012 9.75c-2.551 0-5.056.2-7.5.582V21M3 21h18M12 6.75h.008v.008H12V6.75z" />
                        </svg>
                    </div>
                </div>
                <h1 class="text-4xl xl:text-5xl font-bold leading-tight tracking-tight mb-4">SiPinjam</h1>
                <p :class="['text-lg xl:text-xl font-light leading-relaxed mb-3 transition-colors duration-500', panelTextSub]">Sistem Peminjaman Aset Kampus</p>
                <p :class="['text-sm font-light italic tracking-wide transition-colors duration-500', panelTextItalic]">STITEK Bontang</p>

                <div class="mt-12 pt-8 border-t border-white/10">
                    <p :class="['text-xs tracking-[0.2em] uppercase font-medium transition-colors duration-500', panelTextMuted]">The Knowledgeable and Virtue Campus</p>
                </div>

                <div class="mt-10 space-y-4">
                    <div :class="['flex items-center gap-3 transition-colors duration-500', panelFeatureText]">
                        <div class="w-8 h-8 rounded-lg bg-white/10 flex items-center justify-center flex-shrink-0">
                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                        </div>
                        <span class="text-sm">Peminjaman ruangan & barang secara online</span>
                    </div>
                    <div :class="['flex items-center gap-3 transition-colors duration-500', panelFeatureText]">
                        <div class="w-8 h-8 rounded-lg bg-white/10 flex items-center justify-center flex-shrink-0">
                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                        </div>
                        <span class="text-sm">Tracking status persetujuan real-time</span>
                    </div>
                    <div :class="['flex items-center gap-3 transition-colors duration-500', panelFeatureText]">
                        <div class="w-8 h-8 rounded-lg bg-white/10 flex items-center justify-center flex-shrink-0">
                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12h3.75M9 15h3.75M9 18h3.75m3 .75H18a2.25 2.25 0 002.25-2.25V6.108c0-1.135-.845-2.098-1.976-2.192a48.424 48.424 0 00-1.123-.08m-5.801 0c-.065.21-.1.433-.1.664 0 .414.336.75.75.75h4.5a.75.75 0 00.75-.75 2.25 2.25 0 00-.1-.664m-5.8 0A2.251 2.251 0 0113.5 2.25H15a2.25 2.25 0 012.15 1.586m-5.8 0c-.376.023-.75.05-1.124.08C9.095 4.01 8.25 4.973 8.25 6.108V8.25m0 0H4.875c-.621 0-1.125.504-1.125 1.125v11.25c0 .621.504 1.125 1.125 1.125h9.75c.621 0 1.125-.504 1.125-1.125V9.375c0-.621-.504-1.125-1.125-1.125H8.25zM6.75 12h.008v.008H6.75V12zm0 3h.008v.008H6.75V15zm0 3h.008v.008H6.75V18z" /></svg>
                        </div>
                        <span class="text-sm">Cetak bukti peminjaman otomatis (PDF)</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Right Panel: Login Form -->
        <div class="w-full lg:w-1/2 flex items-center justify-center px-6 sm:px-12 bg-background">
            <div class="w-full max-w-md">
                <!-- Mobile branding -->
                <div class="lg:hidden text-center mb-8">
                    <div class="w-14 h-14 bg-primary/10 rounded-2xl flex items-center justify-center mx-auto mb-4">
                        <svg class="w-7 h-7 text-primary" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 21v-8.25M15.75 21v-8.25M8.25 21v-8.25M3 9l9-6 9 6m-1.5 12V10.332A48.36 48.36 0 0012 9.75c-2.551 0-5.056.2-7.5.582V21M3 21h18M12 6.75h.008v.008H12V6.75z" />
                        </svg>
                    </div>
                    <h1 class="text-2xl font-bold text-foreground">SiPinjam</h1>
                    <p class="text-sm text-muted-foreground mt-1">STITEK Bontang</p>
                </div>

                <div class="mb-8">
                    <h2 class="text-2xl font-semibold text-foreground tracking-tight">Masuk ke akun Anda</h2>
                    <p class="text-sm text-muted-foreground mt-2">Silakan masuk untuk mengakses layanan peminjaman aset kampus.</p>
                </div>

                <!-- Quick Login Toggle -->
                <div class="mb-6">
                    <p class="text-xs font-semibold text-muted-foreground uppercase tracking-wider mb-3">Login Cepat (Demo)</p>
                    <div class="grid grid-cols-2 gap-3">
                        <button id="btn-toggle-admin" type="button" @click="selectRole('admin')"
                            :class="['relative flex items-center gap-3 rounded-xl border-2 px-4 py-3 text-left transition-all duration-300 group',
                                activeRole === 'admin'
                                    ? `${accentBorder} ${accentBgLight} shadow-sm ${accentShadow}`
                                    : 'border-slate-200 bg-white hover:border-slate-300 hover:bg-slate-50']">
                            <div :class="['flex h-9 w-9 shrink-0 items-center justify-center rounded-lg transition-colors duration-300',
                                activeRole === 'admin' ? `${accentBg} text-white` : 'bg-slate-100 text-slate-500 group-hover:bg-slate-200']">
                                <svg class="w-4.5 h-4.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75m-3-7.036A11.959 11.959 0 013.598 6 11.99 11.99 0 003 9.749c0 5.592 3.824 10.29 9 11.623 5.176-1.332 9-6.03 9-11.622 0-1.31-.21-2.571-.598-3.751h-.152c-3.196 0-6.1-1.248-8.25-3.285z" />
                                </svg>
                            </div>
                            <div>
                                <p :class="['text-sm font-semibold transition-colors duration-300', activeRole === 'admin' ? accentText : 'text-slate-700']">Admin</p>
                                <p class="text-[10px] text-muted-foreground">admin@stitek.ac.id</p>
                            </div>
                            <div v-if="activeRole === 'admin'" class="absolute top-2 right-2">
                                <span :class="['flex h-2 w-2 rounded-full transition-colors duration-300', accentDot]"></span>
                            </div>
                        </button>

                        <button id="btn-toggle-mahasiswa" type="button" @click="selectRole('mahasiswa')"
                            :class="['relative flex items-center gap-3 rounded-xl border-2 px-4 py-3 text-left transition-all duration-300 group',
                                activeRole === 'mahasiswa'
                                    ? 'border-blue-500 bg-blue-50 shadow-sm shadow-blue-500/10'
                                    : 'border-slate-200 bg-white hover:border-slate-300 hover:bg-slate-50']">
                            <div :class="['flex h-9 w-9 shrink-0 items-center justify-center rounded-lg transition-colors duration-300',
                                activeRole === 'mahasiswa' ? 'bg-blue-500 text-white' : 'bg-slate-100 text-slate-500 group-hover:bg-slate-200']">
                                <svg class="w-4.5 h-4.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M4.26 10.147a60.438 60.438 0 00-.491 6.347A48.627 48.627 0 0112 20.904a48.627 48.627 0 018.232-4.41 60.46 60.46 0 00-.491-6.347m-15.482 0a50.57 50.57 0 00-2.658-.813A59.905 59.905 0 0112 3.493a59.902 59.902 0 0110.399 5.84c-.896.248-1.783.52-2.658.814m-15.482 0A50.717 50.717 0 0112 13.489a50.702 50.702 0 017.74-3.342M6.75 15a.75.75 0 100-1.5.75.75 0 000 1.5zm0 0v-3.675A55.378 55.378 0 0112 8.443m-7.007 11.55A5.981 5.981 0 006.75 15.75v-1.5" />
                                </svg>
                            </div>
                            <div>
                                <p :class="['text-sm font-semibold transition-colors duration-300', activeRole === 'mahasiswa' ? 'text-blue-700' : 'text-slate-700']">Mahasiswa</p>
                                <p class="text-[10px] text-muted-foreground">mahasiswa@stitek.ac.id</p>
                            </div>
                            <div v-if="activeRole === 'mahasiswa'" class="absolute top-2 right-2">
                                <span class="flex h-2 w-2 rounded-full bg-blue-500"></span>
                            </div>
                        </button>
                    </div>
                </div>

                <!-- Google Login -->
                <a href="/auth/google" id="btn-google-login"
                    class="w-full inline-flex items-center justify-center gap-3 rounded-lg border border-input bg-background px-4 py-3 text-sm font-medium text-foreground shadow-sm transition-all duration-200 hover:bg-accent hover:border-ring focus:outline-none focus:ring-2 focus:ring-ring focus:ring-offset-2">
                    <svg class="w-5 h-5" viewBox="0 0 24 24">
                        <path d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92a5.06 5.06 0 01-2.2 3.32v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.1z" fill="#4285F4"/>
                        <path d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z" fill="#34A853"/>
                        <path d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.07H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.93l2.85-2.22.81-.62z" fill="#FBBC05"/>
                        <path d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.07l3.66 2.84c.87-2.6 3.3-4.53 6.16-4.53z" fill="#EA4335"/>
                    </svg>
                    Masuk dengan Google
                </a>

                <div class="relative my-6">
                    <div class="absolute inset-0 flex items-center"><div class="w-full border-t border-border"></div></div>
                    <div class="relative flex justify-center text-xs uppercase">
                        <span class="bg-background px-3 text-muted-foreground">atau masuk dengan email</span>
                    </div>
                </div>

                <div v-if="$page.props.flash.error" class="mb-4 rounded-lg border border-destructive/30 bg-destructive/5 px-4 py-3 text-sm text-destructive">
                    {{ $page.props.flash.error }}
                </div>

                <form @submit.prevent="submit" class="space-y-5">
                    <div class="space-y-2">
                        <label for="email" class="text-sm font-medium text-foreground">Email</label>
                        <input id="email" v-model="form.email" type="email" required autofocus autocomplete="username" placeholder="nama@email.com"
                            class="w-full rounded-lg border border-input bg-background px-4 py-3 text-sm text-foreground placeholder:text-muted-foreground shadow-sm transition-colors duration-200 focus:border-ring focus:outline-none focus:ring-2 focus:ring-ring/20"
                            :class="{ 'border-destructive focus:border-destructive focus:ring-destructive/20': form.errors.email }" />
                        <p v-if="form.errors.email" class="text-xs text-destructive mt-1">{{ form.errors.email }}</p>
                    </div>

                    <div class="space-y-2">
                        <label for="password" class="text-sm font-medium text-foreground">Password</label>
                        <div class="relative">
                            <input id="password" v-model="form.password" :type="showPassword ? 'text' : 'password'" required autocomplete="current-password" placeholder="••••••••"
                                class="w-full rounded-lg border border-input bg-background px-4 py-3 pr-11 text-sm text-foreground placeholder:text-muted-foreground shadow-sm transition-colors duration-200 focus:border-ring focus:outline-none focus:ring-2 focus:ring-ring/20"
                                :class="{ 'border-destructive focus:border-destructive focus:ring-destructive/20': form.errors.password }" />
                            <button type="button" @click="showPassword = !showPassword" class="absolute right-3 top-1/2 -translate-y-1/2 text-muted-foreground hover:text-foreground transition-colors">
                                <svg v-if="!showPassword" class="w-4.5 h-4.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 010-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178z" /><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" /></svg>
                                <svg v-else class="w-4.5 h-4.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M3.98 8.223A10.477 10.477 0 001.934 12C3.226 16.338 7.244 19.5 12 19.5c.993 0 1.953-.138 2.863-.395M6.228 6.228A10.45 10.45 0 0112 4.5c4.756 0 8.773 3.162 10.065 7.498a10.523 10.523 0 01-4.293 5.774M6.228 6.228L3 3m3.228 3.228l3.65 3.65m7.894 7.894L21 21m-3.228-3.228l-3.65-3.65m0 0a3 3 0 10-4.243-4.243m4.242 4.242L9.88 9.88" /></svg>
                            </button>
                        </div>
                        <p v-if="form.errors.password" class="text-xs text-destructive mt-1">{{ form.errors.password }}</p>
                    </div>

                    <div class="flex items-center gap-2">
                        <input id="remember" v-model="form.remember" type="checkbox" class="h-4 w-4 rounded border-input text-primary shadow-sm focus:ring-ring/20 focus:ring-2 transition-colors" />
                        <label for="remember" class="text-sm text-muted-foreground select-none">Ingat saya</label>
                    </div>

                    <button id="btn-login" type="submit" :disabled="form.processing"
                        :class="['w-full rounded-lg px-4 py-3 text-sm font-semibold text-white shadow-sm transition-all duration-300 focus:outline-none focus:ring-2 focus:ring-ring focus:ring-offset-2 disabled:opacity-50 disabled:cursor-not-allowed', submitBg]">
                        <span v-if="form.processing" class="inline-flex items-center gap-2">
                            <svg class="animate-spin h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                            Memproses...
                        </span>
                        <span v-else>Masuk</span>
                    </button>
                </form>

                <div class="mt-8 pt-6 border-t border-border text-center">
                    <p class="text-sm text-muted-foreground">
                        Tidak punya akun?
                        <a id="link-wa-admin" href="https://wa.me/6281234567890?text=Halo%20Admin%20SIPINJAM,%20saya%20ingin%20mengajukan%20pembuatan%20akun." target="_blank" rel="noopener noreferrer"
                            class="font-medium text-primary hover:text-primary/80 underline underline-offset-4 transition-colors">Hubungi kami via WA</a>
                    </p>
                </div>

                <p class="lg:hidden mt-6 text-center text-[11px] tracking-[0.15em] uppercase text-muted-foreground/50">The Knowledgeable and Virtue Campus</p>
            </div>
        </div>
    </div>
</template>
