@extends('layouts.admin')

@section('title', 'Rating & Saran')

@section('content')
    <h1 class="text-2xl font-bold mb-6">Rating & Saran</h1>

    <form method="GET" class="bg-white rounded-lg border p-4 mb-6 flex flex-wrap gap-3 items-end">
        <div>
            <label class="block text-xs font-medium text-gray-500 mb-1">Aplikasi</label>
            <select name="application_id" class="rounded-md border-gray-300 border px-3 py-2 text-sm">
                <option value="">Semua</option>
                @foreach ($applications as $app)
                    <option value="{{ $app->id }}" @selected(request('application_id') == $app->id)>{{ $app->name }}</option>
                @endforeach
            </select>
        </div>

        <div>
            <label class="block text-xs font-medium text-gray-500 mb-1">Rating</label>
            <select name="rating" class="rounded-md border-gray-300 border px-3 py-2 text-sm">
                <option value="">Semua</option>
                @foreach ([1,2,3,4,5] as $r)
                    <option value="{{ $r }}" @selected(request('rating') == $r)>{{ $r }} ★</option>
                @endforeach
            </select>
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
        <a href="{{ route('admin.ratings.index') }}" class="text-sm text-gray-500 hover:underline px-2">Reset</a>
    </form>

    <div class="bg-white rounded-lg border overflow-hidden">
        <table class="w-full text-sm">
            <thead class="bg-gray-50 text-left text-gray-500">
                <tr>
                    <th class="px-4 py-3">Aplikasi</th>
                    <th class="px-4 py-3">Rating</th>
                    <th class="px-4 py-3">Komentar/Saran</th>
                    <th class="px-4 py-3">Waktu</th>
                </tr>
            </thead>
            <tbody class="divide-y">
                @forelse ($ratings as $rating)
                    <tr>
                        <td class="px-4 py-3 font-medium">{{ $rating->application->name ?? '—' }}</td>
                        <td class="px-4 py-3 text-amber-500">
                            {{ str_repeat('★', $rating->rating) }}{{ str_repeat('☆', 5 - $rating->rating) }}
                        </td>
                        <td class="px-4 py-3 text-gray-700">{{ $rating->comment ?: '—' }}</td>
                        <td class="px-4 py-3 text-gray-500 whitespace-nowrap">{{ $rating->created_at->format('d M Y H:i') }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" class="px-4 py-8 text-center text-gray-500">Tidak ada data.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-4">
        {{ $ratings->links() }}
    </div>
@endsection
