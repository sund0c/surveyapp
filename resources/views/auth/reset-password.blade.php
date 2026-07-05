<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Reset Password</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 min-h-screen flex items-center justify-center px-4">
    <div class="w-full max-w-md bg-white rounded-xl shadow p-8">
        <h1 class="text-lg font-bold text-gray-900 mb-1">Reset Password</h1>
        <p class="text-sm text-gray-500 mb-6">Masukkan password baru Anda.</p>

        @if ($errors->any())
            <div class="mb-4 rounded-md bg-red-50 border border-red-200 px-4 py-3 text-sm text-red-700">
                @foreach ($errors->all() as $error)
                    <p>{{ $error }}</p>
                @endforeach
            </div>
        @endif

        <form method="POST" action="{{ route('password.update') }}" class="space-y-4">
            @csrf
            <input type="hidden" name="token" value="{{ $token }}">

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Email</label>
                <input type="email" name="email" value="{{ old('email', $email) }}" required
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
                Reset Password
            </button>
        </form>
    </div>
</body>
</html>
