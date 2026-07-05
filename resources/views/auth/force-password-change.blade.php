<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Buat Password Baru</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 min-h-screen flex items-center justify-center px-4">
    <div class="w-full max-w-md bg-white rounded-xl shadow p-8">
        <h1 class="text-lg font-bold text-gray-900 mb-1">Buat Password Baru</h1>
        <p class="text-sm text-gray-500 mb-6">
            Ini login pertama Anda dengan password sementara. Buat password baru untuk melanjutkan.
        </p>

        @if ($errors->any())
            <div class="mb-4 rounded-md bg-red-50 border border-red-200 px-4 py-3 text-sm text-red-700">
                @foreach ($errors->all() as $error)
                    <p>{{ $error }}</p>
                @endforeach
            </div>
        @endif

        <form method="POST" action="{{ route('password.force-change.update') }}" class="space-y-4">
            @csrf

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Password Sementara (dari email)</label>
                <input type="password" name="current_password" required autofocus
                       class="w-full rounded-md border-gray-300 shadow-sm border px-3 py-2">
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Password Baru</label>
                <input type="password" name="password" required minlength="10"
                       class="w-full rounded-md border-gray-300 shadow-sm border px-3 py-2">
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Konfirmasi Password Baru</label>
                <input type="password" name="password_confirmation" required
                       class="w-full rounded-md border-gray-300 shadow-sm border px-3 py-2">
            </div>

            <button type="submit"
                    class="w-full bg-gray-900 text-white rounded-md py-2 font-medium hover:bg-gray-800">
                Simpan & Lanjutkan
            </button>
        </form>
    </div>
</body>
</html>
