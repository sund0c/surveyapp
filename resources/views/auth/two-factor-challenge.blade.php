<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Verifikasi 2FA</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 min-h-screen flex items-center justify-center">
    <div class="w-full max-w-sm bg-white rounded-xl shadow p-8">
        <h1 class="text-lg font-bold text-gray-900 mb-1">Verifikasi Dua Langkah</h1>
        <p class="text-sm text-gray-500 mb-6">Masukkan kode dari aplikasi authenticator Anda.</p>

        @if ($errors->any())
            <div class="mb-4 rounded-md bg-red-50 border border-red-200 px-4 py-3 text-sm text-red-700">
                @foreach ($errors->all() as $error)
                    <p>{{ $error }}</p>
                @endforeach
            </div>
        @endif

        <form method="POST" action="{{ route('login.2fa.verify') }}" class="space-y-4">
            @csrf
            <input type="text" name="code" required autofocus inputmode="numeric" placeholder="000000"
                   class="w-full text-center text-2xl tracking-widest rounded-md border-gray-300 shadow-sm border px-3 py-2">

            <button type="submit"
                    class="w-full bg-gray-900 text-white rounded-md py-2 font-medium hover:bg-gray-800">
                Verifikasi
            </button>
        </form>

        <p class="text-xs text-gray-400 mt-4">
            Kehilangan akses ke authenticator? Masukkan salah satu recovery code Anda di kolom di atas.
        </p>
    </div>
</body>
</html>
