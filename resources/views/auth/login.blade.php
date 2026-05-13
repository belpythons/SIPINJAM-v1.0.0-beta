<x-guest-layout>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">

<style>
    *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
    html, body { height: 100%; font-family: 'Plus Jakarta Sans', sans-serif; -webkit-font-smoothing: antialiased; }
    body { display: flex; min-height: 100vh; background: #ffffff; }

    /* ===== LEFT PANEL ===== */
    .left-panel {
        width: 55%; min-height: 100vh;
        display: flex; flex-direction: column; padding: 48px 64px;
        position: relative; overflow: hidden;
        z-index: 1;
        /* Transisi halus saat ganti role */
        transition: background 0.6s ease;
    }

    /* Warna Dasar Panel dengan Linear Gradient yang Elegan */
    .left-panel.mode-user  { 
        background: linear-gradient(135deg, #0f172a 0%, #1e3a8a 100%); 
    } 
    .left-panel.mode-admin { 
        background: linear-gradient(135deg, #431407 0%, #9a3412 100%); 
    }

    /* Efek Pola Grid (Tech Vibe) */
    .pattern-overlay {
        position: absolute; inset: 0;
        background-image: 
            linear-gradient(rgba(255,255,255,0.04) 1px, transparent 1px),
            linear-gradient(90deg, rgba(255,255,255,0.04) 1px, transparent 1px);
        background-size: 40px 40px;
        z-index: -2;
    }

    /* Efek Floating Glass Shapes (Pengganti Bulatan) */
    .bg-shape {
        position: absolute;
        border-radius: 32px;
        background: linear-gradient(135deg, rgba(255,255,255,0.08) 0%, rgba(255,255,255,0) 100%);
        border: 1px solid rgba(255,255,255,0.08);
        backdrop-filter: blur(8px);
        -webkit-backdrop-filter: blur(8px);
        z-index: -1;
        transition: all 0.6s ease;
    }
    
    .shape-1 { width: 450px; height: 450px; top: -10%; right: -15%; transform: rotate(-15deg); }
    .shape-2 { width: 350px; height: 350px; bottom: -5%; left: -10%; transform: rotate(25deg); }
    .shape-3 { width: 200px; height: 200px; top: 40%; right: 15%; transform: rotate(45deg); opacity: 0.7;}

    /* Shadow warna dinamis untuk shapes */
    .left-panel.mode-user .bg-shape { box-shadow: 0 20px 40px rgba(37, 99, 235, 0.15); }
    .left-panel.mode-admin .bg-shape { box-shadow: 0 20px 40px rgba(234, 88, 12, 0.15); }

    /* Elemen Header Kiri */
    .logo-wrap { display: flex; align-items: center; gap: 16px; position: relative; z-index: 1; margin-bottom: auto; }
    .logo-icon {
        width: 52px; height: 52px; border-radius: 12px;
        display: flex; align-items: center; justify-content: center;
        flex-shrink: 0; overflow: hidden; background: transparent;
    }
    .logo-img { width: 100%; height: 100%; object-fit: contain; }
    .logo-text-title { color: #fff; font-weight: 800; font-size: 20px; letter-spacing: 0.03em; line-height: 1.2; }
    .logo-text-sub   { color: rgba(255,255,255,0.7); font-size: 13px; font-weight: 500; margin-top: 2px; }

    /* Konten Tengah (Hero) */
    .hero-content { flex: none; display: flex; flex-direction: column; justify-content: center; position: relative; z-index: 1; margin: auto 0; }
    .hero-eyebrow {
        color: rgba(255,255,255,0.7); font-size: 13px; font-weight: 700;
        letter-spacing: 0.15em; text-transform: uppercase; margin-bottom: 20px;
        display: flex; align-items: center; gap: 12px;
    }
    .hero-eyebrow::before { content: ''; display: inline-block; width: 30px; height: 2px; background: rgba(255,255,255,0.5); border-radius: 2px; }
    .hero-title { color: #fff; font-size: 46px; font-weight: 800; line-height: 1.15; margin-bottom: 24px; letter-spacing: -0.5px; }
    .hero-desc  { color: rgba(255,255,255,0.8); font-size: 16px; line-height: 1.7; margin-bottom: 40px; max-width: 450px; }

    /* Glassmorphism Feature Card */
    .glass-card {
        background: rgba(255, 255, 255, 0.06);
        backdrop-filter: blur(16px);
        -webkit-backdrop-filter: blur(16px);
        border: 1px solid rgba(255, 255, 255, 0.1);
        border-radius: 24px;
        padding: 32px;
        max-width: 480px;
        box-shadow: 0 10px 40px rgba(0, 0, 0, 0.1);
    }
    
    .feature-list { display: flex; flex-direction: column; gap: 18px; }
    .feature-item { display: flex; align-items: center; gap: 16px; }
    .feature-check { 
        width: 32px; height: 32px; border-radius: 50%; 
        background: rgba(255,255,255,0.15); 
        display: flex; align-items: center; justify-content: center; flex-shrink: 0; 
        border: 1px solid rgba(255,255,255,0.2);
    }
    .feature-label { color: rgba(255,255,255,0.95); font-size: 15px; font-weight: 600; }
    
    .left-footer { color: rgba(255,255,255,0.4); font-size: 13px; position: relative; z-index: 1; margin-top: auto; font-weight: 500;}

    /* ===== RIGHT PANEL ===== */
    .right-panel { width: 45%; min-height: 100vh; background: #fff; display: flex; align-items: center; justify-content: center; padding: 40px; }
    .form-card   { width: 100%; max-width: 420px; }
    .form-header { text-align: center; margin-bottom: 28px; }
    .form-title    { font-size: 26px; font-weight: 800; color: #111827; margin-bottom: 8px; letter-spacing: -0.3px; }
    .form-subtitle { font-size: 14px; color: #6b7280; font-weight: 400; }

    /* ===== ROLE TOGGLE ===== */
    .role-toggle { display: flex; background: #f3f4f6; border-radius: 10px; padding: 4px; gap: 4px; margin-bottom: 24px; }
    .toggle-btn {
        flex: 1; padding: 10px 0; border-radius: 8px; font-size: 14px; font-weight: 600;
        cursor: pointer; border: none; background: transparent; color: #6b7280;
        transition: all 0.25s ease; font-family: 'Plus Jakarta Sans', sans-serif;
    }
    .toggle-btn:hover:not(.active) { color: #374151; }

    /* Aktif USER → biru */
    .toggle-btn.active-user  { background: #2563eb; color: #fff; box-shadow: 0 2px 8px rgba(37,99,235,0.35); }
    /* Aktif ADMIN → orange */
    .toggle-btn.active-admin { background: #ea580c; color: #fff; box-shadow: 0 2px 8px rgba(234,88,12,0.35); }

    /* ===== FORM ===== */
    .field-group  { margin-bottom: 18px; }
    .field-label  { display: block; font-size: 13px; font-weight: 600; color: #374151; margin-bottom: 8px; }
    .form-input {
        width: 100%; padding: 12px 14px; border: 1.5px solid #e5e7eb; border-radius: 10px;
        font-size: 14px; color: #111827; font-family: 'Plus Jakarta Sans', sans-serif;
        outline: none; background: #fff; transition: border-color 0.2s, box-shadow 0.2s;
    }
    .form-input::placeholder { color: #9ca3af; }

    /* Focus warna dinamis via CSS variable */
    :root { --accent: #2563eb; --accent-shadow: rgba(37,99,235,0.12); }
    .form-input:focus {
        border-color: var(--accent);
        box-shadow: 0 0 0 3px var(--accent-shadow);
    }

    .password-wrapper { position: relative; }
    .password-wrapper .form-input { padding-right: 44px; }
    .eye-btn { position: absolute; right: 14px; top: 50%; transform: translateY(-50%); background: none; border: none; cursor: pointer; color: #9ca3af; display: flex; align-items: center; padding: 0; transition: color 0.15s; }
    .eye-btn:hover { color: #6b7280; }

    .remember-row { display: flex; align-items: center; justify-content: space-between; margin-bottom: 24px; }
    .remember-label { display: flex; align-items: center; gap: 8px; cursor: pointer; }
    .remember-label input[type="checkbox"] { width: 16px; height: 16px; cursor: pointer; accent-color: var(--accent); }
    .remember-text { font-size: 13px; color: #6b7280; font-weight: 500; }
    .forgot-link { font-size: 13px; font-weight: 600; color: var(--accent); text-decoration: none; transition: color 0.2s; }
    .forgot-link:hover { text-decoration: underline; }

    /* ===== TOMBOL MASUK ===== */
    .btn-primary {
        width: 100%; padding: 13px; color: #fff; border: none;
        border-radius: 10px; font-size: 15px; font-weight: 700;
        font-family: 'Plus Jakarta Sans', sans-serif; cursor: pointer;
        transition: background 0.3s, transform 0.1s, box-shadow 0.2s;
        letter-spacing: 0.01em;
        background: var(--accent);
    }
    .btn-primary:hover  { filter: brightness(0.92); transform: translateY(-1px); box-shadow: 0 4px 16px var(--accent-shadow); }
    .btn-primary:active { transform: translateY(0); }

    .divider { display: flex; align-items: center; gap: 12px; color: #9ca3af; font-size: 11px; font-weight: 600; letter-spacing: 0.08em; text-transform: uppercase; margin: 24px 0; }
    .divider::before, .divider::after { content: ''; flex: 1; height: 1px; background: #e5e7eb; }

    .btn-google {
        width: 100%; padding: 12px; background: #fff; color: #374151; border: 1.5px solid #e5e7eb;
        border-radius: 10px; font-size: 14px; font-weight: 600; font-family: 'Plus Jakarta Sans', sans-serif;
        cursor: pointer; display: flex; align-items: center; justify-content: center; gap: 10px;
        transition: border-color 0.2s, box-shadow 0.2s;
    }
    .btn-google:hover { border-color: #d1d5db; box-shadow: 0 2px 10px rgba(0,0,0,0.08); }

    .input-error { font-size: 12px; color: #ef4444; margin-top: 5px; font-weight: 500; }

    @media (max-width: 992px) {
        .left-panel { padding: 32px 40px; }
        .hero-title { font-size: 36px; }
    }
    @media (max-width: 768px) {
        .left-panel { display: none; }
        .right-panel { width: 100%; padding: 32px 24px; }
    }
</style>

{{-- LEFT PANEL --}}
<div class="left-panel mode-user" id="leftPanel">
    {{-- Elemen Dekorasi Background Geometris --}}
    <div class="pattern-overlay"></div>
    <div class="bg-shape shape-1"></div>
    <div class="bg-shape shape-2"></div>
    <div class="bg-shape shape-3"></div>

    {{-- Logo --}}
    <div class="logo-wrap">
        <div class="logo-icon">
             <img src="{{ asset('image/logo-sp.png') }}" alt="Logo SP" class="logo-img">
        </div>
        <div>
            <div class="logo-text-title">SIPINJAM</div>
            <div class="logo-text-sub">Sistem Informasi Peminjaman</div>
        </div>
    </div>

    {{-- Hero Content --}}
    <div class="hero-content">
        <div class="hero-eyebrow">Platform Peminjaman</div>
        <h1 class="hero-title">Kelola Peminjaman<br>dengan Mudah</h1>
        <p class="hero-desc">Platform terpadu untuk peminjaman ruangan dan barang kampus. Proses cepat, transparan, dan efisien untuk seluruh aktivitas akademik.</p>
        
        {{-- Glassmorphism Card untuk Fitur --}}
        <div class="glass-card">
            <div class="feature-list">
                @foreach(['Booking Online & Real-time 24/7', 'Tracking Status Persetujuan Otomatis', 'Riwayat Peminjaman Terintegrasi'] as $item)
                <div class="feature-item">
                    <div class="feature-check">
                        <svg width="14" height="14" viewBox="0 0 14 14" fill="none">
                            <path d="M2.5 7L5.5 10L11.5 4" stroke="white" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"/>
                        </svg>
                    </div>
                    <span class="feature-label">{{ $item }}</span>
                </div>
                @endforeach
            </div>
        </div>
    </div>

    {{-- Footer --}}
    <div class="left-footer">© 2026 SIPINJAM. Sekolah Tinggi Teknologi Bontang.</div>
</div>

{{-- RIGHT PANEL --}}
<div class="right-panel">
    <div class="form-card">
        <div class="form-header">
            <h2 class="form-title">Welcome to SIPINJAM</h2>
            <p class="form-subtitle">Masukkan email dan password Anda untuk melanjutkan</p>
        </div>

        {{-- TOGGLE USER / ADMIN --}}
        <div class="role-toggle">
            <button type="button" class="toggle-btn active-user" id="btn-user"  onclick="setRole('user')">User</button>
            <button type="button" class="toggle-btn"             id="btn-admin" onclick="setRole('admin')">Admin</button>
        </div>

        <x-auth-session-status class="mb-4" :status="session('status')" />

        <form method="POST" action="{{ route('login') }}">
            @csrf
            {{-- field tersembunyi untuk kirim role ke controller --}}
            <input type="hidden" name="role" id="roleInput" value="user">

            <div class="field-group">
                <label for="email" class="field-label">Email</label>
                <input id="email" type="email" name="email" value="{{ old('email') }}"
                       class="form-input" placeholder="nama@sipinjam.ac.id"
                       required autofocus autocomplete="username" />
                @error('email')<div class="input-error">{{ $message }}</div>@enderror
            </div>

            <div class="field-group">
                <label for="password" class="field-label">Password</label>
                <div class="password-wrapper">
                    <input id="password" type="password" name="password"
                           class="form-input" placeholder="Masukkan password"
                           required autocomplete="current-password" />
                    <button type="button" class="eye-btn" onclick="togglePassword()" aria-label="Toggle password">
                        <svg id="eye-open" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/>
                        </svg>
                        <svg id="eye-closed" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="display:none;">
                            <path d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94"/>
                            <path d="M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19"/>
                            <line x1="1" y1="1" x2="23" y2="23"/>
                        </svg>
                    </button>
                </div>
                @error('password')<div class="input-error">{{ $message }}</div>@enderror
            </div>

            <div class="remember-row">
                <label class="remember-label">
                    <input id="remember_me" type="checkbox" name="remember" />
                    <span class="remember-text">Ingat Saya</span>
                </label>
                @if (Route::has('password.request'))
                    <a href="{{ route('password.request') }}" class="forgot-link">Lupa Password?</a>
                @endif
            </div>

            <button type="submit" class="btn-primary" id="btnMasuk">Masuk</button>
        </form>

        <div class="divider">Atau lanjutkan dengan</div>

        <button type="button" class="btn-google">
            <svg width="18" height="18" viewBox="0 0 18 18" fill="none">
                <path d="M17.64 9.2c0-.637-.057-1.251-.164-1.84H9v3.481h4.844c-.209 1.125-.843 2.078-1.796 2.717v2.258h2.908c1.702-1.567 2.684-3.875 2.684-6.615z" fill="#4285F4"/>
                <path d="M9 18c2.43 0 4.467-.806 5.956-2.18l-2.908-2.259c-.806.54-1.837.86-3.048.86-2.344 0-4.328-1.584-5.036-3.711H.957v2.332A8.997 8.997 0 0 0 9 18z" fill="#34A853"/>
                <path d="M3.964 10.71A5.41 5.41 0 0 1 3.682 9c0-.593.102-1.17.282-1.71V4.958H.957A8.996 8.996 0 0 0 0 9c0 1.452.348 2.827.957 4.042l3.007-2.332z" fill="#FBBC05"/>
                <path d="M9 3.58c1.321 0 2.508.454 3.44 1.345l2.582-2.58C13.463.891 11.426 0 9 0A8.997 8.997 0 0 0 .957 4.958L3.964 7.29C4.672 5.163 6.656 3.58 9 3.58z" fill="#EA4335"/>
            </svg>
            Masuk dengan Google
        </button>
    </div>
</div>

<script>
    function setRole(role) {
        const leftPanel  = document.getElementById('leftPanel');
        const btnUser    = document.getElementById('btn-user');
        const btnAdmin   = document.getElementById('btn-admin');
        const roleInput  = document.getElementById('roleInput');
        const emailInput = document.getElementById('email');
        const root       = document.documentElement;

        if (role === 'user') {
            leftPanel.className = 'left-panel mode-user';
            btnUser.className  = 'toggle-btn active-user';
            btnAdmin.className = 'toggle-btn';

            root.style.setProperty('--accent', '#2563eb');
            root.style.setProperty('--accent-shadow', 'rgba(37,99,235,0.12)');

            emailInput.placeholder = 'user@sipinjam.ac.id';
            roleInput.value = 'user';
        } else {
            leftPanel.className = 'left-panel mode-admin';
            btnAdmin.className = 'toggle-btn active-admin';
            btnUser.className  = 'toggle-btn';

            root.style.setProperty('--accent', '#ea580c');
            root.style.setProperty('--accent-shadow', 'rgba(234,88,12,0.12)');

            emailInput.placeholder = 'admin@sipinjam.ac.id';
            roleInput.value = 'admin';
        }
    }

    function togglePassword() {
        const input     = document.getElementById('password');
        const eyeOpen   = document.getElementById('eye-open');
        const eyeClosed = document.getElementById('eye-closed');
        if (input.type === 'password') {
            input.type = 'text';
            eyeOpen.style.display   = 'none';
            eyeClosed.style.display = 'block';
        } else {
            input.type = 'password';
            eyeOpen.style.display   = 'block';
            eyeClosed.style.display = 'none';
        }
    }
</script>
</x-guest-layout>