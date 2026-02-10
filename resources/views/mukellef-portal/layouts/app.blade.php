<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="theme-color" content="#059669">
    <link rel="manifest" href="/manifest.json">
    <title>@yield('title', 'Mükellef Portal')</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="font-sans antialiased bg-zinc-50 min-h-screen">
    <!-- Top Bar -->
    <header class="bg-emerald-600 text-white sticky top-0 z-30">
        <div class="max-w-lg mx-auto flex items-center justify-between h-14 px-4">
            <h1 class="text-base font-semibold truncate">
                @auth('mukellef')
                    {{ auth('mukellef')->user()->ad }}
                @else
                    Mali Müşavir Portal
                @endauth
            </h1>
            @auth('mukellef')
                <form method="POST" action="{{ route('mukellef-portal.logout') }}">
                    @csrf
                    <button type="submit" class="text-emerald-100 hover:text-white text-sm font-medium">Çıkış</button>
                </form>
            @endauth
        </div>
    </header>

    <!-- Main Content (padding-bottom for bottom nav) -->
    <main class="max-w-lg mx-auto px-4 py-4 pb-20">
        @if(session('success'))
            <div class="mb-4 px-4 py-3 bg-emerald-50 border border-emerald-200 text-emerald-800 text-sm rounded-lg">
                {{ session('success') }}
            </div>
        @endif
        @if(session('error'))
            <div class="mb-4 px-4 py-3 bg-rose-50 border border-rose-200 text-rose-800 text-sm rounded-lg">
                {{ session('error') }}
            </div>
        @endif
        @yield('content')
    </main>

    <!-- Bottom Navigation -->
    @auth('mukellef')
    <nav class="fixed bottom-0 inset-x-0 bg-white border-t border-zinc-200 z-30">
        <div class="max-w-lg mx-auto flex">
            <a href="{{ route('mukellef-portal.belgeler') }}"
               class="flex-1 flex flex-col items-center py-2 {{ request()->routeIs('mukellef-portal.belgeler*') ? 'text-emerald-600' : 'text-zinc-400' }}">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                </svg>
                <span class="text-xs mt-1 font-medium">Belgeler</span>
            </a>
            <a href="{{ route('mukellef-portal.bildirimler') }}"
               class="flex-1 flex flex-col items-center py-2 {{ request()->routeIs('mukellef-portal.bildirimler*') ? 'text-emerald-600' : 'text-zinc-400' }}">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
                </svg>
                <span class="text-xs mt-1 font-medium">Bildirimler</span>
            </a>
            <a href="{{ route('mukellef-portal.durum') }}"
               class="flex-1 flex flex-col items-center py-2 {{ request()->routeIs('mukellef-portal.durum*') ? 'text-emerald-600' : 'text-zinc-400' }}">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                </svg>
                <span class="text-xs mt-1 font-medium">Durum</span>
            </a>
        </div>
    </nav>
    @endauth

    <script>
        if ('serviceWorker' in navigator) {
            navigator.serviceWorker.register('/sw.js');
        }
    </script>

    @stack('scripts')
</body>
</html>
