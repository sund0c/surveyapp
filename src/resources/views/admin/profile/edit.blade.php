@extends('layouts.admin')

@section('title', 'Profil Saya')

@section('content')
    <h1 class="text-2xl font-bold mb-6">Profil Saya</h1>

    @if (session('recovery_codes'))
        <div class="mb-6 bg-amber-50 border border-amber-300 rounded-lg p-5">
            <p class="font-semibold text-amber-900 mb-2">⚠️ Simpan Recovery Codes Ini Sekarang</p>
            <p class="text-sm text-amber-800 mb-3">
                Kalau HP/authenticator hilang, ini satu-satunya cara masuk kembali. Simpan di password manager,
                jangan screenshot dan kirim lewat chat.
            </p>
            <div class="grid grid-cols-2 gap-2 font-mono text-sm bg-white rounded p-3">
                @foreach (session('recovery_codes') as $code)
                    <div>{{ $code }}</div>
                @endforeach
            </div>
        </div>
    @endif

    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        {{-- Ganti Password --}}
        <div class="bg-white rounded-lg border p-6">
            <h2 class="font-semibold mb-4">Ganti Password</h2>

            @if ($errors->has('current_password') || $errors->has('password'))
                <div class="mb-4 rounded-md bg-red-50 border border-red-200 px-4 py-3 text-sm text-red-700">
                    @foreach ($errors->all() as $error)
                        <p>{{ $error }}</p>
                    @endforeach
                </div>
            @endif

            <form method="POST" action="{{ route('admin.profile.password') }}" class="space-y-3">
                @csrf
                @method('PUT')

                <div>
                    <label class="block text-sm text-gray-700 mb-1">Password Saat Ini</label>
                    <input type="password" name="current_password" required
                           class="w-full rounded-md border-gray-300 border px-3 py-2 text-sm">
                </div>
                <div>
                    <label class="block text-sm text-gray-700 mb-1">Password Baru</label>
                    <input type="password" name="password" required minlength="10"
                           class="w-full rounded-md border-gray-300 border px-3 py-2 text-sm">
                </div>
                <div>
                    <label class="block text-sm text-gray-700 mb-1">Konfirmasi Password Baru</label>
                    <input type="password" name="password_confirmation" required
                           class="w-full rounded-md border-gray-300 border px-3 py-2 text-sm">
                </div>

                <button type="submit" class="bg-gray-900 text-white px-4 py-2 rounded-md text-sm font-medium hover:bg-gray-800">
                    Update Password
                </button>
            </form>
        </div>

        {{-- 2FA --}}
        <div class="bg-white rounded-lg border p-6">
            <h2 class="font-semibold mb-4">Autentikasi Dua Faktor (2FA)</h2>

            @if ($user->two_factor_enabled)
                <p class="text-sm text-green-700 mb-4">✓ 2FA aktif di akun Anda.</p>

                @if ($errors->has('current_password'))
                    <div class="mb-4 rounded-md bg-red-50 border border-red-200 px-4 py-3 text-sm text-red-700">
                        {{ $errors->first('current_password') }}
                    </div>
                @endif

                <form method="POST" action="{{ route('admin.profile.2fa.disable') }}"
                      onsubmit="return confirm('Nonaktifkan 2FA? Akun akan hanya dilindungi password.');" class="space-y-3">
                    @csrf
                    @method('DELETE')
                    <div>
                        <label class="block text-sm text-gray-700 mb-1">Konfirmasi Password</label>
                        <input type="password" name="current_password" required
                               class="w-full rounded-md border-gray-300 border px-3 py-2 text-sm">
                    </div>
                    <button type="submit" class="text-red-600 text-sm hover:underline">Nonaktifkan 2FA</button>
                </form>
            @else
                <p class="text-sm text-gray-500 mb-4">2FA belum aktif. Sangat disarankan untuk mengaktifkan.</p>
                <a href="{{ route('admin.profile.2fa.setup') }}"
                   class="inline-block bg-gray-900 text-white px-4 py-2 rounded-md text-sm font-medium hover:bg-gray-800">
                    Aktifkan 2FA
                </a>
            @endif
        </div>
    </div>
@endsection
