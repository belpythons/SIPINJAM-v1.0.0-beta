<x-guest-layout>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">

<style>
    *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
    html, body { height: 100%; font-family: 'Plus Jakarta Sans', sans-serif; -webkit-font-smoothing: antialiased; }
    body { display: flex; align-items: center; justify-content: center; min-height: 100vh; background: #f9fafb; }

    .fp-container {
        width: 100%; max-width: 440px; padding: 0 24px;
    }
    .fp-card {
        background: #ffffff;
        border-radius: 16px;
        box-shadow: 0 1px 3px rgba(0,0,0,0.06), 0 4px 16px rgba(0,0,0,0.04);
        padding: 40px 36px;
    }
    .fp-logo {
        display: flex; justify-content: center; margin-bottom: 28px;
    }
    .fp-logo img {
        width: 64px; height: 64px; object-fit: contain;
    }
    .fp-title {
        text-align: center; font-size: 22px; font-weight: 800; color: #111827;
        margin-bottom: 8px; letter-spacing: -0.3px;
    }
    .fp-desc {
        text-align: center; font-size: 14px; color: #6b7280; line-height: 1.6;
        margin-bottom: 28px; max-width: 360px; margin-left: auto; margin-right: auto;
    }
    .fp-label {
        display: block; font-size: 13px; font-weight: 600; color: #374151; margin-bottom: 8px;
    }
    .fp-input {
        width: 100%; padding: 12px 14px; border: 1.5px solid #e5e7eb; border-radius: 10px;
        font-size: 14px; color: #111827; font-family: 'Plus Jakarta Sans', sans-serif;
        outline: none; background: #fff; transition: border-color 0.2s, box-shadow 0.2s;
    }
    .fp-input::placeholder { color: #9ca3af; }
    .fp-input:focus {
        border-color: #2563eb;
        box-shadow: 0 0 0 3px rgba(37,99,235,0.12);
    }
    .fp-btn {
        width: 100%; padding: 13px; color: #fff; border: none;
        border-radius: 10px; font-size: 15px; font-weight: 700;
        font-family: 'Plus Jakarta Sans', sans-serif; cursor: pointer;
        transition: background 0.3s, transform 0.1s, box-shadow 0.2s;
        background: #2563eb; margin-top: 20px; letter-spacing: 0.01em;
    }
    .fp-btn:hover { background: #1d4ed8; transform: translateY(-1px); box-shadow: 0 4px 16px rgba(37,99,235,0.25); }
    .fp-btn:active { transform: translateY(0); }
    .fp-error { font-size: 12px; color: #ef4444; margin-top: 5px; font-weight: 500; }
    .fp-success {
        background: #ecfdf5; border: 1px solid #a7f3d0; border-radius: 10px;
        padding: 12px 16px; font-size: 13px; color: #065f46; margin-bottom: 20px;
        font-weight: 500;
    }
    .fp-back {
        display: flex; align-items: center; justify-content: center; gap: 6px;
        margin-top: 20px; font-size: 13px; color: #6b7280; text-decoration: none;
        font-weight: 600; transition: color 0.2s;
    }
    .fp-back:hover { color: #2563eb; }
    .fp-back svg { width: 16px; height: 16px; }
</style>

<div class="fp-container">
    <div class="fp-card">
        {{-- Logo --}}
        <div class="fp-logo">
            <img src="{{ asset('image/logo-sp.png') }}" alt="Logo SiPinjam">
        </div>

        <h2 class="fp-title">Lupa Password?</h2>
        <p class="fp-desc">
            Tidak masalah. Masukkan alamat email Anda dan kami akan mengirimkan link untuk mengatur ulang password.
        </p>

        {{-- Session Status --}}
        @if (session('status'))
            <div class="fp-success">
                {{ session('status') }}
            </div>
        @endif

        <form method="POST" action="{{ route('password.email') }}">
            @csrf

            <div>
                <label for="email" class="fp-label">Alamat Email</label>
                <input id="email" type="email" name="email" value="{{ old('email') }}"
                       class="fp-input" placeholder="nama@sipinjam.ac.id"
                       required autofocus autocomplete="username" />
                @error('email')<div class="fp-error">{{ $message }}</div>@enderror
            </div>

            <button type="submit" class="fp-btn">Kirim Link Reset Password</button>
        </form>

        <a href="{{ route('login') }}" class="fp-back">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <path d="M19 12H5"/><path d="M12 19l-7-7 7-7"/>
            </svg>
            Kembali ke halaman Login
        </a>
    </div>
</div>
</x-guest-layout>
