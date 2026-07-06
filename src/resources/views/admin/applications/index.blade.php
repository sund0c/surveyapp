@extends('layouts.admin')

@section('title', 'Aplikasi Terdaftar')

@section('content')
    <div class="flex items-center justify-between mb-6">
        <h1 class="text-2xl font-bold">Aplikasi Terdaftar</h1>
        <a href="{{ route('admin.applications.create') }}"
           class="bg-gray-900 text-white px-4 py-2 rounded-md text-sm font-medium hover:bg-gray-800">
            + Tambah Aplikasi
        </a>
    </div>

    <div class="bg-white rounded-lg border overflow-hidden">
        <table class="w-full text-sm">
            <thead class="bg-gray-50 text-left text-gray-500">
                <tr>
                    <th class="px-4 py-3">Nama</th>
                    <th class="px-4 py-3">Status</th>
                    <th class="px-4 py-3">Total Rating</th>
                    <th class="px-4 py-3">Terakhir Digunakan</th>
                    <th class="px-4 py-3 text-right">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y">
                @forelse ($applications as $app)
                    <tr>
                        <td class="px-4 py-3 font-medium">{{ $app->name }}</td>
                        <td class="px-4 py-3">
                            @if ($app->is_active)
                                <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-green-100 text-green-800">Aktif</span>
                            @else
                                <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-gray-100 text-gray-600">Nonaktif</span>
                            @endif
                        </td>
                        <td class="px-4 py-3">{{ $app->ratings_count }}</td>
                        <td class="px-4 py-3 text-gray-500">
                            {{ $app->last_used_at?->format('d M Y H:i') ?? 'Belum pernah' }}
                        </td>
                        <td class="px-4 py-3 text-right space-x-2">
                            <form method="POST" action="{{ route('admin.applications.regenerate', $app) }}" class="inline"
                                  onsubmit="return confirm('Regenerate credentials untuk {{ $app->name }}? Secret lama langsung tidak berlaku, aplikasi klien perlu update .env segera.');">
                                @csrf
                                <button type="submit" class="text-amber-700 hover:underline text-sm">Regenerate</button>
                            </form>

                            <form method="POST" action="{{ route('admin.applications.toggle-active', $app) }}" class="inline"
                                  onsubmit="return confirm('{{ $app->is_active ? 'Nonaktifkan' : 'Aktifkan' }} {{ $app->name }}?{{ $app->is_active ? ' API akan menolak request baru dari aplikasi ini. Data rating yang sudah ada tetap tersimpan dan bisa diaktifkan lagi kapan saja.' : '' }}');">
                                @csrf
                                <button type="submit" class="{{ $app->is_active ? 'text-red-600' : 'text-green-700' }} hover:underline text-sm">
                                    {{ $app->is_active ? 'Nonaktifkan' : 'Aktifkan' }}
                                </button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="px-4 py-8 text-center text-gray-500">Belum ada aplikasi terdaftar.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
@endsection
