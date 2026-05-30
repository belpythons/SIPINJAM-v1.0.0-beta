<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reset Password — SiPinjam</title>
</head>
<body style="margin: 0; padding: 0; background-color: #f3f4f6; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;">
    <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="background-color: #f3f4f6; padding: 40px 16px;">
        <tr>
            <td align="center">
                <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="max-width: 520px; background-color: #ffffff; border: 4px solid #000000; box-shadow: 8px 8px 0px #000000;">
                    {{-- Header --}}
                    <tr>
                        <td style="background-color: #2563eb; border-bottom: 4px solid #000000; padding: 32px 32px; text-align: center;">
                            <div style="width: 56px; height: 56px; background-color: #ffffff; border: 3px solid #000000; box-shadow: 4px 4px 0px rgba(0,0,0,0.3); margin: 0 auto 16px; line-height: 56px; text-align: center;">
                                <img src="{{ asset('image/logo-sp.png') }}" alt="SiPinjam" width="36" height="36" style="vertical-align: middle;">
                            </div>
                            <h1 style="margin: 0; color: #ffffff; font-size: 22px; font-weight: 900; letter-spacing: 0.5px; text-shadow: 2px 2px 0px rgba(0,0,0,0.3);">
                                Reset Password
                            </h1>
                            <p style="margin: 8px 0 0; color: rgba(255,255,255,0.9); font-size: 13px; font-weight: 500;">
                                SiPinjam — Sekolah Tinggi Teknologi Bontang
                            </p>
                        </td>
                    </tr>

                    {{-- Body --}}
                    <tr>
                        <td style="padding: 32px;">
                            <p style="margin: 0 0 16px; color: #111827; font-size: 15px; line-height: 1.6;">
                                Halo <strong style="font-weight: 800;">{{ $user->name }}</strong>,
                            </p>
                            <p style="margin: 0 0 24px; color: #4b5563; font-size: 14px; line-height: 1.7;">
                                Kami menerima permintaan untuk mereset password akun SiPinjam Anda. Klik tombol di bawah ini untuk membuat password baru:
                            </p>

                            {{-- CTA Button --}}
                            <table role="presentation" width="100%" cellpadding="0" cellspacing="0">
                                <tr>
                                    <td align="center" style="padding: 8px 0 24px;">
                                        <a href="{{ $resetUrl }}"
                                           target="_blank"
                                           style="display: inline-block; padding: 14px 32px; background-color: #2563eb; color: #ffffff; font-size: 14px; font-weight: 900; text-decoration: none; text-transform: uppercase; letter-spacing: 1px; border: 3px solid #000000; box-shadow: 4px 4px 0px #000000;">
                                            RESET PASSWORD SEKARANG
                                        </a>
                                    </td>
                                </tr>
                            </table>

                            {{-- URL Fallback --}}
                            <div style="background-color: #f9fafb; border: 2px solid #e5e7eb; padding: 16px; margin-bottom: 24px;">
                                <p style="margin: 0 0 8px; color: #6b7280; font-size: 12px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.5px;">
                                    Jika tombol tidak berfungsi, salin link berikut:
                                </p>
                                <p style="margin: 0; word-break: break-all; color: #2563eb; font-size: 12px; font-weight: 600;">
                                    {{ $resetUrl }}
                                </p>
                            </div>

                            {{-- Security Notice --}}
                            <div style="background-color: #fef3c7; border: 3px solid #000000; padding: 16px; box-shadow: 3px 3px 0px #000000;">
                                <p style="margin: 0; color: #92400e; font-size: 13px; font-weight: 700;">
                                    ⚠️ Link ini akan kadaluarsa dalam 60 menit.
                                </p>
                                <p style="margin: 6px 0 0; color: #92400e; font-size: 12px; font-weight: 500;">
                                    Jika Anda tidak meminta reset password, abaikan email ini. Akun Anda tetap aman.
                                </p>
                            </div>
                        </td>
                    </tr>

                    {{-- Footer --}}
                    <tr>
                        <td style="background-color: #111827; border-top: 4px solid #000000; padding: 24px 32px; text-align: center;">
                            <p style="margin: 0 0 4px; color: rgba(255,255,255,0.8); font-size: 12px; font-weight: 700;">
                                &copy; {{ date('Y') }} SiPinjam — STITEK Bontang
                            </p>
                            <p style="margin: 0; color: rgba(255,255,255,0.5); font-size: 11px; font-weight: 500;">
                                The Knowledgeable and Virtue Campus
                            </p>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>
</html>
