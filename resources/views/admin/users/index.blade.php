@extends('layouts.admin')

@section('title', 'Manajemen User')

@section('content')
    <div class="flex items-center justify-between mb-6">
        <h1 class="text-2xl font-bold">Manajemen User</h1>
        <a href="{{ route('admin.users.create') }}"
           class="bg-gray-900 text-white px-4 py-2 rounded-md text-sm font-medium hover:bg-gray-800">
            + Tambah User
        </a>
    </div>

    @if ($errors->has('user'))
        <div class="mb-4 rounded-md bg-red-50 border border-red-200 px-4 py-3 text-sm text-red-700">
            {{ $errors->first('user') }}
        </div>
    @endif

    <div class="bg-white rounded-lg border overflow-hidden">
        <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead class="bg-gray-50 text-left text-gray-500">
                <tr>
                    <th class="px-4 py-3">Nama</th>
                    <th class="px-4 py-3">Email</th>
                    <th class="px-4 py-3">2FA</th>
                    <th class="px-4 py-3">Terdaftar</th>
                    <th class="px-4 py-3 text-right">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y">
                @forelse ($users as $user)
                    <tr>
                        <td class="px-4 py-3 font-medium">
                            {{ $user->name }}
                            @if ($user->id === auth()->id())
                                <span class="ml-1 text-xs text-gray-400">(Anda)</span>
                            @endif
                        </td>
                        <td class="px-4 py-3 text-gray-600">{{ $user->email }}</td>
                        <td class="px-4 py-3">
                            @if ($user->two_factor_enabled)
                                <span class="text-green-700 text-xs">Aktif</span>
                            @else
                                <span class="text-gray-400 text-xs">Nonaktif</span>
                            @endif
                        </td>
                        <td class="px-4 py-3 text-gray-500">{{ $user->created_at->format('d M Y') }}</td>
                        <td class="px-4 py-3 text-right space-x-2">
                            @if ($user->id !== auth()->id())
                                <form method="POST" action="{{ route('admin.users.reset-password', $user) }}" class="inline"
                                      onsubmit="return confirm('Reset password untuk {{ $user->name }}? Password sementara baru akan dikirim ke {{ $user->email }}, password lama langsung tidak berlaku.');">
                                    @csrf
                                    <button type="submit" class="text-amber-700 hover:underline text-sm">Reset Password</button>
                                </form>
                                <form method="POST" action="{{ route('admin.users.destroy', $user) }}" class="inline"
                                      onsubmit="return confirm('Hapus user {{ $user->name }} secara permanen? Tindakan ini tidak bisa dibatalkan.');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-red-600 hover:underline text-sm">Hapus</button>
                                </form>
                            @else
                                <span class="text-gray-300 text-sm">—</span>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="px-4 py-8 text-center text-gray-500">Belum ada user.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
        </div>
    </div>
@endsection
