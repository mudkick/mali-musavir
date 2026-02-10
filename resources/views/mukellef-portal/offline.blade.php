<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no">
    <title>Çevrimdışı - Mükellef Portal</title>
    <meta name="theme-color" content="#059669">
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />
    @vite(['resources/css/app.css'])
</head>
<body class="font-sans antialiased bg-zinc-50 min-h-screen flex items-center justify-center">
    <div class="max-w-md mx-auto px-4 text-center">
        <div class="bg-white rounded-xl shadow-sm border border-zinc-200 p-8">
            <div class="inline-flex items-center justify-center w-20 h-20 bg-zinc-100 rounded-full mb-6">
                <svg class="w-10 h-10 text-zinc-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 5.636a9 9 0 010 12.728m0 0l-2.829-2.829m2.829 2.829L21 21M15.536 8.464a5 5 0 010 7.072m0 0l-2.829-2.829m-4.243 2.829a4.978 4.978 0 01-1.414-2.83m-1.414 5.658a9 9 0 01-2.167-9.238m7.824 2.167a1 1 0 111.414 1.414m-1.414-1.414L3 3m8.293 8.293l1.414 1.414"/>
                </svg>
            </div>

            <h1 class="text-2xl font-bold text-slate-800 mb-2">Çevrimdışısınız</h1>
            <p class="text-zinc-600 mb-6">
                İnternet bağlantınızı kontrol edin ve tekrar deneyin.
            </p>

            <button onclick="window.location.reload()"
                    class="w-full py-3 bg-emerald-600 text-white text-sm font-medium rounded-lg hover:bg-emerald-700 transition-colors">
                Yeniden Dene
            </button>
        </div>
    </div>
</body>
</html>
