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
        display: flex; flex-direction: column; padding: 40px 52px;
        position: relative; overflow: hidden;
        transition: background 0.5s ease;
    }

    /* Mode USER → Biru */
    .left-panel.mode-user {
        background: linear-gradient(145deg, #1a3799 0%, #1e40af 40%, #2563eb 100%);
    }

    /* Mode ADMIN → Orange */
    .left-panel.mode-admin {
        background: linear-gradient(145deg, #b84500 0%, #d95e00 40%, #f97316 100%);
    }

    .left-panel::before {
        content: ''; position: absolute; top: -140px; right: -140px;
        width: 420px; height: 420px; background: rgba(255,255,255,0.06);
        border-radius: 50%; pointer-events: none;
    }
    .left-panel::after {
        content: ''; position: absolute; bottom: -100px; left: -100px;
        width: 320px; height: 320px; background: rgba(255,255,255,0.04);
        border-radius: 50%; pointer-events: none;
    }

    .logo-wrap { display: flex; align-items: center; gap: 14px; position: relative; z-index: 1; }
    .logo-icon {
        width: 48px; height: 48px; border-radius: 12px;
        display: flex; align-items: center; justify-content: center;
        font-weight: 800; font-size: 16px; color: #fff; flex-shrink: 0;
        transition: background 0.5s ease;
    }

    /* Logo icon warna kebalikan dari panel */
    .left-panel.mode-user .logo-icon    { background: linear-gradient(135deg, #f97316 0%, #ea580c 100%); }
    .left-panel.mode-admin .logo-icon   { background: rgba(255,255,255,0.25); }

    .logo-text-title { color: #fff; font-weight: 800; font-size: 18px; letter-spacing: 0.03em; line-height: 1.2; }
    .logo-text-sub   { color: rgba(255,255,255,0.6); font-size: 12px; font-weight: 500; margin-top: 2px; }

    .hero-content { flex: 1; display: flex; flex-direction: column; justify-content: center; padding: 40px 0; position: relative; z-index: 1; }
    .hero-eyebrow {
        color: rgba(255,255,255,0.65); font-size: 12px; font-weight: 700;
        letter-spacing: 0.1em; text-transform: uppercase; margin-bottom: 18px;
        display: flex; align-items: center; gap: 10px;
    }
    .hero-eyebrow::before { content: ''; display: inline-block; width: 24px; height: 2px; background: rgba(255,255,255,0.4); border-radius: 2px; }
    .hero-title { color: #fff; font-size: 40px; font-weight: 800; line-height: 1.18; margin-bottom: 20px; letter-spacing: -0.5px; }
    .hero-desc  { color: rgba(255,255,255,0.75); font-size: 15px; line-height: 1.75; margin-bottom: 40px; max-width: 380px; }

    .feature-list { display: flex; flex-direction: column; gap: 14px; }
    .feature-item { display: flex; align-items: center; gap: 14px; }
    .feature-check { width: 30px; height: 30px; border-radius: 50%; background: rgba(255,255,255,0.15); display: flex; align-items: center; justify-content: center; flex-shrink: 0; }
    .feature-label { color: rgba(255,255,255,0.88); font-size: 14px; font-weight: 500; }
    .left-footer { color: rgba(255,255,255,0.35); font-size: 12px; position: relative; z-index: 1; }

    /* ===== RIGHT PANEL ===== */
    .right-panel { width: 45%; min-height: 100vh; background: #fff; display: flex; align-items: center; justify-content: center; padding: 40px; }
    .form-card   { width: 100%; max-width: 420px; }
    .form-header { text-align: center; margin-bottom: 28px; }
    .form-title    { font-size: 26px; font-weight: 800; color: #111827; margin-bottom: 8px; letter-spacing: -0.3px; }
    .form-subtitle { font-size: 14px; color: #6b7280; font-weight: 400; }

    /* ===== ROLE TOGGLE ===== */
    .role-toggle { display: flex; background: #f3f4f6; border-radius: 10px; padding: 4px; gap: 4px; margin-bottom: 24px; }
    .toggle-btn {
        flex: 1; padding: 9px 0; border-radius: 8px; font-size: 14px; font-weight: 600;
        cursor: pointer; border: none; background: transparent; color: #6b7280;
        transition: all 0.25s ease; font-family: 'Plus Jakarta Sans', sans-serif;
    }
    .toggle-btn:hover:not(.active) { color: #374151; }

    /* Aktif USER → biru */
    .toggle-btn.active-user  { background: #2563eb; color: #fff; box-shadow: 0 2px 8px rgba(37,99,235,0.35); }
    /* Aktif ADMIN → orange */
    .toggle-btn.active-admin { background: #f97316; color: #fff; box-shadow: 0 2px 8px rgba(249,115,22,0.35); }

    /* ===== FORM ===== */
    .field-group  { margin-bottom: 16px; }
    .field-label  { display: block; font-size: 13px; font-weight: 600; color: #374151; margin-bottom: 7px; }
    .form-input {
        width: 100%; padding: 11px 14px; border: 1.5px solid #e5e7eb; border-radius: 10px;
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
    .eye-btn { position: absolute; right: 13px; top: 50%; transform: translateY(-50%); background: none; border: none; cursor: pointer; color: #9ca3af; display: flex; align-items: center; padding: 0; transition: color 0.15s; }
    .eye-btn:hover { color: #6b7280; }

    .remember-row { display: flex; align-items: center; justify-content: space-between; margin-bottom: 20px; }
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

    .divider { display: flex; align-items: center; gap: 12px; color: #9ca3af; font-size: 11px; font-weight: 600; letter-spacing: 0.08em; text-transform: uppercase; margin: 20px 0; }
    .divider::before, .divider::after { content: ''; flex: 1; height: 1px; background: #e5e7eb; }

    .btn-google {
        width: 100%; padding: 12px; background: #fff; color: #374151; border: 1.5px solid #e5e7eb;
        border-radius: 10px; font-size: 14px; font-weight: 600; font-family: 'Plus Jakarta Sans', sans-serif;
        cursor: pointer; display: flex; align-items: center; justify-content: center; gap: 10px;
        transition: border-color 0.2s, box-shadow 0.2s;
    }
    .btn-google:hover { border-color: #d1d5db; box-shadow: 0 2px 10px rgba(0,0,0,0.08); }

    .input-error { font-size: 12px; color: #ef4444; margin-top: 5px; font-weight: 500; }

    @media (max-width: 768px) {
        .left-panel { display: none; }
        .right-panel { width: 100%; padding: 32px 24px; }
    }
</style>

{{-- LEFT PANEL --}}
<div class="left-panel mode-user" id="leftPanel">
    <div class="logo-wrap">
        <div class="logo-icon">SP</div>
        <div>
            <div class="logo-text-title">SIPINJAM</div>
            <div class="logo-text-sub">Sistem Informasi Peminjaman</div>
        </div>
    </div>
    <div class="hero-content">
        <div class="hero-eyebrow">Platform Peminjaman</div>
        <h1 class="hero-title">Kelola Peminjaman<br>dengan Mudah</h1>
        <p class="hero-desc">Platform terpadu untuk peminjaman ruangan dan barang kampus. Proses cepat, transparan, dan efisien.</p>
        <div class="feature-list">
            @foreach(['Booking online 24/7', 'Tracking status real-time', 'Riwayat peminjaman lengkap'] as $item)
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
    <div class="left-footer">© 2025 SIPINJAM. Sekolah Tinggi Teknologi Bontang.</div>
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
            // Panel kiri → BIRU
            leftPanel.className = 'left-panel mode-user';

            // Toggle button
            btnUser.className  = 'toggle-btn active-user';
            btnAdmin.className = 'toggle-btn';

            // CSS variable → biru (tombol Masuk, focus input, forgot link)
            root.style.setProperty('--accent', '#2563eb');
            root.style.setProperty('--accent-shadow', 'rgba(37,99,235,0.12)');

            // Placeholder & role
            emailInput.placeholder = 'user@sipinjam.ac.id';
            roleInput.value = 'user';

        } else {
            // Panel kiri → ORANGE
            leftPanel.className = 'left-panel mode-admin';

            // Toggle button
            btnAdmin.className = 'toggle-btn active-admin';
            btnUser.className  = 'toggle-btn';

            // CSS variable → orange
            root.style.setProperty('--accent', '#f97316');
            root.style.setProperty('--accent-shadow', 'rgba(249,115,22,0.12)');

            // Placeholder & role
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