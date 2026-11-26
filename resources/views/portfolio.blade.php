<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="Portofolio Kikii - Full-Stack Web Developer yang berfokus pada arsitektur sistem yang tangguh, API berkinerja tinggi, dan antarmuka web modern yang responsif.">
    <title>Kikii — Full-Stack Web Developer</title>
    <link rel="icon" type="image/svg+xml" href="data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24' fill='white'%3E%3Cg transform='rotate(-30 12 12)'%3E%3Ccircle cx='7.3' cy='3.2' r='1.45'/%3E%3Crect x='5.5' y='4.7' width='3.6' height='14.6' rx='1.8'/%3E%3Crect x='14.9' y='4.7' width='3.6' height='14.6' rx='1.8'/%3E%3Ccircle cx='16.7' cy='20.8' r='1.45'/%3E%3C/g%3E%3C/svg%3E">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:ital,opsz,wght@0,14..32,100..900&family=Instrument+Serif:ital@1&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    {{-- Inline critical: prevent flash of unstyled background --}}
    <style>
        html, body { background: #000000 !important; color: #ffffff; }
    </style>
</head>

<body class="antialiased min-h-screen" style="background:#000000;color:#ffffff;">

    <!-- ━━━ VESPER GRAIN OVERLAY ━━━ -->
    <div class="grain" aria-hidden="true"></div>

    <!-- ━━━ REFERENCE-STYLE VIDEO BACKGROUND ━━━ -->
    <div class="hero-photo" aria-hidden="true">
        <video autoplay muted loop playsinline preload="auto" width="1920" height="1080" fetchpriority="high">
            <source src="{{ asset('videos/vesper-background.mp4') }}" type="video/mp4">
        </video>
    </div>

    <div class="menu-backdrop" aria-hidden="true"></div>

    <!-- ━━━ VESPER 1:1 FIRST-VIEWPORT SHELL (Header + Bottom-Centered Hero + Bottom Stats) ━━━ -->
    <div class="vsp-viewport">
        <!-- Header -->
        <header class="vsp-header">
            <!-- Left: Brand -->
            <a href="{{ route('portfolio.index') }}" class="vsp-logo appear appear--scale d1" aria-label="Halaman Beranda Portofolio Kikii">
                <svg class="vsp-logo-mark" width="22" height="22" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true">
                    <g transform="rotate(-30 12 12)">
                        <circle cx="7.3" cy="3.2" r="1.45"/>
                        <rect x="5.5" y="4.7" width="3.6" height="14.6" rx="1.8"/>
                        <rect x="14.9" y="4.7" width="3.6" height="14.6" rx="1.8"/>
                        <circle cx="16.7" cy="20.8" r="1.45"/>
                    </g>
                </svg>
                <span>Kikii<span class="font-normal text-[#9a9a9a]">.dev</span></span>
            </a>

            <!-- Center: Liquid-metal nav pills -->
            <nav id="site-nav" aria-label="Navigasi Utama" class="hidden md:flex md:items-center md:gap-2 md:justify-self-center">
                @php $navDelays = ['projects' => 'd2', 'architecture' => 'd3', 'experience' => 'd4', 'contact' => 'd5']; @endphp
                @foreach (['projects' => 'Proyek', 'architecture' => 'Keahlian', 'experience' => 'Pengalaman', 'contact' => 'Kontak'] as $anchor => $label)
                    <a href="#{{ $anchor }}"
                       class="nav-pill appear appear--scale {{ $navDelays[$anchor] ?? '' }}">
                        {{ $label }}
                    </a>
                @endforeach
            </nav>

            <!-- Right: CTA + Burger -->
            <div class="flex items-center gap-3 justify-self-end">
                <a href="#contact" class="vsp-header-cta btn btn-solid hidden md:inline-flex appear appear--scale d6" style="height:40px;min-height:40px;padding:0 16px;border-radius:6px;">
                    Hubungi Saya
                </a>

                <button id="mobile-menu-btn" type="button"
                        aria-expanded="false" aria-controls="mobile-menu"
                        aria-label="Buka menu navigasi"
                        class="vsp-burger md:hidden">
                    <span class="flex flex-col gap-[5px]" aria-hidden="true">
                        <span class="burger-bar"></span>
                        <span class="burger-bar"></span>
                        <span class="burger-bar"></span>
                    </span>
                </button>
            </div>
        </header>

        <!-- Bottom-Centered Hero -->
        <section id="top" class="vsp-hero">
            <div class="vsp-hero-copy">
                <!-- Badge -->
                <div class="vsp-badge appear appear--pop d7">
                    <svg class="vsp-badge-sparkle" width="18" height="20" viewBox="0 0 24 24" fill="white" aria-hidden="true">
                        <path d="M12 2.6C12.55 2.6 12.88 3.15 13.08 4.7c.62 4.7 1.52 5.6 6.22 6.22 1.55.2 2.1.53 2.1 1.08s-.55.88-2.1 1.08c-4.7.62-5.6 1.52-6.22 6.22-.2 1.55-.53 2.1-1.08 2.1s-.88-.55-1.08-2.1c-.62-4.7-1.52-5.6-6.22-6.22C3.15 12.88 2.6 12.55 2.6 12s.55-.88 2.1-1.08c4.7-.62 5.6-1.52 6.22-6.22C11.12 3.15 11.45 2.6 12 2.6Z"/>
                    </svg>
                    Terbuka untuk Peluang Kerja &amp; Kolaborasi Proyek
                </div>

                <!-- H1 -->
                <h1>
                    <span class="block appear appear--mask d8">Selamat datang, wahai</span>
                    <span class="block appear appear--mask d9"><em class="vsp-serif">Kikiawan &amp; Kikiawati</em></span>
                </h1>

                <!-- Lede -->
                <p class="vsp-lede appear appear--soft d9">
                    Halo! Saya Kikii, seorang Full-Stack Developer yang merancang aplikasi web yang rapi, cepat, dan mudah dipelihara dengan Laravel, PHP, serta JavaScript modern.
                </p>

                <!-- Actions -->
                <div class="vsp-hero-actions">
                    <a href="#projects" class="btn btn-solid appear appear--btn d10">
                        Lihat Hasil Karya
                    </a>
                    <a href="#contact" class="btn btn-ghost appear appear--btn d11">
                        Mari Berkolaborasi
                    </a>
                </div>
            </div>
        </section>

        <!-- Bottom-Left 3-Stat Strip -->
        <div class="vsp-stats">
            <div class="vsp-stat appear appear--stat d12">
                <svg width="44" height="22" viewBox="0 0 44 22" fill="none" aria-hidden="true">
                    <defs>
                        <linearGradient id="sg1a" x1="0" y1="0" x2="1" y2="0">
                            <stop offset="0%" stop-color="#555"/>
                            <stop offset="100%" stop-color="#222"/>
                        </linearGradient>
                        <linearGradient id="sg1b" x1="0" y1="0" x2="1" y2="0">
                            <stop offset="0%" stop-color="#aaa"/>
                            <stop offset="100%" stop-color="#666"/>
                        </linearGradient>
                    </defs>
                    <rect x="0" y="3" width="20" height="16" rx="8" fill="url(#sg1a)"/>
                    <rect x="24" y="3" width="20" height="16" rx="8" fill="url(#sg1b)"/>
                </svg>
                <div class="flex flex-col gap-0.5">
                    <span class="vsp-stat-label">Arsitektur Modular</span>
                    <span class="text-[11px] text-[#9a9a9a]">Bersih &amp; mudah dikembangkan</span>
                </div>
            </div>

            <div class="vsp-stat appear appear--stat d13">
                <svg width="22" height="22" viewBox="0 0 22 22" fill="none" aria-hidden="true">
                    <rect width="22" height="22" rx="5" fill="#fff" fill-opacity="0.9"/>
                    <path d="M11 5.5v7m0 0l-3-3m3 3l3-3" stroke="#111" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"/>
                    <path d="M6 15.5h10" stroke="#111" stroke-width="1.6" stroke-linecap="round"/>
                </svg>
                <div class="flex flex-col gap-0.5">
                    <span class="vsp-stat-label">Performa Terukur</span>
                    <span class="text-[11px] text-[#9a9a9a]">Query efisien &amp; caching Redis</span>
                </div>
            </div>

            <div class="vsp-stat appear appear--stat d14">
                <svg width="44" height="22" viewBox="0 0 44 22" fill="none" aria-hidden="true">
                    <circle cx="8" cy="11" r="9" fill="#2a2a2a" stroke="#000" stroke-width="1"/>
                    <circle cx="8" cy="8.5" r="3" fill="#555"/>
                    <ellipse cx="8" cy="16" rx="5" ry="3.5" fill="#444"/>
                    <circle cx="22" cy="11" r="9" fill="#e8e8e8" stroke="#000" stroke-width="1"/>
                    <circle cx="22" cy="8.5" r="3" fill="#aaa"/>
                    <ellipse cx="22" cy="16" rx="5" ry="3.5" fill="#bbb"/>
                    <circle cx="36" cy="11" r="9" fill="#525252" stroke="#000" stroke-width="1"/>
                    <circle cx="36" cy="8.5" r="3" fill="#888"/>
                    <ellipse cx="36" cy="16" rx="5" ry="3.5" fill="#666"/>
                </svg>
                <div class="flex flex-col gap-0.5">
                    <span class="vsp-stat-label">Siap Produksi</span>
                    <span class="text-[11px] text-[#9a9a9a]">Teruji PHPUnit &amp; standar PSR-12</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Mobile Drawer -->
    <div id="mobile-menu" class="hidden md:hidden" aria-label="Navigasi Seluler">
        <nav class="flex flex-col items-center gap-3 w-full max-w-xs">
            @foreach (['projects' => 'Proyek', 'architecture' => 'Keahlian', 'experience' => 'Pengalaman', 'contact' => 'Kontak'] as $anchor => $label)
                <a href="#{{ $anchor }}" class="nav-pill w-full justify-center" style="height:48px;font-size:15px;">
                    {{ $label }}
                </a>
            @endforeach
            <a href="#contact" class="btn btn-solid w-full mt-2" style="height:46px;">
                Hubungi Saya
            </a>
        </nav>
    </div>

    <main id="main-content" class="relative z-10">

    <!-- ━━━ PROJECTS ━━━ -->
    <section id="projects" class="section-divider py-20" style="background:rgba(255,255,255,0.015);">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-10">

            <!-- Section header -->
            <div class="reveal flex flex-col md:flex-row md:items-end md:justify-between gap-6">
                <div>
                    <h2 class="text-3xl font-semibold tracking-[-0.03em] text-white sm:text-4xl">
                        Karya &amp; <em class="vsp-serif font-normal">Proyek</em> Pilihan
                    </h2>
                    <p class="mt-3 max-w-xl text-[15px] text-[#9a9a9a]">
                        Kumpulan aplikasi web nyata, integrasi sistem, dan karya digital yang dibangun dengan perhatian pada kualitas performa dan estetika.
                    </p>
                </div>

                <!-- Filter tabs -->
                <div class="w-full md:w-auto">
                    <div id="project-filter-tablist"
                         class="project-filter-tablist relative grid grid-cols-2 sm:inline-flex sm:flex-row p-1.5 rounded-xl border gap-1.5 w-full sm:w-auto"
                         style="border-color:rgba(255,255,255,0.16);background:rgba(255,255,255,0.03);backdrop-filter:blur(12px);"
                         role="tablist"
                         aria-label="Kategori Proyek">

                        <!-- Morphing active indicator -->
                        <div id="project-filter-pill"
                             class="filter-morph-pill"
                             aria-hidden="true"></div>

                        @php
                            $translatedTabs = [
                                'all' => 'Semua Karya',
                                'full-stack' => 'Aplikasi Web',
                                'backend-api' => 'Backend & API',
                                'dev-tool' => 'Alat & Utilitas',
                            ];
                        @endphp
                        @foreach ($categories as $key => $label)
                            <button type="button"
                                    role="tab"
                                    id="filter-tab-{{ $key }}"
                                    aria-selected="{{ $key === 'all' ? 'true' : 'false' }}"
                                    tabindex="{{ $key === 'all' ? '0' : '-1' }}"
                                    data-category="{{ $key }}"
                                    class="project-filter-tab group relative z-10 flex min-h-[40px] items-center justify-center rounded-lg px-4 py-2 text-xs tracking-wide whitespace-nowrap transition-colors duration-200 select-none focus-visible:outline-none">
                                <span class="tab-label pointer-events-none transition-transform duration-150 group-active:scale-95">
                                    {{ $translatedTabs[$key] ?? $label }}
                                </span>
                            </button>
                        @endforeach
                    </div>
                </div>
            </div>

            <!-- Featured Showcase: Paksadita Paksi -->
            <div class="reveal vsp-card-featured mt-10 p-6 sm:p-8">
                <div class="grid grid-cols-1 gap-8 lg:grid-cols-12 lg:items-center">
                    <div class="lg:col-span-7">
                        <div class="flex flex-wrap items-center gap-2.5">
                            <span class="vsp-badge" style="padding:5px 12px;font-size:11px;">
                                ★ PROYEK UNGGULAN
                            </span>
                            <span class="cat-badge">
                                Web Interaktif &amp; Portofolio
                            </span>
                        </div>

                        <h3 class="mt-4 text-2xl sm:text-3xl font-semibold tracking-[-0.03em] text-white">
                            Paksadita Paksi
                        </h3>
                        <p class="mt-2 text-[15px] leading-relaxed text-[#9a9a9a]">
                            Platform web interaktif dan portofolio digital yang menyajikan pengalaman visual memikat, navigasi yang responsif, serta performa pemuatan yang cepat di berbagai ukuran layar.
                        </p>

                        <div class="mt-4 flex flex-wrap gap-2">
                            @foreach (['Laravel', 'PHP', 'Tailwind CSS', 'JavaScript', 'Responsive Web'] as $tech)
                                <span class="tech-chip">{{ $tech }}</span>
                            @endforeach
                        </div>

                        <div class="mt-6 flex flex-wrap items-center gap-3">
                            <a href="https://mrizqys.web.id" target="_blank" rel="noopener noreferrer"
                               class="btn btn-solid" style="height:42px;min-height:42px;">
                                <span>Buka Live Preview (mrizqys.web.id)</span>
                                <svg class="ml-2 h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/>
                                </svg>
                            </a>
                        </div>
                    </div>

                    <div class="lg:col-span-5">
                        <div class="rounded-xl border p-4 sm:p-5" style="border-color:rgba(255,255,255,0.12);background:rgba(0,0,0,0.6);">
                            <div class="flex items-center justify-between border-b pb-3" style="border-color:rgba(255,255,255,0.1);">
                                <span class="font-mono text-xs font-semibold text-white">Pratinjau Situs</span>
                                <span class="font-mono text-[11px] text-[#9a9a9a]">mrizqys.web.id</span>
                            </div>
                            <div class="mt-4 space-y-3 font-mono text-xs">
                                <div class="flex items-center justify-between">
                                    <span class="text-[#9a9a9a]">Status Web:</span>
                                    <span class="inline-flex items-center gap-1.5 font-medium text-emerald-400">
                                        <span class="h-2 w-2 rounded-full bg-emerald-400"></span> Online &amp; Aktif
                                    </span>
                                </div>
                                <div class="flex items-center justify-between">
                                    <span class="text-[#9a9a9a]">Domain:</span>
                                    <span class="text-white">mrizqys.web.id</span>
                                </div>
                                <div class="flex items-center justify-between">
                                    <span class="text-[#9a9a9a]">Aksesibilitas:</span>
                                    <span class="text-[#d8d8d8]">Mobile &amp; Desktop Ready</span>
                                </div>
                            </div>
                            <a href="https://mrizqys.web.id" target="_blank" rel="noopener noreferrer"
                               class="btn btn-ghost mt-4 w-full" style="height:40px;min-height:40px;">
                                Kunjungi Situs Sekarang &rarr;
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Projects Grid -->
            @php
                $displayProjects = $projects->isNotEmpty() ? $projects : collect([
                    (object)[
                        'id' => 1,
                        'title' => 'Paksadita Paksi',
                        'slug' => 'paksadita-paksi',
                        'category' => 'full-stack',
                        'category_label' => 'Web App & Portofolio',
                        'tagline' => 'Platform web interaktif dan portofolio personal yang responsif dan modern.',
                        'description' => 'Paksadita Paksi adalah platform website interaktif yang dibangun dengan fokus pada kenyamanan penjelajahan, estetika visual yang elegan, dan performa tinggi di berbagai ukuran layar.',
                        'architecture_notes' => 'Dioptimalkan dengan prinsip clean code, struktur komponen modular, dan kompresi aset untuk pemuatan super cepat.',
                        'key_features' => [
                            'Desain responsif yang adaptif untuk smartphone, tablet, hingga desktop',
                            'Navigasi yang intuitif dengan hierarki informasi yang jelas',
                            'Waktu muat halaman instan dan performa optimal',
                            'Tampilan visual modern yang memanjakan mata',
                        ],
                        'tech_stack' => ['Laravel', 'PHP', 'Tailwind CSS', 'JavaScript'],
                        'github_url' => null,
                        'demo_url' => 'https://mrizqys.web.id',
                        'system_metrics' => 'Live & aktif di mrizqys.web.id',
                    ],
                    (object)[
                        'id' => 2,
                        'title' => 'LogPulse',
                        'slug' => 'logpulse-realtime-monitor',
                        'category' => 'full-stack',
                        'category_label' => 'Aplikasi Full-Stack',
                        'tagline' => 'Agregasi pelacakan error dan telemetri log waktu nyata untuk layanan berbasis Laravel.',
                        'description' => 'Sistem monitoring log yang mengumpulkan trace error aplikasi secara terpusat, mengelompokkannya secara otomatis, dan memberikan notifikasi instan.',
                        'architecture_notes' => 'Menggunakan Redis buffering untuk menangani lonjakan data tanpa kehilangan paket informasi log.',
                        'key_features' => [
                            'Pemrosesan log berkecepatan tinggi dengan Redis Streams',
                            'Dashboard visual interaktif untuk pelacakan error',
                            'Integrasi notifikasi ke berbagai kanal komunikasi',
                        ],
                        'tech_stack' => ['Laravel 12', 'PostgreSQL', 'Redis Streams', 'Tailwind CSS', 'Alpine.js'],
                        'github_url' => 'https://github.com/example/logpulse',
                        'demo_url' => null,
                        'system_metrics' => 'Mampu memproses hingga 35.000 events/detik',
                    ],
                    (object)[
                        'id' => 3,
                        'title' => 'SchemaCraft',
                        'slug' => 'schemacraft-database-designer',
                        'category' => 'dev-tool',
                        'category_label' => 'Alat Pengembang',
                        'tagline' => 'Pemodel skema database visual dan generator migrasi Laravel dua arah.',
                        'description' => 'Aplikasi untuk mendesain relasi tabel database secara visual dan langsung menghasilkan file migrasi, model Eloquent, serta factory yang rapi dan terstandarisasi.',
                        'architecture_notes' => 'Menggunakan AST parser untuk mengonversi rantai metode migrasi langsung ke dalam grafik node relasional.',
                        'key_features' => [
                            'Sinkronisasi dua arah antara kanvas visual dan file migrasi',
                            'Deteksi otomatis konflik referensi foreign key',
                            'Ekspor model Eloquent lengkap dengan relasi ter-typehint',
                        ],
                        'tech_stack' => ['PHP 8.5', 'Laravel 12', 'Tailwind CSS', 'TypeScript'],
                        'github_url' => 'https://github.com/example/schemacraft',
                        'demo_url' => null,
                        'system_metrics' => 'Parsing skema lebih dari 120 tabel dalam < 180ms',
                    ],
                    (object)[
                        'id' => 4,
                        'title' => 'QueueMaster',
                        'slug' => 'queuemaster-horizon-profiler',
                        'category' => 'backend-api',
                        'category_label' => 'Backend & API',
                        'tagline' => 'Profiler eksekusi job dan telemetri latensi untuk antrean antarmuka antrean Laravel.',
                        'description' => 'Alat pemantau antrean Laravel untuk mendeteksi bottleneck pemrosesan background job dan penggunaan memori sebelum memengaruhi ketersediaan layanan.',
                        'architecture_notes' => 'Merekam siklus hidup job dengan overhead memori yang sangat rendah.',
                        'key_features' => [
                            'Pelacakan durasi, selisih memori, dan jumlah query database per job',
                            'Deteksi otomatis kejanggalan durasi eksekusi',
                            'Fitur jeda dan coba ulang job yang aman',
                        ],
                        'tech_stack' => ['Laravel 12', 'PHP 8.5', 'PostgreSQL', 'Tailwind CSS'],
                        'github_url' => 'https://github.com/example/queuemaster',
                        'demo_url' => null,
                        'system_metrics' => 'Overhead CPU kurang dari 1.2% pada beban kerja tinggi',
                    ],
                ]);
            @endphp

            <div class="mt-8 grid grid-cols-1 gap-6 md:grid-cols-2 lg:grid-cols-3">
                @forelse ($displayProjects as $index => $project)
                    <article
                        class="project-card vsp-card reveal reveal-delay-{{ min($index + 1, 4) }} flex flex-col"
                        data-category="{{ $project->category }}"
                        data-project-json="{{ json_encode([
                            'id'               => $project->id,
                            'title'            => $project->title,
                            'slug'             => $project->slug,
                            'category'         => $project->category,
                            'categoryLabel'    => $project->category_label ?? $project->category,
                            'tagline'          => $project->tagline,
                            'description'      => $project->description,
                            'architectureNotes'=> $project->architecture_notes,
                            'keyFeatures'      => $project->key_features,
                            'techStack'        => $project->tech_stack,
                            'githubUrl'        => $project->github_url,
                            'demoUrl'          => $project->demo_url,
                            'systemMetrics'    => $project->system_metrics,
                        ]) }}"
                    >
                        <div class="flex flex-1 flex-col p-6">
                            <!-- Category + metric -->
                            <div class="flex items-center justify-between gap-2">
                                <span class="cat-badge">
                                    {{ $project->category_label ?? $project->category }}
                                </span>
                                @if (!empty($project->system_metrics))
                                    <span class="max-w-[160px] truncate font-mono text-[10px] text-[#9a9a9a]" title="{{ $project->system_metrics }}">
                                        {{ $project->system_metrics }}
                                    </span>
                                @endif
                            </div>

                            <!-- Title & tagline -->
                            <h3 class="mt-4 text-xl font-semibold tracking-[-0.02em] text-white">{{ $project->title }}</h3>
                            <p class="mt-2 text-sm leading-relaxed text-[#9a9a9a]">{{ $project->tagline }}</p>

                            <!-- Tech chips (up to 4) -->
                            <div class="mt-4 flex flex-wrap gap-1.5">
                                @foreach (array_slice($project->tech_stack ?? [], 0, 4) as $tech)
                                    <span class="tech-chip">{{ $tech }}</span>
                                @endforeach
                            </div>
                        </div>

                        <!-- Card footer -->
                        <div class="border-t px-6 py-4" style="border-color:rgba(255,255,255,0.08);">
                            <div class="flex items-center justify-between gap-2">
                                <button type="button" data-open-project-modal
                                        class="inline-flex min-h-[44px] items-center gap-1.5 text-sm font-medium text-white transition-colors hover:text-[#d8d8d8]">
                                    Detail Proyek
                                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                                    </svg>
                                </button>

                                <div class="flex items-center gap-2">
                                    @if (!empty($project->demo_url))
                                        <a href="{{ $project->demo_url }}"
                                           target="_blank" rel="noopener noreferrer"
                                           class="btn btn-ghost" style="min-height:36px;height:36px;padding:0 12px;font-size:12px;">
                                            <span>Live Preview</span>
                                            <svg class="ml-1.5 h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/>
                                            </svg>
                                        </a>
                                    @endif

                                    @if (!empty($project->github_url))
                                        <a href="{{ $project->github_url }}"
                                           target="_blank" rel="noopener noreferrer"
                                           aria-label="Lihat kode sumber {{ $project->title }} di GitHub"
                                           class="flex h-10 w-10 items-center justify-center rounded-lg border transition-colors hover:border-white/40 hover:text-white"
                                           style="border-color:rgba(255,255,255,0.14);color:#9a9a9a;">
                                            <svg class="h-4 w-4" fill="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                                <path fill-rule="evenodd" clip-rule="evenodd" d="M12 2C6.477 2 2 6.484 2 12.017c0 4.425 2.865 8.18 6.839 9.504.5.092.682-.217.682-.483 0-.237-.008-.868-.013-1.703-2.782.605-3.369-1.343-3.369-1.343-.454-1.158-1.11-1.466-1.11-1.466-.908-.62.069-.608.069-.608 1.003.07 1.53 1.032 1.53 1.032.892 1.53 2.341 1.088 2.91.832.092-.647.35-1.088.636-1.338-2.22-.253-4.555-1.113-4.555-4.951 0-1.093.39-1.988 1.029-2.688-.103-.253-.446-1.272.098-2.65 0 0 .84-.27 2.75 1.026A9.564 9.564 0 0112 6.844c.85.004 1.705.115 2.504.337 1.909-1.296 2.747-1.027 2.747-1.027.546 1.379.202 2.398.1 2.651.64.7 1.028 1.595 1.028 2.688 0 3.848-2.339 4.695-4.566 4.943.359.309.678.92.678 1.855 0 1.338-.012 2.419-.012 2.747 0 .268.18.58.688.482A10.019 10.019 0 0022 12.017C22 6.484 17.522 2 12 2z"/>
                                            </svg>
                                        </a>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </article>
                @empty
                    <div class="col-span-full rounded-2xl border border-dashed p-12 text-center" style="border-color:rgba(255,255,255,0.16);">
                        <p class="font-semibold text-white">Belum ada proyek di kategori ini.</p>
                        <p class="mt-1 text-sm text-[#9a9a9a]">Silakan pilih tab kategori lainnya untuk melihat karya saya.</p>
                    </div>
                @endforelse
            </div>

            <!-- Filter empty state -->
            <div id="projects-empty-state" class="hidden mt-8 rounded-2xl border border-dashed p-12 text-center" style="border-color:rgba(255,255,255,0.16);">
                <p class="font-semibold text-white">Belum ada proyek di kategori ini.</p>
                <p class="mt-1 text-sm text-[#9a9a9a]">Silakan klik kategori lainnya untuk melihat hasil karya.</p>
            </div>
        </div>
    </section>

    <!-- ━━━ ARCHITECTURE / SKILLS ━━━ -->
    <section id="architecture" class="section-divider py-20">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-10">

            <div class="reveal mb-12 flex flex-col gap-8 lg:flex-row lg:items-end lg:justify-between">
                <div class="max-w-2xl">
                    <h2 class="text-3xl font-semibold tracking-[-0.03em] text-white sm:text-4xl">
                        Keahlian &amp; <em class="vsp-serif font-normal">Pendekatan</em> Teknis
                    </h2>
                    <p class="mt-3 text-[15px] text-[#9a9a9a]">
                        Prinsip kerja saya dalam membangun sistem: terstruktur, mudah dipelihara, dan memiliki performa nyata yang terukur.
                    </p>
                </div>

                <!-- Stack chips -->
                <div class="lg:max-w-md">
                    <p class="mb-2.5 font-mono text-[11px] font-medium uppercase tracking-widest text-[#9a9a9a]">Teknologi Utama</p>
                    <div class="flex flex-wrap gap-1.5">
                        @foreach (['Laravel 12', 'PHP 8.5', 'PostgreSQL / MySQL', 'Redis Streams', 'Tailwind CSS', 'JavaScript & Alpine.js', 'Docker'] as $tech)
                            <span class="tech-chip">{{ $tech }}</span>
                        @endforeach
                    </div>
                </div>
            </div>

            <!-- System Spec Bar (ringkasan_sistem.json) -->
            <div class="vsp-card reveal mb-8 p-6">
                <div class="flex items-center justify-between border-b pb-3.5" style="border-color:rgba(255,255,255,0.12);">
                    <div class="flex items-center gap-3">
                        <div class="flex gap-1.5" aria-hidden="true">
                            <span class="h-2.5 w-2.5 rounded-full bg-white/80"></span>
                            <span class="h-2.5 w-2.5 rounded-full bg-white/35"></span>
                            <span class="h-2.5 w-2.5 rounded-full bg-white/15"></span>
                        </div>
                        <span class="font-mono text-xs text-white">$ cek_arsitektur --rinci</span>
                    </div>
                    <span class="font-mono text-xs text-[#9a9a9a]">ringkasan_sistem.json</span>
                </div>
                <div class="mt-4 grid grid-cols-1 gap-4 font-mono text-xs md:grid-cols-3">
                    <div class="rounded-lg border p-3.5" style="border-color:rgba(255,255,255,0.1);background:rgba(255,255,255,0.02);">
                        <span class="text-[#9a9a9a]">Pengujian Kode</span>
                        <p class="mt-1 text-sm font-semibold text-white">PHPUnit Suite</p>
                        <p class="mt-0.5 text-[11px] text-[#d8d8d8]">Feature &amp; Unit Test</p>
                    </div>
                    <div class="rounded-lg border p-3.5" style="border-color:rgba(255,255,255,0.1);background:rgba(255,255,255,0.02);">
                        <span class="text-[#9a9a9a]">Standar Kode</span>
                        <p class="mt-1 text-sm font-semibold text-white">PSR-12 / Pint</p>
                        <p class="mt-0.5 text-[11px] text-[#d8d8d8]">Strict types aktif</p>
                    </div>
                    <div class="rounded-lg border p-3.5" style="border-color:rgba(255,255,255,0.1);background:rgba(255,255,255,0.02);">
                        <span class="text-[#9a9a9a]">Lingkungan Kerja</span>
                        <p class="mt-1 text-sm font-semibold text-white">PHP 8.5 &bull; Laravel 12</p>
                        <p class="mt-0.5 text-[11px] text-[#d8d8d8]">MySQL / SQLite &bull; Vite Pipeline</p>
                    </div>
                </div>
            </div>

            <!-- 3-col staggered cards -->
            <div class="grid grid-cols-1 gap-6 md:grid-cols-3">

                <!-- Card 1 -->
                <div class="vsp-card reveal reveal-delay-1 p-6">
                    <div class="flex h-11 w-11 items-center justify-center rounded-lg border"
                         style="border-color:rgba(255,255,255,0.14);background:rgba(255,255,255,0.05);color:#fff;">
                        <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 7v10c0 2.21 3.582 4 8 4s8-1.79 8-4V7M4 7c0 2.21 3.582 4 8 4s8-1.79 8-4M4 7c0-2.21 3.582-4 8-4s8 1.79 8 4"/>
                        </svg>
                    </div>
                    <h3 class="mt-5 text-lg font-semibold tracking-[-0.02em] text-white">Backend Cepat &amp; Tangguh</h3>
                    <p class="mt-2 text-sm leading-relaxed text-[#9a9a9a]">
                        Optimalisasi database, caching efisien dengan Redis, dan pemrosesan antrean latar belakang agar performa web tetap prima.
                    </p>
                    <ul class="mt-5 space-y-2">
                        @foreach (['Struktur &amp; Indeks Relasi Database Efisien', 'Redis Streams &amp; Manajemen Caching', 'Arsitektur RESTful API yang Bersih &amp; Aman'] as $item)
                            <li class="flex items-center gap-2.5 font-mono text-xs text-[#d8d8d8]">
                                <span class="h-1.5 w-1.5 shrink-0 rounded-full bg-white/70" aria-hidden="true"></span>
                                {!! $item !!}
                            </li>
                        @endforeach
                    </ul>
                </div>

                <!-- Card 2 -->
                <div class="vsp-card reveal reveal-delay-2 p-6">
                    <div class="flex h-11 w-11 items-center justify-center rounded-lg border"
                         style="border-color:rgba(255,255,255,0.14);background:rgba(255,255,255,0.05);color:#fff;">
                        <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M10 20l4-16m4 4l4 4-4 4M6 16l-4-4 4-4"/>
                        </svg>
                    </div>
                    <h3 class="mt-5 text-lg font-semibold tracking-[-0.02em] text-white">Antarmuka Web yang Nyaman</h3>
                    <p class="mt-2 text-sm leading-relaxed text-[#9a9a9a]">
                        Penerapan HTML5 semantik, kontras warna yang nyaman di mata, aksesibilitas ramah pengguna, dan tata letak responsif di setiap perangkat.
                    </p>
                    <ul class="mt-5 space-y-2">
                        @foreach (['Desain Responsif &amp; Modern dengan Tailwind CSS', 'Aksesibilitas &amp; Struktur Navigasi Ramah Pengguna', 'Formulir Interaktif dengan Validasi Cepat'] as $item)
                            <li class="flex items-center gap-2.5 font-mono text-xs text-[#d8d8d8]">
                                <span class="h-1.5 w-1.5 shrink-0 rounded-full bg-white/70" aria-hidden="true"></span>
                                {!! $item !!}
                            </li>
                        @endforeach
                    </ul>
                </div>

                <!-- Card 3 -->
                <div class="vsp-card reveal reveal-delay-3 p-6">
                    <div class="flex h-11 w-11 items-center justify-center rounded-lg border"
                         style="border-color:rgba(255,255,255,0.14);background:rgba(255,255,255,0.05);color:#fff;">
                        <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                        </svg>
                    </div>
                    <h3 class="mt-5 text-lg font-semibold tracking-[-0.02em] text-white">Kualitas Kode &amp; Pengujian</h3>
                    <p class="mt-2 text-sm leading-relaxed text-[#9a9a9a]">
                        Pengujian otomatis menggunakan PHPUnit, penegakan standar kode PSR, dan workflow terstandarisasi untuk mencegah potensi bug.
                    </p>
                    <ul class="mt-5 space-y-2">
                        @foreach (['Automated Testing PHPUnit (Feature &amp; Unit)', 'Standar Kode dengan Laravel Pint', 'Konfigurasi Docker yang Konsisten &amp; Reusable'] as $item)
                            <li class="flex items-center gap-2.5 font-mono text-xs text-[#d8d8d8]">
                                <span class="h-1.5 w-1.5 shrink-0 rounded-full bg-white/70" aria-hidden="true"></span>
                                {!! $item !!}
                            </li>
                        @endforeach
                    </ul>
                </div>
            </div>
        </div>
    </section>

    <!-- ━━━ EXPERIENCE ━━━ -->
    <section id="experience" class="section-divider py-20" style="background:rgba(255,255,255,0.015);">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-10">

            <div class="reveal mb-14 max-w-2xl">
                <h2 class="text-3xl font-semibold tracking-[-0.03em] text-white sm:text-4xl">
                    Pengalaman &amp; <em class="vsp-serif font-normal">Perjalanan</em> Kerja
                </h2>
                <p class="mt-3 text-[15px] text-[#9a9a9a]">
                    Rekam jejak dalam membangun sistem perangkat lunak, merancang antarmuka, dan memberikan solusi teknologi yang berdampak nyata.
                </p>
            </div>

            <!-- Timeline -->
            <div class="ml-2 space-y-10 border-l sm:ml-4" style="border-color:rgba(255,255,255,0.16);">

                @foreach ([
                    ['title' => 'Senior Full-Stack Engineer',   'period' => '2023 - Sekarang', 'sub' => 'Sistem Terdistribusi &amp; Aplikasi Web Modern', 'active' => true,  'body' => 'Merancang dan memimpin arsitektur sistem berbasis Laravel modular. Mengoptimalkan antrean latar belakang Redis untuk memproses ratusan ribu tugas terjadwal harian serta meningkatkan kecepatan respon sistem.'],
                    ['title' => 'Backend Laravel Developer',    'period' => '2021 - 2023',     'sub' => 'Layanan API &amp; Arsitektur Database',           'active' => false, 'body' => 'Membangun integrasi gateway pembayaran yang aman, sistem webhook otomatis, serta RESTful API terstruktur untuk kebutuhan aplikasi mobile dan antarmuka web.'],
                    ['title' => 'Software Engineer',            'period' => '2019 - 2021',     'sub' => 'Pengembangan Web Full-Stack',                     'active' => false, 'body' => 'Mengembangkan portal klien, dashboard manajemen data internal, dan sistem laporan otomatis menggunakan Laravel, MySQL, serta JavaScript modern.'],
                ] as $i => $role)
                    <div class="reveal reveal-delay-{{ $i + 1 }} relative pl-8 sm:pl-10">
                        <span class="absolute -left-[9px] mt-1.5 h-4 w-4 rounded-full {{ $role['active'] ? 'timeline-dot-active' : 'timeline-dot' }}"
                              aria-hidden="true"></span>

                        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-1">
                            <h3 class="text-lg font-semibold tracking-[-0.02em] text-white">{{ $role['title'] }}</h3>
                            <span class="font-mono text-xs text-[#d8d8d8]">{{ $role['period'] }}</span>
                        </div>
                        <p class="mt-1 text-sm font-medium {{ $role['active'] ? 'text-white' : 'text-[#9a9a9a]' }}">{!! $role['sub'] !!}</p>
                        <p class="mt-3 text-sm leading-relaxed text-[#9a9a9a]">{{ $role['body'] }}</p>
                    </div>
                @endforeach
            </div>
        </div>
    </section>

    <!-- ━━━ CONTACT ━━━ -->
    <section id="contact" class="section-divider py-20">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-10">
            <div class="grid grid-cols-1 gap-14 lg:grid-cols-12">

                <!-- Left narrative -->
                <div class="lg:col-span-5 reveal">
                    <h2 class="text-3xl font-semibold tracking-[-0.03em] text-white sm:text-4xl">
                        Mari <em class="vsp-serif font-normal">Terhubung</em>
                    </h2>
                    <p class="mt-4 text-[15px] leading-relaxed text-[#9a9a9a]">
                        Punya ide proyek menarik, butuh bantuan arsitektur sistem web, atau ingin mengajak kolaborasi? Pintu selalu terbuka, silakan tinggalkan pesan dan saya akan membalasnya dengan senang hati.
                    </p>

                    <div class="mt-8 space-y-4">
                        <div class="flex items-center gap-4">
                            <span class="flex h-11 w-11 shrink-0 items-center justify-center rounded-lg border"
                                  style="border-color:rgba(255,255,255,0.14);background:rgba(255,255,255,0.05);color:#fff;" aria-hidden="true">
                                <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                                </svg>
                            </span>
                            <div>
                                <span class="block font-mono text-xs text-[#9a9a9a]">Email Langsung</span>
                                <a href="mailto:gamergoblin889@gmail.com" class="font-medium text-white transition-colors hover:text-[#d8d8d8]">
                                    gamergoblin889@gmail.com
                                </a>
                            </div>
                        </div>

                        <div class="flex items-center gap-4">
                            <span class="flex h-11 w-11 shrink-0 items-center justify-center rounded-lg border"
                                  style="border-color:rgba(255,255,255,0.14);background:rgba(255,255,255,0.05);color:#fff;" aria-hidden="true">
                                <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0zM15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                                </svg>
                            </span>
                            <div>
                                <span class="block font-mono text-xs text-[#9a9a9a]">Domisili</span>
                                <span class="font-medium text-white">Jawa Barat, Indonesia</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Right form card -->
                <div class="lg:col-span-7 reveal reveal-delay-2">
                    <div class="vsp-card p-6 sm:p-8">

                        @if (session('success'))
                            <div tabindex="-1" class="mb-6 rounded-lg border p-4 text-sm font-medium"
                                 style="border-color:rgba(52,211,153,0.3);background:rgba(52,211,153,0.08);color:#6ee7b7;">
                                {{ session('success') }}
                            </div>
                        @endif

                        <div id="contact-form-status" class="hidden mb-6" tabindex="-1"></div>

                        <form id="contact-form" action="{{ route('portfolio.contact.store') }}" method="POST" class="space-y-5" novalidate>
                            @csrf

                            {{-- Honeypot anti-spam field --}}
                            <div class="hidden" aria-hidden="true">
                                <label for="company_hp">Biarkan bidang ini kosong</label>
                                <input type="text" id="company_hp" name="company_hp" tabindex="-1" autocomplete="off">
                            </div>

                            <div class="grid grid-cols-1 gap-5 sm:grid-cols-2">
                                <!-- Name -->
                                <div>
                                    <label for="contact-name" class="block text-xs font-medium uppercase tracking-widest text-[#9a9a9a]">
                                        Nama Anda <span class="text-white" aria-label="wajib diisi">*</span>
                                    </label>
                                    <input type="text" id="contact-name" name="name"
                                           value="{{ old('name') }}" required
                                           placeholder="Tuliskan nama Anda"
                                           class="vsp-input mt-2">
                                    <p data-error-feedback="name" class="mt-1.5 text-xs {{ $errors->has('name') ? '' : 'hidden' }}" style="color:#f87171;">
                                        {{ $errors->first('name') }}
                                    </p>
                                </div>

                                <!-- Email -->
                                <div>
                                    <label for="contact-email" class="block text-xs font-medium uppercase tracking-widest text-[#9a9a9a]">
                                        Alamat Email <span class="text-white" aria-label="wajib diisi">*</span>
                                    </label>
                                    <input type="email" id="contact-email" name="email"
                                           value="{{ old('email') }}" required
                                           placeholder="nama@email.com"
                                           class="vsp-input mt-2">
                                    <p data-error-feedback="email" class="mt-1.5 text-xs {{ $errors->has('email') ? '' : 'hidden' }}" style="color:#f87171;">
                                        {{ $errors->first('email') }}
                                    </p>
                                </div>
                            </div>

                            <!-- Subject -->
                            <div>
                                <label for="contact-subject" class="block text-xs font-medium uppercase tracking-widest text-[#9a9a9a]">
                                    Subjek Pesan <span class="text-white" aria-label="wajib diisi">*</span>
                                </label>
                                <input type="text" id="contact-subject" name="subject"
                                       value="{{ old('subject') }}" required
                                       placeholder="Contoh: Diskusi Proyek Web / Penawaran Kolaborasi"
                                       class="vsp-input mt-2">
                                <p data-error-feedback="subject" class="mt-1.5 text-xs {{ $errors->has('subject') ? '' : 'hidden' }}" style="color:#f87171;">
                                    {{ $errors->first('subject') }}
                                </p>
                            </div>

                            <!-- Message -->
                            <div>
                                <label for="contact-message" class="block text-xs font-medium uppercase tracking-widest text-[#9a9a9a]">
                                    Pesan Anda <span class="text-white" aria-label="wajib diisi">*</span>
                                </label>
                                <textarea id="contact-message" name="message" rows="4" required
                                          placeholder="Ceritakan sedikit tentang proyek, kebutuhan teknis, atau hal yang ingin Anda diskusikan..."
                                          class="vsp-input mt-2 resize-none">{{ old('message') }}</textarea>
                                <p data-error-feedback="message" class="mt-1.5 text-xs {{ $errors->has('message') ? '' : 'hidden' }}" style="color:#f87171;">
                                    {{ $errors->first('message') }}
                                </p>
                            </div>

                            <!-- Submit -->
                            <button type="submit" class="btn btn-solid w-full" style="height:46px;">
                                <svg id="submit-btn-spinner" class="hidden mr-2 h-4 w-4 animate-spin" fill="none" viewBox="0 0 24 24" aria-hidden="true">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                </svg>
                                <span id="submit-btn-text">Kirim Pesan</span>
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </section>

    </main>

    <!-- ━━━ FOOTER ━━━ -->
    <footer class="relative z-10 section-divider py-10" style="background:rgba(0,0,0,0.85);backdrop-filter:blur(12px);">
        <div class="mx-auto flex max-w-7xl flex-col items-center justify-between gap-4 px-4 sm:flex-row sm:px-6 lg:px-10">
            <span class="text-[13.5px] text-[#9a9a9a]">
                <span class="font-medium text-white">Kikii &bull; Full-Stack Web Developer</span>
                &nbsp;&bull;&nbsp; Dibangun dengan Laravel 12 &amp; Tailwind CSS
            </span>
            <div class="flex items-center gap-6 text-[13.5px] text-[#9a9a9a]">
                @foreach (['projects' => 'Proyek', 'architecture' => 'Keahlian', 'experience' => 'Pengalaman', 'contact' => 'Kontak'] as $anchor => $label)
                    <a href="#{{ $anchor }}" class="transition-colors hover:text-white">{{ $label }}</a>
                @endforeach
            </div>
        </div>
    </footer>

    <!-- ━━━ PROJECT MODAL ━━━ -->
    <div id="project-modal" role="dialog" aria-modal="true" aria-labelledby="modal-project-title"
         class="hidden fixed inset-0 z-50 flex items-center justify-center p-4 sm:p-6 lg:p-8"
         style="background:rgba(0,0,0,0.85);backdrop-filter:blur(10px);">

        <div class="relative w-full max-w-3xl max-h-[90vh] overflow-y-auto rounded-2xl border p-6 sm:p-8"
             style="border-color:rgba(255,255,255,0.18);background:#0a0a0a;box-shadow:0 0 60px rgba(255,255,255,0.06);">

            <!-- Close button -->
            <button type="button" data-modal-close aria-label="Tutup detail proyek"
                    class="absolute right-5 top-5 flex h-11 w-11 items-center justify-center rounded-lg border transition-colors hover:border-white/40 hover:text-white"
                    style="border-color:rgba(255,255,255,0.14);color:#9a9a9a;">
                <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>

            <div class="pr-14">
                <span id="modal-project-category" class="cat-badge"></span>
                <h2 id="modal-project-title" class="mt-3 text-2xl font-semibold tracking-[-0.03em] text-white sm:text-3xl"></h2>
                <p id="modal-project-tagline" class="mt-2 text-base text-[#9a9a9a]"></p>
            </div>

            <!-- Metrics banner -->
            <div id="modal-project-metrics-wrapper" class="mt-5 rounded-lg border p-4 font-mono text-xs"
                 style="border-color:rgba(255,255,255,0.14);background:rgba(255,255,255,0.04);color:#d8d8d8;">
                <span class="font-semibold text-white">Info &amp; Performa:</span>
                <span id="modal-project-metrics" class="ml-1"></span>
            </div>

            <!-- Description -->
            <div class="mt-6">
                <h3 class="text-xs font-medium uppercase tracking-widest text-[#9a9a9a]">Ringkasan Proyek</h3>
                <p id="modal-project-description" class="mt-2 text-sm leading-relaxed text-[#d8d8d8]"></p>
            </div>

            <!-- Architecture -->
            <div id="modal-project-architecture-wrapper" class="mt-5 rounded-lg border p-4"
                 style="border-color:rgba(255,255,255,0.12);background:rgba(255,255,255,0.02);">
                <h3 class="font-mono text-xs font-semibold uppercase tracking-widest text-white">Strategi &amp; Arsitektur</h3>
                <p id="modal-project-architecture" class="mt-2 font-mono text-xs leading-relaxed text-[#9a9a9a]"></p>
            </div>

            <!-- Features -->
            <div class="mt-6">
                <h3 class="text-xs font-medium uppercase tracking-widest text-[#9a9a9a]">Fitur Utama</h3>
                <ul id="modal-project-features" class="mt-3 space-y-2.5"></ul>
            </div>

            <!-- Tech stack -->
            <div class="mt-6 border-t pt-5" style="border-color:rgba(255,255,255,0.12);">
                <h3 class="font-mono text-xs font-medium uppercase tracking-widest text-[#9a9a9a]">Teknologi yang Digunakan</h3>
                <div id="modal-project-tech-stack" class="mt-3 flex flex-wrap gap-2"></div>
            </div>

            <!-- Actions -->
            <div class="mt-7 flex flex-wrap items-center justify-end gap-3 border-t pt-5" style="border-color:rgba(255,255,255,0.12);">
                <a id="modal-project-github" href="#" target="_blank" rel="noopener noreferrer"
                   class="btn btn-ghost">
                    <svg class="mr-2 h-4 w-4" fill="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                        <path fill-rule="evenodd" clip-rule="evenodd" d="M12 2C6.477 2 2 6.484 2 12.017c0 4.425 2.865 8.18 6.839 9.504.5.092.682-.217.682-.483 0-.237-.008-.868-.013-1.703-2.782.605-3.369-1.343-3.369-1.343-.454-1.158-1.11-1.466-1.11-1.466-.908-.62.069-.608.069-.608 1.003.07 1.53 1.032 1.53 1.032.892 1.53 2.341 1.088 2.91.832.092-.647.35-1.088.636-1.338-2.22-.253-4.555-1.113-4.555-4.951 0-1.093.39-1.988 1.029-2.688-.103-.253-.446-1.272.098-2.65 0 0 .84-.27 2.75 1.026A9.564 9.564 0 0112 6.844c.85.004 1.705.115 2.504.337 1.909-1.296 2.747-1.027 2.747-1.027.546 1.379.202 2.398.1 2.651.64.7 1.028 1.595 1.028 2.688 0 3.848-2.339 4.695-4.566 4.943.359.309.678.92.678 1.855 0 1.338-.012 2.419-.012 2.747 0 .268.18.58.688.482A10.019 10.019 0 0022 12.017C22 6.484 17.522 2 12 2z"/>
                    </svg>
                    Lihat di GitHub
                </a>
                <a id="modal-project-demo" href="#" target="_blank" rel="noopener noreferrer"
                   class="btn btn-solid">
                    Kunjungi Website / Live Preview
                </a>
            </div>
        </div>
    </div>

</body>
</html>
