@extends('layouts.admin')

@section('title', 'Dashboard')

@section('content')
    <h1 class="text-2xl font-bold mb-6">Dashboard</h1>

    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mb-8">
        <div class="bg-white rounded-lg border p-5">
            <p class="text-sm text-gray-500">Aplikasi Aktif</p>
            <p class="text-3xl font-bold mt-1">{{ $activeApps }}</p>
        </div>
        <div class="bg-white rounded-lg border p-5">
            <p class="text-sm text-gray-500">Total Rating Masuk</p>
            <p class="text-3xl font-bold mt-1">{{ $totalRatings }}</p>
            <p class="text-xs text-gray-400 mt-1">{{ $last7Days }} dalam 7 hari terakhir</p>
        </div>
        <div class="bg-white rounded-lg border p-5">
            <p class="text-sm text-gray-500">Rata-rata Rating</p>
            <p class="text-3xl font-bold mt-1">{{ $avgRating }} / 5</p>
        </div>
    </div>

    <h2 class="text-lg font-semibold mb-3">Rating Terbaru</h2>
    <div class="bg-white rounded-lg border divide-y">
        @forelse ($recentRatings as $rating)
            <div class="p-4 flex items-start justify-between">
                <div>
                    <p class="text-sm font-medium">{{ $rating->application->name ?? '—' }}</p>
                    <p class="text-sm text-gray-500">{{ $rating->comment ?: 'Tanpa komentar' }}</p>
                </div>
                <div class="text-right">
                    <p class="text-amber-500">{{ str_repeat('★', $rating->rating) }}{{ str_repeat('☆', 5 - $rating->rating) }}</p>
                    <p class="text-xs text-gray-400">{{ $rating->created_at->format('d M Y H:i') }}</p>
                </div>
            </div>
        @empty
            <p class="p-4 text-sm text-gray-500">Belum ada rating masuk.</p>
        @endforelse
    </div>
@endsection
