<!DOCTYPE html>
<html lang="tr" class="scroll-smooth">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Mali Müşavir Asistanı</title>

    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:300,400,500,600,700,800,900&display=swap" rel="stylesheet" />

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        [x-cloak] { display: none !important; }

        .hero-grid {
            background-image:
                linear-gradient(rgba(16, 185, 129, 0.07) 1px, transparent 1px),
                linear-gradient(90deg, rgba(16, 185, 129, 0.07) 1px, transparent 1px);
            background-size: 64px 64px;
            mask-image: radial-gradient(ellipse 80% 70% at 50% 40%, black 30%, transparent 100%);
            -webkit-mask-image: radial-gradient(ellipse 80% 70% at 50% 40%, black 30%, transparent 100%);
        }

        .hero-glow {
            background: radial-gradient(ellipse 60% 50% at 50% 40%, rgba(16, 185, 129, 0.12) 0%, transparent 70%);
        }

        @keyframes fade-up {
            from { opacity: 0; transform: translateY(28px); }
            to { opacity: 1; transform: translateY(0); }
        }

        @keyframes fade-scale {
            from { opacity: 0; transform: scale(0.92); }
            to { opacity: 1; transform: scale(1); }
        }

        .animate-fade-up {
            animation: fade-up 0.8s cubic-bezier(0.22, 1, 0.36, 1) both;
        }

        .animate-fade-scale {
            animation: fade-scale 0.7s cubic-bezier(0.22, 1, 0.36, 1) both;
        }

        .stagger-1 { animation-delay: 0.1s; }
        .stagger-2 { animation-delay: 0.2s; }
        .stagger-3 { animation-delay: 0.35s; }
        .stagger-4 { animation-delay: 0.5s; }
        .stagger-5 { animation-delay: 0.65s; }

        .step-connector {
            background: linear-gradient(90deg, #10b981, #d4d4d8 50%, #10b981);
            background-size: 200% 100%;
            animation: shimmer 3s ease-in-out infinite;
        }

        @keyframes shimmer {
            0%, 100% { background-position: 200% 0; }
            50% { background-position: -200% 0; }
        }

        .feature-card {
            transition: transform 0.35s cubic-bezier(0.22, 1, 0.36, 1), box-shadow 0.35s ease;
        }
        .feature-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 20px 40px -12px rgba(0, 0, 0, 0.08);
        }
    </style>
</head>
<body class="font-sans antialiased bg-white text-slate-800" x-data="{ scrolled: false }" @scroll.window="scrolled = window.scrollY > 40">

    {{-- ===== NAVBAR ===== --}}
    <nav class="fixed top-0 inset-x-0 z-50 transition-all duration-300"
         :class="scrolled ? 'bg-slate-900/95 backdrop-blur-md shadow-lg shadow-slate-900/10' : 'bg-transparent'">
        <div class="max-w-6xl mx-auto px-6 flex items-center justify-between h-16">
            <a href="/" class="flex items-center space-x-2.5">
                <div class="w-8 h-8 rounded-lg bg-emerald-500 flex items-center justify-center">
                    <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                    </svg>
                </div>
                <span class="text-white font-bold text-lg tracking-tight">Mali Asistan</span>
            </a>

            <div class="flex items-center space-x-3">
                <a href="{{ route('mukellef-portal.login') }}"
                   class="hidden sm:inline-flex text-sm text-zinc-400 hover:text-white transition-colors font-medium px-3 py-1.5">
                    Mükellef Portal
                </a>
                <a href="{{ route('login') }}"
                   class="text-sm font-semibold text-white bg-emerald-600 hover:bg-emerald-500 px-5 py-2 rounded-lg transition-colors">
                    Giriş Yap
                </a>
            </div>
        </div>
    </nav>

    {{-- ===== HERO ===== --}}
    <section class="relative min-h-[92vh] flex items-center bg-slate-950 overflow-hidden">
        <div class="absolute inset-0 hero-grid"></div>
        <div class="absolute inset-0 hero-glow"></div>
        <div class="absolute inset-0 opacity-[0.03]" style="background-image: url('data:image/svg+xml,%3Csvg viewBox=%220 0 256 256%22 xmlns=%22http://www.w3.org/2000/svg%22%3E%3Cfilter id=%22noise%22%3E%3CfeTurbulence type=%22fractalNoise%22 baseFrequency=%220.9%22 numOctaves=%224%22 stitchTiles=%22stitch%22/%3E%3C/filter%3E%3Crect width=%22100%25%22 height=%22100%25%22 filter=%22url(%23noise)%22/%3E%3C/svg%3E');"></div>

        <div class="relative max-w-6xl mx-auto px-6 py-32 w-full">
            <div class="max-w-3xl">
                <div class="animate-fade-up stagger-1">
                    <span class="inline-flex items-center text-xs font-semibold tracking-widest uppercase text-emerald-400 mb-6">
                        <span class="w-8 h-px bg-emerald-500 mr-3"></span>
                        AI Destekli Muhasebe Platformu
                    </span>
                </div>

                <h1 class="animate-fade-up stagger-2 text-4xl sm:text-5xl md:text-6xl lg:text-7xl font-extrabold text-white leading-[1.05] tracking-tight mb-6">
                    Mali Müşavirlikte
                    <span class="relative">
                        <span class="text-transparent bg-clip-text bg-gradient-to-r from-emerald-400 to-emerald-300">Yeni Dönem</span>
                        <span class="absolute -bottom-1 left-0 w-full h-1 bg-gradient-to-r from-emerald-500 to-emerald-300/0 rounded-full"></span>
                    </span>
                </h1>

                <p class="animate-fade-up stagger-3 text-lg sm:text-xl text-zinc-400 leading-relaxed mb-10 max-w-xl font-light">
                    Belge toplama, beyanname takibi ve mükellef iletişimini
                    <span class="text-zinc-300 font-medium">tek platformda</span> otomatize edin.
                </p>

                <div class="animate-fade-up stagger-4 flex flex-col sm:flex-row gap-3">
                    <a href="{{ route('login') }}"
                       class="inline-flex items-center justify-center px-7 py-3.5 bg-emerald-600 hover:bg-emerald-500 text-white text-sm font-semibold rounded-xl transition-all hover:shadow-lg hover:shadow-emerald-500/20">
                        Panele Giriş
                        <svg class="w-4 h-4 ml-2" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5L21 12m0 0l-7.5 7.5M21 12H3"/>
                        </svg>
                    </a>
                    <a href="{{ route('mukellef-portal.login') }}"
                       class="inline-flex items-center justify-center px-7 py-3.5 border border-zinc-700 hover:border-zinc-500 text-zinc-300 hover:text-white text-sm font-semibold rounded-xl transition-all">
                        Mükellef Girişi
                    </a>
                </div>
            </div>

            <div class="hidden lg:block absolute right-12 top-1/2 -translate-y-1/2 animate-fade-scale stagger-5">
                <div class="relative w-72 h-72">
                    <div class="absolute inset-0 rounded-full border border-emerald-500/20"></div>
                    <div class="absolute inset-6 rounded-full border border-emerald-500/10"></div>
                    <div class="absolute inset-0 flex items-center justify-center">
                        <div class="grid grid-cols-2 gap-3">
                            <div class="w-16 h-16 rounded-2xl bg-emerald-500/10 border border-emerald-500/20 flex items-center justify-center backdrop-blur-sm">
                                <svg class="w-7 h-7 text-emerald-400" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                </svg>
                            </div>
                            <div class="w-16 h-16 rounded-2xl bg-amber-500/10 border border-amber-500/20 flex items-center justify-center backdrop-blur-sm">
                                <svg class="w-7 h-7 text-amber-400" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                </svg>
                            </div>
                            <div class="w-16 h-16 rounded-2xl bg-emerald-500/10 border border-emerald-500/20 flex items-center justify-center backdrop-blur-sm">
                                <svg class="w-7 h-7 text-emerald-400" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 18h.01M8 21h8a2 2 0 002-2V5a2 2 0 00-2-2H8a2 2 0 00-2 2v14a2 2 0 002 2z"/>
                                </svg>
                            </div>
                            <div class="w-16 h-16 rounded-2xl bg-zinc-500/10 border border-zinc-500/20 flex items-center justify-center backdrop-blur-sm">
                                <svg class="w-7 h-7 text-zinc-400" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
                                </svg>
                            </div>
                        </div>
                    </div>
                    <div class="absolute top-0 left-1/2 -translate-x-1/2 -translate-y-1/2">
                        <div class="w-3 h-3 rounded-full bg-emerald-400 shadow-lg shadow-emerald-400/50"></div>
                    </div>
                </div>
            </div>
        </div>

        <div class="absolute bottom-0 inset-x-0 h-32 bg-gradient-to-t from-white to-transparent"></div>
    </section>

    {{-- ===== FEATURES ===== --}}
    <section class="relative py-24 sm:py-32" x-data="{ shown: false }" x-intersect.half="shown = true">
        <div class="max-w-6xl mx-auto px-6">
            <div class="text-center mb-16">
                <span class="inline-flex items-center text-xs font-semibold tracking-widest uppercase text-emerald-600 mb-4">
                    <span class="w-6 h-px bg-emerald-500 mr-2"></span>
                    Özellikler
                    <span class="w-6 h-px bg-emerald-500 ml-2"></span>
                </span>
                <h2 class="text-3xl sm:text-4xl font-extrabold text-slate-900 tracking-tight">
                    Her Şey Tek Platformda
                </h2>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5">
                <div class="feature-card bg-white border border-zinc-200 rounded-2xl p-6 relative overflow-hidden"
                     x-show="shown" x-transition.duration.500ms style="transition-delay: 0ms">
                    <div class="absolute top-0 right-0 w-24 h-24 bg-emerald-50 rounded-bl-[3rem] -mr-2 -mt-2"></div>
                    <div class="relative">
                        <div class="w-11 h-11 rounded-xl bg-emerald-100 flex items-center justify-center mb-5">
                            <svg class="w-6 h-6 text-emerald-600" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 010-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178z"/>
                                <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                            </svg>
                        </div>
                        <h3 class="text-base font-bold text-slate-900 mb-2">AI Belge Tanıma</h3>
                        <p class="text-sm text-zinc-500 leading-relaxed">Fatura fotoğrafından VKN, tutar, KDV gibi verileri otomatik çıkarır.</p>
                    </div>
                </div>

                <div class="feature-card bg-white border border-zinc-200 rounded-2xl p-6 relative overflow-hidden"
                     x-show="shown" x-transition.duration.500ms style="transition-delay: 100ms">
                    <div class="absolute top-0 right-0 w-24 h-24 bg-amber-50 rounded-bl-[3rem] -mr-2 -mt-2"></div>
                    <div class="relative">
                        <div class="w-11 h-11 rounded-xl bg-amber-100 flex items-center justify-center mb-5">
                            <svg class="w-6 h-6 text-amber-600" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 012.25-2.25h13.5A2.25 2.25 0 0121 7.5v11.25m-18 0A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75m-18 0v-7.5A2.25 2.25 0 015.25 9h13.5A2.25 2.25 0 0121 11.25v7.5"/>
                            </svg>
                        </div>
                        <h3 class="text-base font-bold text-slate-900 mb-2">Beyanname Takvimi</h3>
                        <p class="text-sm text-zinc-500 leading-relaxed">Otomatik hatırlatmalar ve son tarih takibi ile beyanname kaçırmayın.</p>
                    </div>
                </div>

                <div class="feature-card bg-white border border-zinc-200 rounded-2xl p-6 relative overflow-hidden"
                     x-show="shown" x-transition.duration.500ms style="transition-delay: 200ms">
                    <div class="absolute top-0 right-0 w-24 h-24 bg-emerald-50 rounded-bl-[3rem] -mr-2 -mt-2"></div>
                    <div class="relative">
                        <div class="w-11 h-11 rounded-xl bg-emerald-100 flex items-center justify-center mb-5">
                            <svg class="w-6 h-6 text-emerald-600" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 1.5H8.25A2.25 2.25 0 006 3.75v16.5a2.25 2.25 0 002.25 2.25h7.5A2.25 2.25 0 0018 20.25V3.75a2.25 2.25 0 00-2.25-2.25H13.5m-3 0V3h3V1.5m-3 0h3m-3 18.75h3"/>
                            </svg>
                        </div>
                        <h3 class="text-base font-bold text-slate-900 mb-2">Mükellef Portalı</h3>
                        <p class="text-sm text-zinc-500 leading-relaxed">Mükelleflerin telefondan belge gönderebileceği PWA uygulaması.</p>
                    </div>
                </div>

                <div class="feature-card bg-white border border-zinc-200 rounded-2xl p-6 relative overflow-hidden"
                     x-show="shown" x-transition.duration.500ms style="transition-delay: 300ms">
                    <div class="absolute top-0 right-0 w-24 h-24 bg-zinc-50 rounded-bl-[3rem] -mr-2 -mt-2"></div>
                    <div class="relative">
                        <div class="w-11 h-11 rounded-xl bg-zinc-100 flex items-center justify-center mb-5">
                            <svg class="w-6 h-6 text-zinc-600" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M14.857 17.082a23.848 23.848 0 005.454-1.31A8.967 8.967 0 0118 9.75v-.7V9A6 6 0 006 9v.75a8.967 8.967 0 01-2.312 6.022c1.733.64 3.56 1.085 5.455 1.31m5.714 0a24.255 24.255 0 01-5.714 0m5.714 0a3 3 0 11-5.714 0"/>
                            </svg>
                        </div>
                        <h3 class="text-base font-bold text-slate-900 mb-2">Akıllı Bildirimler</h3>
                        <p class="text-sm text-zinc-500 leading-relaxed">Eksik belge konfirmasyonu ve toplu bildirim ile iletişimi otomatize edin.</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- ===== HOW IT WORKS ===== --}}
    <section class="py-24 sm:py-32 bg-zinc-50" x-data="{ shown: false }" x-intersect.half="shown = true">
        <div class="max-w-6xl mx-auto px-6">
            <div class="text-center mb-16">
                <span class="inline-flex items-center text-xs font-semibold tracking-widest uppercase text-emerald-600 mb-4">
                    <span class="w-6 h-px bg-emerald-500 mr-2"></span>
                    Nasıl Çalışır
                    <span class="w-6 h-px bg-emerald-500 ml-2"></span>
                </span>
                <h2 class="text-3xl sm:text-4xl font-extrabold text-slate-900 tracking-tight">
                    Üç Adımda Tamamlayın
                </h2>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-8 md:gap-0 items-start relative">
                {{-- Connector lines (md+) --}}
                <div class="hidden md:block absolute top-10 left-[calc(33.33%+10px)] right-[calc(33.33%+10px)] h-px step-connector"></div>

                <div class="text-center px-6" x-show="shown" x-transition.duration.600ms style="transition-delay: 0ms">
                    <div class="relative inline-flex items-center justify-center w-20 h-20 rounded-full bg-white border-2 border-emerald-200 shadow-sm mb-6">
                        <span class="absolute -top-2 -right-2 w-7 h-7 rounded-full bg-emerald-600 text-white text-xs font-bold flex items-center justify-center shadow-md">1</span>
                        <svg class="w-9 h-9 text-emerald-600" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75V16.5m-13.5-9L12 3m0 0l4.5 4.5M12 3v13.5"/>
                        </svg>
                    </div>
                    <h3 class="text-lg font-bold text-slate-900 mb-2">Belge Gönder</h3>
                    <p class="text-sm text-zinc-500 max-w-xs mx-auto">Mükellef telefonuyla fatura fotoğrafını çeker veya PDF yükler.</p>
                </div>

                <div class="text-center px-6" x-show="shown" x-transition.duration.600ms style="transition-delay: 150ms">
                    <div class="relative inline-flex items-center justify-center w-20 h-20 rounded-full bg-white border-2 border-amber-200 shadow-sm mb-6">
                        <span class="absolute -top-2 -right-2 w-7 h-7 rounded-full bg-amber-500 text-white text-xs font-bold flex items-center justify-center shadow-md">2</span>
                        <svg class="w-9 h-9 text-amber-500" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9.813 15.904L9 18.75l-.813-2.846a4.5 4.5 0 00-3.09-3.09L2.25 12l2.846-.813a4.5 4.5 0 003.09-3.09L9 5.25l.813 2.846a4.5 4.5 0 003.09 3.09L15.75 12l-2.846.813a4.5 4.5 0 00-3.09 3.09zM18.259 8.715L18 9.75l-.259-1.035a3.375 3.375 0 00-2.455-2.456L14.25 6l1.036-.259a3.375 3.375 0 002.455-2.456L18 2.25l.259 1.035a3.375 3.375 0 002.455 2.456L21.75 6l-1.036.259a3.375 3.375 0 00-2.455 2.456z"/>
                        </svg>
                    </div>
                    <h3 class="text-lg font-bold text-slate-900 mb-2">AI İşler</h3>
                    <p class="text-sm text-zinc-500 max-w-xs mx-auto">Gemini Flash yapay zeka ile veriler otomatik çıkarılır ve doğrulanır.</p>
                </div>

                <div class="text-center px-6" x-show="shown" x-transition.duration.600ms style="transition-delay: 300ms">
                    <div class="relative inline-flex items-center justify-center w-20 h-20 rounded-full bg-white border-2 border-emerald-200 shadow-sm mb-6">
                        <span class="absolute -top-2 -right-2 w-7 h-7 rounded-full bg-emerald-600 text-white text-xs font-bold flex items-center justify-center shadow-md">3</span>
                        <svg class="w-9 h-9 text-emerald-600" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>
                    <h3 class="text-lg font-bold text-slate-900 mb-2">Beyanname Hazır</h3>
                    <p class="text-sm text-zinc-500 max-w-xs mx-auto">Veriler organize edilir, beyanname takvimi güncellenir, eksikler bildirilir.</p>
                </div>
            </div>
        </div>
    </section>

    {{-- ===== STATS ===== --}}
    <section class="py-20 bg-slate-950 relative overflow-hidden"
             x-data="{ shown: false, count1: 0, count2: 100, count3: 0 }"
             x-intersect.once="shown = true;
                 let i1 = setInterval(() => { count1++; if(count1 >= 10) clearInterval(i1) }, 60);
                 let i2 = setInterval(() => { count2--; if(count2 <= 0) clearInterval(i2) }, 12);
                 let i3 = setInterval(() => { count3++; if(count3 >= 24) clearInterval(i3) }, 50);">
        <div class="absolute inset-0 hero-glow opacity-50"></div>

        <div class="relative max-w-5xl mx-auto px-6">
            <div class="grid grid-cols-1 md:grid-cols-3 divide-y md:divide-y-0 md:divide-x divide-zinc-800">
                <div class="text-center py-8 md:py-0 md:px-8">
                    <div class="text-4xl sm:text-5xl font-extrabold text-white mb-2">
                        <span x-text="count1"></span><span class="text-emerald-400">x</span>
                    </div>
                    <p class="text-sm text-zinc-400 font-medium">Daha Hızlı Belge İşleme</p>
                </div>
                <div class="text-center py-8 md:py-0 md:px-8">
                    <div class="text-4xl sm:text-5xl font-extrabold text-white mb-2">
                        <span x-text="count2"></span>
                    </div>
                    <p class="text-sm text-zinc-400 font-medium">Eksik Belge</p>
                </div>
                <div class="text-center py-8 md:py-0 md:px-8">
                    <div class="text-4xl sm:text-5xl font-extrabold text-white mb-2">
                        <span class="text-emerald-400">7</span>/<span x-text="count3"></span>
                    </div>
                    <p class="text-sm text-zinc-400 font-medium">Mükellef Erişimi</p>
                </div>
            </div>
        </div>
    </section>

    {{-- ===== CTA ===== --}}
    <section class="py-24 sm:py-32">
        <div class="max-w-3xl mx-auto px-6 text-center">
            <h2 class="text-3xl sm:text-4xl font-extrabold text-slate-900 tracking-tight mb-4">
                Hemen Başlayın
            </h2>
            <p class="text-zinc-500 text-lg mb-10 max-w-lg mx-auto">
                Mali müşavirlik süreçlerinizi dijitalleştirin. Ücretsiz deneyin.
            </p>
            <div class="flex flex-col sm:flex-row gap-3 justify-center">
                <a href="{{ route('register') }}"
                   class="inline-flex items-center justify-center px-8 py-3.5 bg-emerald-600 hover:bg-emerald-500 text-white text-sm font-semibold rounded-xl transition-all hover:shadow-lg hover:shadow-emerald-500/20">
                    Kayıt Ol
                    <svg class="w-4 h-4 ml-2" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5L21 12m0 0l-7.5 7.5M21 12H3"/>
                    </svg>
                </a>
                <a href="{{ route('login') }}"
                   class="inline-flex items-center justify-center px-8 py-3.5 border border-zinc-300 hover:border-zinc-400 text-zinc-700 text-sm font-semibold rounded-xl transition-all">
                    Giriş Yap
                </a>
            </div>
        </div>
    </section>

    {{-- ===== FOOTER ===== --}}
    <footer class="border-t border-zinc-200 py-10">
        <div class="max-w-6xl mx-auto px-6 flex flex-col sm:flex-row items-center justify-between gap-4">
            <div class="flex items-center space-x-2.5">
                <div class="w-6 h-6 rounded-md bg-emerald-500 flex items-center justify-center">
                    <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                    </svg>
                </div>
                <span class="text-sm text-zinc-500">&copy; {{ date('Y') }} Mali Müşavir Asistanı</span>
            </div>
            <div class="flex items-center space-x-6 text-sm text-zinc-500">
                <a href="{{ route('login') }}" class="hover:text-slate-800 transition-colors">Giriş</a>
                <a href="{{ route('mukellef-portal.login') }}" class="hover:text-slate-800 transition-colors">Mükellef Portal</a>
            </div>
        </div>
    </footer>

</body>
</html>
