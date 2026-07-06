<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Login - Survei Kepuasan Layanan Persandian</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 min-h-screen flex items-center justify-center">
    <div class="w-full max-w-md bg-white rounded-xl shadow p-8">
        <div class="text-center mb-6">
            {{-- Taruh file logo di public/images/logo-pemprov-bali.png --}}
            <img src="{{ asset('images/logo-pemprov-bali.png') }}" alt="Logo Pemprov Bali"
                 class="h-16 mx-auto mb-4" onerror="this.style.display='none'">

            <h1 class="text-lg font-bold text-gray-900 leading-snug">
                Dashboard Survei Kepuasan Layanan Persandian
            </h1>
            <p class="text-sm text-gray-500 mt-1">
                Dinas Komunikasi, Informatika dan Statistik Provinsi Bali
            </p>
        </div>

        @if ($errors->any())
            <div class="mb-4 rounded-md bg-red-50 border border-red-200 px-4 py-3 text-sm text-red-700">
                @foreach ($errors->all() as $error)
                    <p>{{ $error }}</p>
                @endforeach
            </div>
        @endif

        <form method="POST" action="{{ route('login.attempt') }}" class="space-y-4">
            @csrf

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Email</label>
                <input type="email" name="email" value="{{ old('email') }}" required autofocus
                       class="w-full rounded-md border-gray-300 shadow-sm focus:border-gray-500 focus:ring-gray-500 border px-3 py-2">
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Password</label>
                <input type="password" name="password" required
                       class="w-full rounded-md border-gray-300 shadow-sm focus:border-gray-500 focus:ring-gray-500 border px-3 py-2">
            </div>

            <label class="flex items-center text-sm text-gray-600">
                <input type="checkbox" name="remember" class="rounded border-gray-300 mr-2">
                Ingat saya
            </label>

            <button type="submit"
                    class="w-full bg-gray-900 text-white rounded-md py-2 font-medium hover:bg-gray-800">
                Masuk
            </button>
        </form>
    </div>
</body>
</html>
