<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
</head>
<body style="font-family: Arial, sans-serif; color: #1f2937; max-width: 480px; margin: 0 auto; padding: 24px;">
    <h2 style="margin-bottom: 4px;">
        {{ $isNewAccount ? 'Akun Anda Telah Dibuat' : 'Password Sementara' }}
    </h2>
    <p style="color: #6b7280; font-size: 14px; margin-top: 0;">
        Dashboard Survei Kepuasan Layanan Persandian — Dinas Kominfos Provinsi Bali
    </p>

    <p>Halo {{ $recipientName }},</p>

    @if ($isNewAccount)
        <p>Akun Anda telah dibuat oleh administrator. Berikut password sementara Anda:</p>
    @else
        <p>Password akun Anda telah direset oleh administrator. Berikut password sementara yang baru:</p>
    @endif

    <div style="background: #f3f4f6; border-radius: 8px; padding: 16px; text-align: center; margin: 20px 0;">
        <span style="font-family: monospace; font-size: 18px; letter-spacing: 1px;">{{ $temporaryPassword }}</span>
    </div>

    <p style="font-size: 14px;">
        <strong>Password ini bersifat sementara.</strong> Anda akan diminta membuat password
        baru segera setelah login pertama kali - password sementara ini tidak bisa dipakai
        untuk login berikutnya.
    </p>

    <p style="margin-top: 24px;">
        <a href="{{ $loginUrl }}" style="background: #111827; color: #fff; padding: 10px 20px; border-radius: 6px; text-decoration: none; font-size: 14px;">
            Login Sekarang
        </a>
    </p>

    <p style="font-size: 12px; color: #9ca3af; margin-top: 32px; border-top: 1px solid #e5e7eb; padding-top: 16px;">
        Kalau Anda tidak meminta atau tidak mengenali aktivitas ini, segera hubungi administrator sistem.
        Jangan teruskan email ini ke siapa pun.
    </p>
</body>
</html>
