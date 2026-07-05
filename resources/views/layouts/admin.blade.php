<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'Survei Kepuasan Layanan Persandian')</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
</head>
<body class="bg-gray-50 text-gray-900" x-data="{ sidebarOpen: false }">
    <div class="flex min-h-screen">

        {{-- Overlay - mobile only, click to close --}}
        <div x-show="sidebarOpen"
             x-transition.opacity
             @click="sidebarOpen = false"
             class="fixed inset-0 bg-black/40 z-30 md:hidden"
             style="display: none;"></div>

        {{-- Sidebar --}}
        <aside
            :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full'"
            class="fixed inset-y-0 left-0 z-40 w-64 bg-gray-900 text-gray-200 flex flex-col
                   transform transition-transform duration-200 ease-in-out
                   md:relative md:translate-x-0 md:z-auto">

            <div class="p-5 border-b border-gray-800 flex items-start justify-between">
                <div>
                    <p class="font-semibold text-sm leading-tight">Survei Kepuasan</p>
                    <p class="text-xs text-gray-400">Layanan Persandian</p>
                </div>
                {{-- Close button - mobile only --}}
                <button @click="sidebarOpen = false" class="md:hidden text-gray-400 hover:text-white" aria-label="Tutup menu">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd" />
                    </svg>
                </button>
            </div>

            <nav class="flex-1 p-3 space-y-1 text-sm overflow-y-auto">
                <a href="{{ route('admin.dashboard') }}"
                   class="block px-3 py-2 rounded {{ request()->routeIs('admin.dashboard') ? 'bg-gray-800 text-white' : 'hover:bg-gray-800' }}">
                    Dashboard
                </a>
                <a href="{{ route('admin.applications.index') }}"
                   class="block px-3 py-2 rounded {{ request()->routeIs('admin.applications.*') ? 'bg-gray-800 text-white' : 'hover:bg-gray-800' }}">
                    Aplikasi Terdaftar
                </a>
                <a href="{{ route('admin.ratings.index') }}"
                   class="block px-3 py-2 rounded {{ request()->routeIs('admin.ratings.*') ? 'bg-gray-800 text-white' : 'hover:bg-gray-800' }}">
                    Rating & Saran
                </a>
            </nav>

            <div class="p-3 border-t border-gray-800 text-sm">
                <a href="{{ route('admin.profile.edit') }}" class="block px-3 py-2 rounded hover:bg-gray-800 truncate">
                    {{ auth()->user()->name }}
                </a>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="w-full text-left px-3 py-2 rounded hover:bg-gray-800 text-red-300">
                        Sign out
                    </button>
                </form>
            </div>
        </aside>

        {{-- Main content --}}
        <div class="flex-1 min-w-0 flex flex-col">
            {{-- Mobile top bar with hamburger --}}
            <header class="md:hidden sticky top-0 z-20 bg-white border-b px-4 py-3 flex items-center gap-3">
                <button @click="sidebarOpen = true" aria-label="Buka menu">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-gray-700" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M3 5a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zm0 5a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zm0 5a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1z" clip-rule="evenodd" />
                    </svg>
                </button>
                <span class="font-semibold text-sm text-gray-800">Survei Kepuasan Persandian</span>
            </header>

            <main class="flex-1">
                <div class="max-w-6xl mx-auto p-4 sm:p-6 md:p-8">
                    @if (session('status'))
                        <div class="mb-4 rounded-md bg-green-50 border border-green-200 px-4 py-3 text-sm text-green-800">
                            {{ session('status') }}
                        </div>
                    @endif

                    @if (session('new_credentials'))
                        <div class="mb-4 rounded-md bg-amber-50 border border-amber-300 px-4 py-3 text-sm text-amber-900">
                            <p class="font-semibold mb-1">⚠️ Simpan credentials ini sekarang — tidak akan ditampilkan lagi:</p>
                            <p class="font-mono text-xs break-all">API Key: {{ session('new_credentials')['api_key'] }}</p>
                            <p class="font-mono text-xs break-all">API Secret: {{ session('new_credentials')['api_secret'] }}</p>
                        </div>
                    @endif

                    @yield('content')
                </div>
            </main>
        </div>
    </div>
</body>
</html>
