@extends('layouts.admin')

@section('title', 'Audit Trail')

@section('content')
    <h1 class="text-2xl font-bold mb-6">Audit Trail</h1>

    <form method="GET" class="bg-white rounded-lg border p-4 mb-6 flex flex-wrap gap-3 items-end">
        <div>
            <label class="block text-xs font-medium text-gray-500 mb-1">Aksi</label>
            <select name="action" class="rounded-md border-gray-300 border px-3 py-2 text-sm">
                <option value="">Semua</option>
                @foreach ($actions as $action)
                    <option value="{{ $action }}" @selected(request('action') === $action)>{{ $action }}</option>
                @endforeach
            </select>
        </div>

        <div>
            <label class="block text-xs font-medium text-gray-500 mb-1">Email Pelaku</label>
            <input type="text" name="actor_email" value="{{ request('actor_email') }}" placeholder="cari email..."
                   class="rounded-md border-gray-300 border px-3 py-2 text-sm">
        </div>

        <div>
            <label class="block text-xs font-medium text-gray-500 mb-1">Dari Tanggal</label>
            <input type="date" name="from" value="{{ request('from') }}" class="rounded-md border-gray-300 border px-3 py-2 text-sm">
        </div>

        <div>
            <label class="block text-xs font-medium text-gray-500 mb-1">Sampai Tanggal</label>
            <input type="date" name="until" value="{{ request('until') }}" class="rounded-md border-gray-300 border px-3 py-2 text-sm">
        </div>

        <button type="submit" class="bg-gray-900 text-white px-4 py-2 rounded-md text-sm font-medium hover:bg-gray-800">
            Filter
        </button>
        <a href="{{ route('admin.audit.index') }}" class="text-sm text-gray-500 hover:underline px-2">Reset</a>
    </form>

    <div class="bg-white rounded-lg border overflow-hidden">
        <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead class="bg-gray-50 text-left text-gray-500">
                <tr>
                    <th class="px-4 py-3">Waktu</th>
                    <th class="px-4 py-3">Pelaku</th>
                    <th class="px-4 py-3">Aksi</th>
                    <th class="px-4 py-3">Deskripsi</th>
                    <th class="px-4 py-3">IP</th>
                </tr>
            </thead>
            <tbody class="divide-y">
                @forelse ($logs as $log)
                    <tr>
                        <td class="px-4 py-3 whitespace-nowrap text-gray-500">{{ $log->created_at->format('d M Y H:i:s') }}</td>
                        <td class="px-4 py-3">
                            {{ $log->actor_name ?? 'Sistem' }}
                            @if ($log->actor_email)
                                <div class="text-xs text-gray-400">{{ $log->actor_email }}</div>
                            @endif
                        </td>
                        <td class="px-4 py-3">
                            <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-mono bg-gray-100 text-gray-700">
                                {{ $log->action }}
                            </span>
                        </td>
                        <td class="px-4 py-3 text-gray-700">{{ $log->description }}</td>
                        <td class="px-4 py-3 text-gray-400 whitespace-nowrap">{{ $log->ip_address }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="px-4 py-8 text-center text-gray-500">Belum ada log.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
        </div>
    </div>

    <div class="mt-4">
        {{ $logs->links() }}
    </div>
@endsection
