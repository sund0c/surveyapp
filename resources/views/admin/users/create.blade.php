@extends('layouts.admin')

@section('title', 'Tambah User')

@section('content')
    <h1 class="text-2xl font-bold mb-6">Tambah User</h1>

    <div class="bg-white rounded-lg border p-6 max-w-lg">
        @if ($errors->any())
            <div class="mb-4 rounded-md bg-red-50 border border-red-200 px-4 py-3 text-sm text-red-700">
                @foreach ($errors->all() as $error)
                    <p>{{ $error }}</p>
                @endforeach
            </div>
        @endif

        <form method="POST" action="{{ route('admin.users.store') }}" class="space-y-4">
            @csrf

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Nama</label>
                <input type="text" name="name" value="{{ old('name') }}" required
                       class="w-full rounded-md border-gray-300 shadow-sm border px-3 py-2">
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Email</label>
                <input type="email" name="email" value="{{ old('email') }}" required
                       class="w-full rounded-md border-gray-300 shadow-sm border px-3 py-2">
            </div>

            <div class="rounded-md bg-blue-50 border border-blue-200 px-4 py-3 text-sm text-blue-800">
                Password sementara akan digenerate otomatis dan dikirim ke email di atas.
                User wajib membuat password baru saat login pertama.
            </div>

            <div class="flex gap-3">
                <button type="submit" class="bg-gray-900 text-white px-4 py-2 rounded-md text-sm font-medium hover:bg-gray-800">
                    Simpan & Kirim Email
                </button>
                <a href="{{ route('admin.users.index') }}" class="px-4 py-2 text-sm text-gray-600 hover:underline">
                    Batal
                </a>
            </div>
        </form>
    </div>
@endsection
