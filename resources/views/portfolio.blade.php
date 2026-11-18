<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="Portofolio Kikii - Full-Stack Web Developer yang berfokus pada arsitektur sistem yang tangguh, API berkinerja tinggi, dan antarmuka web modern yang responsif.">
    <title>Kikii | Full-Stack Web Developer</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    {{-- Inline critical: prevent flash of unstyled void background --}}
    <style>
        :root { background: #060412; color: #e9e0ff; }
    </style>
</head>

<body class="antialiased min-h-screen" style="background:#060412;color:#e9e0ff;">

    {{-- SVG radial aura behind hero --}}
    <div class="pointer-events-none fixed inset-0 overflow-hidden" aria-hidden="true">
        <div class="absolute -top-32 left-1/2 -translate-x-1/2 w-[900px] h-[600px] rounded-full opacity-20"
             style="background:radial-gradient(ellipse at 50% 0%, #6b4fa8 0%, transparent 70%);"></div>
    </div>

    <!-- ━━━ HEADER ━━━ -->
    <header class="sticky top-0 z-40 w-full" style="border-bottom:1px solid rgba(107,79,168,0.3);background:rgba(6,4,18,0.85);backdrop-filter:blur(16px);">
        <div class="mx-auto flex h-18 max-w-7xl items-center justify-between px-4 sm:px-6 lg:px-8">

            <!-- Brand -->
            <a href="{{ route('portfolio.index') }}" class="group flex items-center gap-3" aria-label="Halaman Beranda Portofolio Kikii">
                <span class="flex h-10 w-10 items-center justify-center rounded-xl font-mono text-sm font-black"
                      style="background:linear-gradient(135deg,#4f338a 0%,#f7d340 100%);color:#060412;letter-spacing:-0.02em;">
                    K
                </span>
                <span class="flex flex-col">
                    <span class="text-sm font-bold tracking-tight" style="color:#e9e0ff;">Kikii</span>
                    <span class="font-mono text-[10px]" style="color:#9270d0;">Full-Stack Developer</span>
                </span>
            </a>

            <!-- Desktop Nav -->
            <nav aria-label="Navigasi Utama" class="hidden md:flex md:items-center md:gap-7">
                @foreach (['projects' => 'Proyek', 'architecture' => 'Keahlian', 'experience' => 'Pengalaman', 'contact' => 'Kontak'] as $anchor => $label)
                    <a href="#{{ $anchor }}"
                       class="text-sm font-medium transition-colors duration-150"
                       style="color:#c0a8e8;"
                       onmouseover="this.style.color='#f7d340'"
                       onmouseout="this.style.color='#c0a8e8'">
                        {{ $label }}
                    </a>
                @endforeach
            </nav>

            <!-- Controls -->
            <div class="flex items-center gap-3">
                <!-- CTA -->
                <a href="#contact"
                   class="hidden sm:inline-flex items-center justify-center rounded-xl px-4 py-2.5 text-sm font-semibold transition-all duration-200 active:scale-95"
                   style="background:#f7d340;color:#0d0820;"
                   onmouseover="this.style.background='#ffe033';this.style.boxShadow='0 0 18px 2px rgba(247,211,64,0.4)'"
                   onmouseout="this.style.background='#f7d340';this.style.boxShadow='none'">
                    Hubungi Saya
                </a>

                <!-- Mobile menu toggle -->
                <button id="mobile-menu-btn" type="button"
                        aria-expanded="false" aria-controls="mobile-menu"
                        aria-label="Buka menu navigasi"
                        class="flex h-11 w-11 items-center justify-center rounded-xl border md:hidden"
                        style="border-color:rgba(107,79,168,0.5);background:rgba(46,27,92,0.5);color:#c0a8e8;">
                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                    </svg>
                </button>
            </div>
        </div>

        <!-- Mobile Drawer -->
        <div id="mobile-menu" class="hidden border-t md:hidden" style="border-color:rgba(107,79,168,0.3);background:rgba(13,8,32,0.97);">
            <nav class="flex flex-col px-4 py-5 gap-1" aria-label="Navigasi Seluler">
                @foreach (['projects' => 'Proyek', 'architecture' => 'Keahlian', 'experience' => 'Pengalaman', 'contact' => 'Kontak'] as $anchor => $label)
                    <a href="#{{ $anchor }}"
                       class="flex h-12 items-center rounded-xl px-4 text-base font-medium transition-colors"
                       style="color:#c0a8e8;"
                       onmouseover="this.style.background='rgba(79,51,138,0.4)';this.style.color='#f7d340'"
                       onmouseout="this.style.background='transparent';this.style.color='#c0a8e8'">
                        {{ $label }}
                    </a>
                @endforeach
            </nav>
        </div>
    </header>

    <main id="main-content">

    <!-- ━━━ HERO ━━━ -->
    <section class="relative overflow-hidden pt-16 pb-24 md:pt-24 md:pb-32">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 items-center gap-14 lg:grid-cols-12">

                <!-- Left column -->
                <div class="lg:col-span-7 reveal">
                    <!-- Availability indicator -->
                    <div class="mb-6 inline-flex items-center gap-2.5 rounded-lg border px-3.5 py-1.5 text-xs font-mono font-semibold"
                         style="border-color:rgba(247,211,64,0.3);background:rgba(247,211,64,0.08);color:#f7d340;">
                        <span class="relative flex h-2 w-2">
                            <span class="absolute inline-flex h-full w-full animate-ping rounded-full opacity-60" style="background:#f7d340;animation-duration:2.4s;"></span>
                            <span class="relative inline-flex h-2 w-2 rounded-full" style="background:#f7d340;"></span>
                        </span>
                        Terbuka untuk Peluang Kerja &amp; Kolaborasi Proyek
                    </div>

                    <!-- H1 -->
                    <h1 class="text-4xl font-extrabold tracking-tight sm:text-5xl lg:text-6xl" style="color:#f0ebff;line-height:1.15;">
                        Selamat datang, wahai
                        <span class="relative block sm:inline">
                            <span style="color:#f7d340;">Kikiawan dan Kikiawati!</span>
                        </span>
                        <br><span class="text-2xl sm:text-3xl lg:text-4xl font-medium" style="color:#c0a8e8;">Silakan menikmati portofolio singkat ini ^_^</span>
                    </h1>

                    <p class="mt-6 max-w-xl text-lg leading-relaxed" style="color:#a89bc2;">
                        Halo! Saya <strong style="color:#f0ebff;">Kikii</strong>, seorang Full-Stack Developer yang berfokus merancang aplikasi web yang rapi, cepat, dan mudah dipelihara. Mengembangkan solusi digital menggunakan ekosistem <strong style="color:#f7d340;">Laravel</strong>, <strong style="color:#f7d340;">PHP</strong>, serta JavaScript modern dengan arsitektur data yang terstruktur.
                    </p>

                    <!-- CTAs -->
                    <div class="mt-9 flex flex-wrap items-center gap-4">
                        <a href="#projects"
                           class="inline-flex min-h-[44px] items-center justify-center rounded-xl px-6 py-3 text-base font-semibold transition-all duration-200 active:scale-95"
                           style="background:#f7d340;color:#0d0820;"
                           onmouseover="this.style.background='#ffe033';this.style.boxShadow='0 0 22px 3px rgba(247,211,64,0.45)'"
                           onmouseout="this.style.background='#f7d340';this.style.boxShadow='none'">
                            Lihat Hasil Karya
                        </a>
                        <a href="#contact"
                           class="inline-flex min-h-[44px] items-center justify-center rounded-xl border px-6 py-3 text-base font-semibold transition-all duration-200"
                           style="border-color:rgba(107,79,168,0.6);color:#c0a8e8;"
                           onmouseover="this.style.borderColor='rgba(192,132,252,0.7)';this.style.color='#e9e0ff';this.style.background='rgba(79,51,138,0.2)'"
                           onmouseout="this.style.borderColor='rgba(107,79,168,0.6)';this.style.color='#c0a8e8';this.style.background='transparent'">
                            Mari Berkolaborasi
                        </a>
                    </div>

                    <!-- Stack chips -->
                    <div class="mt-10 border-t pt-6" style="border-color:rgba(107,79,168,0.25);">
                        <p class="mb-3 font-mono text-xs font-semibold uppercase tracking-widest" style="color:#9270d0;">Teknologi Utama</p>
                        <div class="flex flex-wrap gap-2">
                            @foreach (['Laravel 12', 'PHP 8.5', 'PostgreSQL / MySQL', 'Redis Streams', 'Tailwind CSS', 'JavaScript & Alpine.js', 'Docker'] as $tech)
                                <span class="rounded-lg border px-3 py-1 font-mono text-xs"
                                      style="border-color:rgba(107,79,168,0.4);background:rgba(46,27,92,0.5);color:#c0a8e8;">
                                    {{ $tech }}
                                </span>
                            @endforeach
                        </div>
                    </div>
                </div>

                <!-- Right: Spec card -->
                <div class="lg:col-span-5 reveal reveal-delay-2">
                    <div class="rounded-2xl border p-6"
                         style="border-color:rgba(107,79,168,0.4);background:rgba(22,13,48,0.8);box-shadow:0 0 48px 4px rgba(107,79,168,0.12);">
                        <div class="flex items-center justify-between border-b pb-4" style="border-color:rgba(107,79,168,0.25);">
                            <div class="flex gap-1.5">
                                @foreach (['#c0a8e8', '#6b4fa8', '#2e1b5c'] as $dot)
                                    <span class="h-3 w-3 rounded-full" style="background:{{ $dot }};" aria-hidden="true"></span>
                                @endforeach
                            </div>
                            <span class="font-mono text-xs" style="color:#6b4fa8;">ringkasan_sistem.json</span>
                        </div>

                        <div class="mt-5 space-y-4 font-mono text-sm">
                            <div class="rounded-xl p-4" style="background:rgba(6,4,18,0.7);">
                                <span style="color:#f7d340;" class="font-semibold">$ cek_arsitektur --rinci</span>
                                <p class="mt-1.5 text-xs leading-relaxed" style="color:#9270d0;">
                                    Struktur aplikasi modular, antrean latar belakang yang efisien, dan pengecekan tipe kode yang ketat untuk menjaga keandalan sistem.
                                </p>
                            </div>

                            <div class="grid grid-cols-2 gap-3">
                                <div class="rounded-xl border p-3.5" style="border-color:rgba(107,79,168,0.3);background:rgba(22,13,48,0.6);">
                                    <span class="text-xs" style="color:#9270d0;">Pengujian Kode</span>
                                    <p class="mt-1 text-sm font-bold" style="color:#e9e0ff;">PHPUnit Suite</p>
                                    <p class="mt-0.5 text-[11px]" style="color:#c0a8e8;">Feature &amp; Unit Test</p>
                                </div>
                                <div class="rounded-xl border p-3.5" style="border-color:rgba(107,79,168,0.3);background:rgba(22,13,48,0.6);">
                                    <span class="text-xs" style="color:#9270d0;">Standar Kode</span>
                                    <p class="mt-1 text-sm font-bold" style="color:#e9e0ff;">PSR-12 / Pint</p>
                                    <p class="mt-0.5 text-[11px]" style="color:#c0a8e8;">Strict types aktif</p>
                                </div>
                            </div>

                            <div class="rounded-xl border p-3.5" style="border-color:rgba(107,79,168,0.3);background:rgba(22,13,48,0.6);">
                                <span class="text-xs" style="color:#9270d0;">Lingkungan Kerja</span>
                                <p class="mt-1 text-xs" style="color:#c0a8e8;">
                                    PHP 8.5 &nbsp;|&nbsp; Laravel 12 &nbsp;|&nbsp; MySQL / SQLite &nbsp;|&nbsp; Vite Pipeline
                                </p>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </section>

    <!-- ━━━ PROJECTS ━━━ -->
    <section id="projects" class="border-t py-20" style="border-color:rgba(107,79,168,0.2);background:rgba(13,8,32,0.6);">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">

            <!-- Section header -->
            <div class="reveal flex flex-col md:flex-row md:items-end md:justify-between gap-6">
                <div>
                    <h2 class="text-3xl font-extrabold tracking-tight sm:text-4xl" style="color:#f0ebff;">
                        Karya &amp; Proyek Pilihan
                    </h2>
                    <p class="mt-3 max-w-xl text-base" style="color:#9270d0;">
                        Kumpulan aplikasi web nyata, integrasi sistem, dan karya digital yang dibangun dengan perhatian pada kualitas performa dan estetika.
                    </p>
                </div>

                <!-- Filter tabs -->
                <div class="flex flex-wrap gap-2" role="tablist" aria-label="Kategori Proyek">
                    @php
                        $translatedTabs = [
                            'all' => 'Semua Karya',
                            'full-stack' => 'Aplikasi Web',
                            'backend-api' => 'Backend & API',
                            'dev-tool' => 'Alat & Utilitas',
                        ];
                    @endphp
                    @foreach ($categories as $key => $label)
                        <button type="button" role="tab"
                                aria-selected="{{ $key === 'all' ? 'true' : 'false' }}"
                                data-category="{{ $key }}"
                                class="project-filter-tab min-h-[44px] rounded-xl px-4 py-2 text-xs font-semibold tracking-wide transition-all duration-200"
                                style="{{ $key === 'all'
                                    ? 'background:#f7d340;color:#0d0820;box-shadow:0 0 10px 1px rgba(247,211,64,0.22);'
                                    : 'background:rgba(46,27,92,0.6);color:#c0a8e8;border:1px solid rgba(107,79,168,0.35);' }}">
                            {{ $translatedTabs[$key] ?? $label }}
                        </button>
                    @endforeach
                </div>
            </div>

            <!-- Featured Showcase: Paksadita Paksi -->
            <div class="reveal mt-10 rounded-2xl border p-6 sm:p-8"
                 style="border-color:rgba(247,211,64,0.35);background:radial-gradient(ellipse at top right, rgba(79,51,138,0.3) 0%, rgba(22,13,48,0.9) 70%);box-shadow:0 0 40px 4px rgba(247,211,64,0.08);">
                <div class="grid grid-cols-1 gap-8 lg:grid-cols-12 lg:items-center">
                    <div class="lg:col-span-7">
                        <div class="flex flex-wrap items-center gap-2.5">
                            <span class="inline-flex items-center gap-1.5 rounded-lg px-3 py-1 font-mono text-xs font-bold"
                                  style="background:rgba(247,211,64,0.18);color:#f7d340;border:1px solid rgba(247,211,64,0.4);">
                                ★ PROYEK UNGGULAN
                            </span>
                            <span class="inline-flex items-center rounded-lg px-2.5 py-1 font-mono text-xs font-semibold"
                                  style="background:rgba(192,132,252,0.12);color:#c084fc;border:1px solid rgba(192,132,252,0.25);">
                                Web Interaktif &amp; Portofolio
                            </span>
                        </div>

                        <h3 class="mt-4 text-2xl sm:text-3xl font-extrabold tracking-tight" style="color:#f0ebff;">
                            Paksadita Paksi
                        </h3>
                        <p class="mt-2 text-base leading-relaxed" style="color:#c0a8e8;">
                            Platform web interaktif dan portofolio digital yang menyajikan pengalaman visual memikat, navigasi yang responsif, serta performa pemuatan yang cepat di berbagai ukuran layar.
                        </p>

                        <div class="mt-4 flex flex-wrap gap-2">
                            @foreach (['Laravel', 'PHP', 'Tailwind CSS', 'JavaScript', 'Responsive Web'] as $tech)
                                <span class="rounded-md border px-2.5 py-1 font-mono text-xs"
                                      style="border-color:rgba(107,79,168,0.4);background:rgba(6,4,18,0.7);color:#f7d340;">
                                    {{ $tech }}
                                </span>
                            @endforeach
                        </div>

                        <div class="mt-6 flex flex-wrap items-center gap-3">
                            <a href="https://mrizqys.web.id" target="_blank" rel="noopener noreferrer"
                               class="inline-flex min-h-[44px] items-center gap-2 rounded-xl px-5 py-2.5 text-sm font-bold transition-all active:scale-95"
                               style="background:#f7d340;color:#0d0820;"
                               onmouseover="this.style.background='#ffe033';this.style.boxShadow='0 0 20px 2px rgba(247,211,64,0.4)'"
                               onmouseout="this.style.background='#f7d340';this.style.boxShadow='none'">
                                <span>Buka Live Preview (mrizqys.web.id)</span>
                                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/>
                                </svg>
                            </a>
                        </div>
                    </div>

                    <div class="lg:col-span-5">
                        <div class="rounded-xl border p-4 sm:p-5" style="border-color:rgba(107,79,168,0.4);background:rgba(6,4,18,0.75);">
                            <div class="flex items-center justify-between border-b pb-3" style="border-color:rgba(107,79,168,0.25);">
                                <span class="font-mono text-xs font-semibold" style="color:#f7d340;">Pratinjau Situs</span>
                                <span class="font-mono text-[11px]" style="color:#9270d0;">mrizqys.web.id</span>
                            </div>
                            <div class="mt-4 space-y-3 font-mono text-xs">
                                <div class="flex items-center justify-between">
                                    <span style="color:#9270d0;">Status Web:</span>
                                    <span class="inline-flex items-center gap-1.5 font-bold" style="color:#34d399;">
                                        <span class="h-2 w-2 rounded-full bg-emerald-400"></span> Online &amp; Aktif
                                    </span>
                                </div>
                                <div class="flex items-center justify-between">
                                    <span style="color:#9270d0;">Domain:</span>
                                    <span style="color:#e9e0ff;">mrizqys.web.id</span>
                                </div>
                                <div class="flex items-center justify-between">
                                    <span style="color:#9270d0;">Aksesibilitas:</span>
                                    <span style="color:#f7d340;">Mobile &amp; Desktop Ready</span>
                                </div>
                            </div>
                            <a href="https://mrizqys.web.id" target="_blank" rel="noopener noreferrer"
                               class="mt-4 flex w-full items-center justify-center gap-2 rounded-lg border py-2 text-xs font-semibold transition-colors"
                               style="border-color:rgba(247,211,64,0.3);color:#f7d340;"
                               onmouseover="this.style.background='rgba(247,211,64,0.1)'"
                               onmouseout="this.style.background='transparent'">
                                Kunjungi Situs Sekarang &rarr;
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Projects Grid -->
            @php
                // Jika database kosong, sediakan data fallback berbahasa Indonesia lengkap dengan Paksadita Paksi
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
                        class="project-card reveal reveal-delay-{{ min($index + 1, 4) }} flex flex-col rounded-2xl border transition-all duration-300"
                        style="border-color:rgba(107,79,168,0.3);background:rgba(22,13,48,0.75);"
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
                        onmouseover="this.style.borderColor='rgba(192,132,252,0.5)';this.style.boxShadow='0 0 32px 4px rgba(107,79,168,0.15)'"
                        onmouseout="this.style.borderColor='rgba(107,79,168,0.3)';this.style.boxShadow='none'"
                    >
                        <div class="flex flex-1 flex-col p-6">
                            <!-- Category + metric -->
                            <div class="flex items-center justify-between gap-2">
                                <span class="inline-flex items-center rounded-lg px-2.5 py-1 font-mono text-xs font-semibold"
                                      style="background:rgba(192,132,252,0.12);color:#c084fc;border:1px solid rgba(192,132,252,0.25);">
                                    {{ $project->category_label ?? $project->category }}
                                </span>
                                @if (!empty($project->system_metrics))
                                    <span class="max-w-[160px] truncate font-mono text-[10px]" style="color:#9270d0;" title="{{ $project->system_metrics }}">
                                        {{ $project->system_metrics }}
                                    </span>
                                @endif
                            </div>

                            <!-- Title & tagline -->
                            <h3 class="mt-4 text-xl font-bold tracking-tight" style="color:#f0ebff;">{{ $project->title }}</h3>
                            <p class="mt-2 text-sm leading-relaxed" style="color:#9270d0;">{{ $project->tagline }}</p>

                            <!-- Tech chips (up to 4) -->
                            <div class="mt-4 flex flex-wrap gap-1.5">
                                @foreach (array_slice($project->tech_stack ?? [], 0, 4) as $tech)
                                    <span class="rounded-md border px-2 py-0.5 font-mono text-[11px]"
                                          style="border-color:rgba(107,79,168,0.35);background:rgba(6,4,18,0.6);color:#9270d0;">
                                        {{ $tech }}
                                    </span>
                                @endforeach
                            </div>
                        </div>

                        <!-- Card footer with Case Study & Direct Live Preview -->
                        <div class="border-t px-6 py-4" style="border-color:rgba(107,79,168,0.2);">
                            <div class="flex items-center justify-between gap-2">
                                <button type="button" data-open-project-modal
                                        class="inline-flex min-h-[44px] items-center gap-1.5 text-sm font-semibold transition-colors"
                                        style="color:#f7d340;"
                                        onmouseover="this.style.color='#ffe033'"
                                        onmouseout="this.style.color='#f7d340'">
                                    Detail Proyek
                                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                                    </svg>
                                </button>

                                <div class="flex items-center gap-2">
                                    @if (!empty($project->demo_url))
                                        <a href="{{ $project->demo_url }}"
                                           target="_blank" rel="noopener noreferrer"
                                           class="inline-flex min-h-[38px] items-center gap-1.5 rounded-lg px-3 py-1.5 text-xs font-semibold transition-all active:scale-95"
                                           style="background:rgba(247,211,64,0.15);color:#f7d340;border:1px solid rgba(247,211,64,0.35);"
                                           onmouseover="this.style.background='#f7d340';this.style.color='#0d0820'"
                                           onmouseout="this.style.background='rgba(247,211,64,0.15)';this.style.color='#f7d340'">
                                            <span>Live Preview</span>
                                            <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/>
                                            </svg>
                                        </a>
                                    @endif

                                    @if (!empty($project->github_url))
                                        <a href="{{ $project->github_url }}"
                                           target="_blank" rel="noopener noreferrer"
                                           aria-label="Lihat kode sumber {{ $project->title }} di GitHub"
                                           class="flex h-11 w-11 items-center justify-center rounded-xl border transition-all"
                                           style="border-color:rgba(107,79,168,0.35);color:#9270d0;"
                                           onmouseover="this.style.borderColor='rgba(192,132,252,0.5)';this.style.color='#c0a8e8';this.style.background='rgba(79,51,138,0.3)'"
                                           onmouseout="this.style.borderColor='rgba(107,79,168,0.35)';this.style.color='#9270d0';this.style.background='transparent'">
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
                    <div class="col-span-full rounded-2xl border border-dashed p-12 text-center" style="border-color:rgba(107,79,168,0.3);">
                        <p class="font-semibold" style="color:#f0ebff;">Belum ada proyek di kategori ini.</p>
                        <p class="mt-1 text-sm" style="color:#6b4fa8;">Silakan pilih tab kategori lainnya untuk melihat karya saya.</p>
                    </div>
                @endforelse
            </div>

            <!-- Filter empty state -->
            <div id="projects-empty-state" class="hidden mt-8 rounded-2xl border border-dashed p-12 text-center" style="border-color:rgba(107,79,168,0.3);">
                <p class="font-semibold" style="color:#f0ebff;">Belum ada proyek di kategori ini.</p>
                <p class="mt-1 text-sm" style="color:#6b4fa8;">Silakan klik kategori lainnya untuk melihat hasil karya.</p>
            </div>
        </div>
    </section>

    <!-- ━━━ ARCHITECTURE / SKILLS ━━━ -->
    <section id="architecture" class="border-t py-20" style="border-color:rgba(107,79,168,0.2);">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">

            <div class="reveal mb-14 max-w-2xl">
                <h2 class="text-3xl font-extrabold tracking-tight sm:text-4xl" style="color:#f0ebff;">
                    Keahlian &amp; Pendekatan Teknis
                </h2>
                <p class="mt-3 text-base" style="color:#9270d0;">
                    Prinsip kerja saya dalam membangun sistem: terstruktur, mudah dipelihara, dan memiliki performa nyata yang terukur.
                </p>
            </div>

            <!-- 3-col staggered cards -->
            <div class="grid grid-cols-1 gap-6 md:grid-cols-3">

                <!-- Card 1 -->
                <div class="reveal reveal-delay-1 rounded-2xl border p-6"
                     style="border-color:rgba(107,79,168,0.35);background:rgba(22,13,48,0.75);">
                    <div class="flex h-12 w-12 items-center justify-center rounded-xl"
                         style="background:rgba(192,132,252,0.12);color:#c084fc;">
                        <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 7v10c0 2.21 3.582 4 8 4s8-1.79 8-4V7M4 7c0 2.21 3.582 4 8 4s8-1.79 8-4M4 7c0-2.21 3.582-4 8-4s8 1.79 8 4"/>
                        </svg>
                    </div>
                    <h3 class="mt-5 text-lg font-bold" style="color:#f0ebff;">Backend Cepat &amp; Tangguh</h3>
                    <p class="mt-2 text-sm leading-relaxed" style="color:#9270d0;">
                        Optimalisasi database, caching efisien dengan Redis, dan pemrosesan antrean latar belakang agar performa web tetap prima.
                    </p>
                    <ul class="mt-5 space-y-2">
                        @foreach (['Struktur &amp; Indeks Relasi Database Efisien', 'Redis Streams &amp; Manajemen Caching', 'Arsitektur RESTful API yang Bersih &amp; Aman'] as $item)
                            <li class="flex items-center gap-2.5 font-mono text-xs" style="color:#c0a8e8;">
                                <span class="h-1.5 w-1.5 shrink-0 rounded-full" style="background:#f7d340;" aria-hidden="true"></span>
                                {!! $item !!}
                            </li>
                        @endforeach
                    </ul>
                </div>

                <!-- Card 2 (gold accent) -->
                <div class="reveal reveal-delay-2 rounded-2xl border p-6"
                     style="border-color:rgba(247,211,64,0.3);background:rgba(22,13,48,0.75);">
                    <div class="flex h-12 w-12 items-center justify-center rounded-xl"
                         style="background:rgba(247,211,64,0.12);color:#f7d340;">
                        <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M10 20l4-16m4 4l4 4-4 4M6 16l-4-4 4-4"/>
                        </svg>
                    </div>
                    <h3 class="mt-5 text-lg font-bold" style="color:#f7d340;">Antarmuka Web yang Nyaman</h3>
                    <p class="mt-2 text-sm leading-relaxed" style="color:#9270d0;">
                        Penerapan HTML5 semantik, kontras warna yang nyaman di mata, aksesibilitas ramah pengguna, dan tata letak responsif di setiap perangkat.
                    </p>
                    <ul class="mt-5 space-y-2">
                        @foreach (['Desain Responsif &amp; Modern dengan Tailwind CSS', 'Aksesibilitas &amp; Struktur Navigasi Ramah Pengguna', 'Formulir Interaktif dengan Validasi Cepat'] as $item)
                            <li class="flex items-center gap-2.5 font-mono text-xs" style="color:#c0a8e8;">
                                <span class="h-1.5 w-1.5 shrink-0 rounded-full" style="background:#f7d340;" aria-hidden="true"></span>
                                {!! $item !!}
                            </li>
                        @endforeach
                    </ul>
                </div>

                <!-- Card 3 -->
                <div class="reveal reveal-delay-3 rounded-2xl border p-6"
                     style="border-color:rgba(107,79,168,0.35);background:rgba(22,13,48,0.75);">
                    <div class="flex h-12 w-12 items-center justify-center rounded-xl"
                         style="background:rgba(192,132,252,0.12);color:#c084fc;">
                        <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                        </svg>
                    </div>
                    <h3 class="mt-5 text-lg font-bold" style="color:#f0ebff;">Kualitas Kode &amp; Pengujian</h3>
                    <p class="mt-2 text-sm leading-relaxed" style="color:#9270d0;">
                        Pengujian otomatis menggunakan PHPUnit, penegakan standar kode PSR, dan workflow terstandarisasi untuk mencegah potensi bug.
                    </p>
                    <ul class="mt-5 space-y-2">
                        @foreach (['Automated Testing PHPUnit (Feature &amp; Unit)', 'Standarisasi Kode dengan Laravel Pint', 'Konfigurasi Docker yang Konsisten &amp; Reusable'] as $item)
                            <li class="flex items-center gap-2.5 font-mono text-xs" style="color:#c0a8e8;">
                                <span class="h-1.5 w-1.5 shrink-0 rounded-full" style="background:#f7d340;" aria-hidden="true"></span>
                                {!! $item !!}
                            </li>
                        @endforeach
                    </ul>
                </div>
            </div>
        </div>
    </section>

    <!-- ━━━ EXPERIENCE ━━━ -->
    <section id="experience" class="border-t py-20" style="border-color:rgba(107,79,168,0.2);background:rgba(13,8,32,0.5);">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">

            <div class="reveal mb-14 max-w-2xl">
                <h2 class="text-3xl font-extrabold tracking-tight sm:text-4xl" style="color:#f0ebff;">
                    Pengalaman &amp; Perjalanan Kerja
                </h2>
                <p class="mt-3 text-base" style="color:#9270d0;">
                    Rekam jejak dalam membangun sistem perangkat lunak, merancang antarmuka, dan memberikan solusi teknologi yang berdampak nyata.
                </p>
            </div>

            <!-- Timeline -->
            <div class="ml-2 space-y-10 border-l sm:ml-4" style="border-color:rgba(107,79,168,0.3);">

                @foreach ([
                    ['title' => 'Senior Full-Stack Engineer',   'period' => '2023 — Sekarang', 'sub' => 'Sistem Terdistribusi &amp; Aplikasi Web Modern', 'gold' => true,  'body' => 'Merancang dan memimpin arsitektur sistem berbasis Laravel modular. Mengoptimalkan antrean latar belakang Redis untuk memproses ratusan ribu tugas terjadwal harian serta meningkatkan kecepatan respon sistem.'],
                    ['title' => 'Backend Laravel Developer',    'period' => '2021 — 2023',     'sub' => 'Layanan API &amp; Arsitektur Database',           'gold' => false, 'body' => 'Membangun integrasi gateway pembayaran yang aman, sistem webhook otomatis, serta RESTful API terstruktur untuk kebutuhan aplikasi mobile dan antarmuka web.'],
                    ['title' => 'Software Engineer',            'period' => '2019 — 2021',     'sub' => 'Pengembangan Web Full-Stack',                     'gold' => false, 'body' => 'Mengembangkan portal klien, dashboard manajemen data internal, dan sistem laporan otomatis menggunakan Laravel, MySQL, serta JavaScript modern.'],
                ] as $i => $role)
                    <div class="reveal reveal-delay-{{ $i + 1 }} relative pl-8 sm:pl-10">
                        <span class="absolute -left-[9px] mt-1.5 h-4 w-4 rounded-full border-2"
                              style="background:{{ $role['gold'] ? '#f7d340' : '#2e1b5c' }};border-color:{{ $role['gold'] ? '#0d0820' : '#6b4fa8' }};{{ $role['gold'] ? 'box-shadow:0 0 10px 2px rgba(247,211,64,0.3);' : '' }}"
                              aria-hidden="true"></span>

                        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-1">
                            <h3 class="text-lg font-bold" style="color:#f0ebff;">{{ $role['title'] }}</h3>
                            <span class="font-mono text-xs" style="color:#f7d340;">{{ $role['period'] }}</span>
                        </div>
                        <p class="mt-1 text-sm font-semibold" style="color:{{ $role['gold'] ? '#f7d340' : '#9270d0' }};">{!! $role['sub'] !!}</p>
                        <p class="mt-3 text-sm leading-relaxed" style="color:#c0a8e8;">{{ $role['body'] }}</p>
                    </div>
                @endforeach
            </div>
        </div>
    </section>

    <!-- ━━━ CONTACT ━━━ -->
    <section id="contact" class="border-t py-20" style="border-color:rgba(107,79,168,0.2);">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 gap-14 lg:grid-cols-12">

                <!-- Left narrative -->
                <div class="lg:col-span-5 reveal">
                    <h2 class="text-3xl font-extrabold tracking-tight sm:text-4xl" style="color:#f0ebff;">
                        Mari Terhubung
                    </h2>
                    <p class="mt-4 text-base leading-relaxed" style="color:#9270d0;">
                        Punya ide proyek menarik, butuh bantuan arsitektur sistem web, atau ingin mengajak kolaborasi? Pintu selalu terbuka, silakan tinggalkan pesan dan saya akan membalasnya dengan senang hati.
                    </p>

                    <div class="mt-8 space-y-4">
                        <div class="flex items-center gap-4">
                            <span class="flex h-11 w-11 shrink-0 items-center justify-center rounded-xl"
                                  style="background:rgba(192,132,252,0.1);color:#c084fc;" aria-hidden="true">
                                <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                                </svg>
                            </span>
                            <div>
                                <span class="block font-mono text-xs" style="color:#9270d0;">Email Langsung</span>
                                <a href="mailto:gamergoblin889@gmail.com" class="font-semibold transition-colors" style="color:#e9e0ff;"
                                   onmouseover="this.style.color='#f7d340'" onmouseout="this.style.color='#e9e0ff'">
                                    gamergoblin889@gmail.com
                                </a>
                            </div>
                        </div>

                        <div class="flex items-center gap-4">
                            <span class="flex h-11 w-11 shrink-0 items-center justify-center rounded-xl"
                                  style="background:rgba(192,132,252,0.1);color:#c084fc;" aria-hidden="true">
                                <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0zM15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                                </svg>
                            </span>
                            <div>
                                <span class="block font-mono text-xs" style="color:#9270d0;">Domisili</span>
                                <span class="font-semibold" style="color:#e9e0ff;">Jawa Barat, Indonesia</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Right form card -->
                <div class="lg:col-span-7 reveal reveal-delay-2">
                    <div class="rounded-2xl border p-6 sm:p-8"
                         style="border-color:rgba(107,79,168,0.4);background:rgba(22,13,48,0.8);">

                        @if (session('success'))
                            <div tabindex="-1" class="mb-6 rounded-xl border p-4 text-sm font-medium"
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
                                    <label for="contact-name" class="block text-xs font-semibold uppercase tracking-widest" style="color:#9270d0;">
                                        Nama Anda <span style="color:#f7d340;" aria-label="wajib diisi">*</span>
                                    </label>
                                    <input type="text" id="contact-name" name="name"
                                           value="{{ old('name') }}" required
                                           placeholder="Tuliskan nama Anda"
                                           class="mt-2 block w-full rounded-xl border px-4 py-3 text-sm transition placeholder:text-opacity-40 focus:outline-none focus:ring-2"
                                           style="border-color:rgba(107,79,168,0.4);background:rgba(6,4,18,0.7);color:#e9e0ff;"
                                           onfocus="this.style.borderColor='rgba(247,211,64,0.5)';this.style.boxShadow='0 0 0 2px rgba(247,211,64,0.12)'"
                                           onblur="this.style.borderColor='rgba(107,79,168,0.4)';this.style.boxShadow='none'">
                                    <p data-error-feedback="name" class="mt-1.5 text-xs {{ $errors->has('name') ? '' : 'hidden' }}" style="color:#f87171;">
                                        {{ $errors->first('name') }}
                                    </p>
                                </div>

                                <!-- Email -->
                                <div>
                                    <label for="contact-email" class="block text-xs font-semibold uppercase tracking-widest" style="color:#9270d0;">
                                        Alamat Email <span style="color:#f7d340;" aria-label="wajib diisi">*</span>
                                    </label>
                                    <input type="email" id="contact-email" name="email"
                                           value="{{ old('email') }}" required
                                           placeholder="nama@email.com"
                                           class="mt-2 block w-full rounded-xl border px-4 py-3 text-sm transition focus:outline-none"
                                           style="border-color:rgba(107,79,168,0.4);background:rgba(6,4,18,0.7);color:#e9e0ff;"
                                           onfocus="this.style.borderColor='rgba(247,211,64,0.5)';this.style.boxShadow='0 0 0 2px rgba(247,211,64,0.12)'"
                                           onblur="this.style.borderColor='rgba(107,79,168,0.4)';this.style.boxShadow='none'">
                                    <p data-error-feedback="email" class="mt-1.5 text-xs {{ $errors->has('email') ? '' : 'hidden' }}" style="color:#f87171;">
                                        {{ $errors->first('email') }}
                                    </p>
                                </div>
                            </div>

                            <!-- Subject -->
                            <div>
                                <label for="contact-subject" class="block text-xs font-semibold uppercase tracking-widest" style="color:#9270d0;">
                                    Subjek Pesan <span style="color:#f7d340;" aria-label="wajib diisi">*</span>
                                </label>
                                <input type="text" id="contact-subject" name="subject"
                                       value="{{ old('subject') }}" required
                                       placeholder="Contoh: Diskusi Proyek Web / Penawaran Kolaborasi"
                                       class="mt-2 block w-full rounded-xl border px-4 py-3 text-sm transition focus:outline-none"
                                       style="border-color:rgba(107,79,168,0.4);background:rgba(6,4,18,0.7);color:#e9e0ff;"
                                       onfocus="this.style.borderColor='rgba(247,211,64,0.5)';this.style.boxShadow='0 0 0 2px rgba(247,211,64,0.12)'"
                                       onblur="this.style.borderColor='rgba(107,79,168,0.4)';this.style.boxShadow='none'">
                                <p data-error-feedback="subject" class="mt-1.5 text-xs {{ $errors->has('subject') ? '' : 'hidden' }}" style="color:#f87171;">
                                    {{ $errors->first('subject') }}
                                </p>
                            </div>

                            <!-- Message -->
                            <div>
                                <label for="contact-message" class="block text-xs font-semibold uppercase tracking-widest" style="color:#9270d0;">
                                    Pesan Anda <span style="color:#f7d340;" aria-label="wajib diisi">*</span>
                                </label>
                                <textarea id="contact-message" name="message" rows="4" required
                                          placeholder="Ceritakan sedikit tentang proyek, kebutuhan teknis, atau hal yang ingin Anda diskusikan..."
                                          class="mt-2 block w-full rounded-xl border px-4 py-3 text-sm transition focus:outline-none resize-none"
                                          style="border-color:rgba(107,79,168,0.4);background:rgba(6,4,18,0.7);color:#e9e0ff;"
                                          onfocus="this.style.borderColor='rgba(247,211,64,0.5)';this.style.boxShadow='0 0 0 2px rgba(247,211,64,0.12)'"
                                          onblur="this.style.borderColor='rgba(107,79,168,0.4)';this.style.boxShadow='none'">{{ old('message') }}</textarea>
                                <p data-error-feedback="message" class="mt-1.5 text-xs {{ $errors->has('message') ? '' : 'hidden' }}" style="color:#f87171;">
                                    {{ $errors->first('message') }}
                                </p>
                            </div>

                            <!-- Submit -->
                            <button type="submit"
                                    class="inline-flex min-h-[44px] w-full items-center justify-center gap-2 rounded-xl px-6 py-3 text-sm font-semibold transition-all duration-200 active:scale-[0.98] disabled:opacity-60"
                                    style="background:#f7d340;color:#0d0820;"
                                    onmouseover="if(!this.disabled){this.style.background='#ffe033';this.style.boxShadow='0 0 22px 3px rgba(247,211,64,0.4)'}"
                                    onmouseout="this.style.background='#f7d340';this.style.boxShadow='none'">
                                <svg id="submit-btn-spinner" class="hidden h-4 w-4 animate-spin" fill="none" viewBox="0 0 24 24" aria-hidden="true">
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
    <footer class="border-t py-10" style="border-color:rgba(107,79,168,0.2);background:rgba(6,4,18,0.8);">
        <div class="mx-auto flex max-w-7xl flex-col items-center justify-between gap-4 px-4 sm:flex-row sm:px-6 lg:px-8">
            <span class="text-sm" style="color:#9270d0;">
                <span style="color:#c0a8e8;font-weight:600;">Kikii &bull; Full-Stack Web Developer</span>
                &nbsp;&bull;&nbsp; Dibangun dengan Laravel 12 &amp; Tailwind CSS
            </span>
            <div class="flex items-center gap-6 text-sm" style="color:#9270d0;">
                @foreach (['projects' => 'Proyek', 'architecture' => 'Keahlian', 'experience' => 'Pengalaman', 'contact' => 'Kontak'] as $anchor => $label)
                    <a href="#{{ $anchor }}" class="transition-colors"
                       onmouseover="this.style.color='#f7d340'" onmouseout="this.style.color='#9270d0'">{{ $label }}</a>
                @endforeach
            </div>
        </div>
    </footer>

    <!-- ━━━ PROJECT MODAL ━━━ -->
    <div id="project-modal" role="dialog" aria-modal="true" aria-labelledby="modal-project-title"
         class="hidden fixed inset-0 z-50 flex items-center justify-center p-4 sm:p-6 lg:p-8"
         style="background:rgba(6,4,18,0.85);backdrop-filter:blur(8px);">

        <div class="relative w-full max-w-3xl max-h-[90vh] overflow-y-auto rounded-2xl border p-6 sm:p-8"
             style="border-color:rgba(107,79,168,0.5);background:#0d0820;box-shadow:0 0 60px 8px rgba(107,79,168,0.2);">

            <!-- Close button -->
            <button type="button" data-modal-close aria-label="Tutup detail proyek"
                    class="absolute right-5 top-5 flex h-11 w-11 items-center justify-center rounded-xl border transition-all"
                    style="border-color:rgba(107,79,168,0.4);color:#9270d0;"
                    onmouseover="this.style.borderColor='rgba(192,132,252,0.5)';this.style.color='#c0a8e8';this.style.background='rgba(79,51,138,0.3)'"
                    onmouseout="this.style.borderColor='rgba(107,79,168,0.4)';this.style.color='#9270d0';this.style.background='transparent'">
                <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>

            <div class="pr-14">
                <span id="modal-project-category" class="inline-flex items-center rounded-lg border px-2.5 py-1 font-mono text-xs font-semibold"
                      style="border-color:rgba(192,132,252,0.25);background:rgba(192,132,252,0.1);color:#c084fc;"></span>
                <h2 id="modal-project-title" class="mt-3 text-2xl font-extrabold tracking-tight sm:text-3xl" style="color:#f0ebff;"></h2>
                <p id="modal-project-tagline" class="mt-2 text-base" style="color:#9270d0;"></p>
            </div>

            <!-- Metrics banner -->
            <div id="modal-project-metrics-wrapper" class="mt-5 rounded-xl border p-4 font-mono text-xs"
                 style="border-color:rgba(247,211,64,0.25);background:rgba(247,211,64,0.06);color:#f7d340;">
                <span class="font-bold">Info &amp; Performa:</span>
                <span id="modal-project-metrics" class="ml-1"></span>
            </div>

            <!-- Description -->
            <div class="mt-6">
                <h3 class="text-xs font-semibold uppercase tracking-widest" style="color:#9270d0;">Ringkasan Proyek</h3>
                <p id="modal-project-description" class="mt-2 text-sm leading-relaxed" style="color:#c0a8e8;"></p>
            </div>

            <!-- Architecture -->
            <div id="modal-project-architecture-wrapper" class="mt-5 rounded-xl border p-4"
                 style="border-color:rgba(107,79,168,0.35);background:rgba(6,4,18,0.7);">
                <h3 class="font-mono text-xs font-semibold uppercase tracking-widest" style="color:#c084fc;">Strategi &amp; Arsitektur</h3>
                <p id="modal-project-architecture" class="mt-2 font-mono text-xs leading-relaxed" style="color:#9270d0;"></p>
            </div>

            <!-- Features -->
            <div class="mt-6">
                <h3 class="text-xs font-semibold uppercase tracking-widest" style="color:#9270d0;">Fitur Utama</h3>
                <ul id="modal-project-features" class="mt-3 space-y-2.5"></ul>
            </div>

            <!-- Tech stack -->
            <div class="mt-6 border-t pt-5" style="border-color:rgba(107,79,168,0.25);">
                <h3 class="font-mono text-xs font-semibold uppercase tracking-widest" style="color:#9270d0;">Teknologi yang Digunakan</h3>
                <div id="modal-project-tech-stack" class="mt-3 flex flex-wrap gap-2"></div>
            </div>

            <!-- Actions -->
            <div class="mt-7 flex flex-wrap items-center justify-end gap-3 border-t pt-5" style="border-color:rgba(107,79,168,0.25);">
                <a id="modal-project-github" href="#" target="_blank" rel="noopener noreferrer"
                   class="inline-flex min-h-[44px] items-center gap-2 rounded-xl border px-5 py-2.5 text-sm font-semibold transition-all"
                   style="border-color:rgba(107,79,168,0.4);color:#c0a8e8;"
                   onmouseover="this.style.borderColor='rgba(192,132,252,0.5)';this.style.background='rgba(79,51,138,0.3)'"
                   onmouseout="this.style.borderColor='rgba(107,79,168,0.4)';this.style.background='transparent'">
                    <svg class="h-4 w-4" fill="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                        <path fill-rule="evenodd" clip-rule="evenodd" d="M12 2C6.477 2 2 6.484 2 12.017c0 4.425 2.865 8.18 6.839 9.504.5.092.682-.217.682-.483 0-.237-.008-.868-.013-1.703-2.782.605-3.369-1.343-3.369-1.343-.454-1.158-1.11-1.466-1.11-1.466-.908-.62.069-.608.069-.608 1.003.07 1.53 1.032 1.53 1.032.892 1.53 2.341 1.088 2.91.832.092-.647.35-1.088.636-1.338-2.22-.253-4.555-1.113-4.555-4.951 0-1.093.39-1.988 1.029-2.688-.103-.253-.446-1.272.098-2.65 0 0 .84-.27 2.75 1.026A9.564 9.564 0 0112 6.844c.85.004 1.705.115 2.504.337 1.909-1.296 2.747-1.027 2.747-1.027.546 1.379.202 2.398.1 2.651.64.7 1.028 1.595 1.028 2.688 0 3.848-2.339 4.695-4.566 4.943.359.309.678.92.678 1.855 0 1.338-.012 2.419-.012 2.747 0 .268.18.58.688.482A10.019 10.019 0 0022 12.017C22 6.484 17.522 2 12 2z"/>
                    </svg>
                    Lihat di GitHub
                </a>
                <a id="modal-project-demo" href="#" target="_blank" rel="noopener noreferrer"
                   class="inline-flex min-h-[44px] items-center gap-2 rounded-xl px-5 py-2.5 text-sm font-semibold transition-all active:scale-95"
                   style="background:#f7d340;color:#0d0820;"
                   onmouseover="this.style.background='#ffe033';this.style.boxShadow='0 0 18px 2px rgba(247,211,64,0.4)'"
                   onmouseout="this.style.background='#f7d340';this.style.boxShadow='none'">
                    Kunjungi Website / Live Preview
                </a>
            </div>
        </div>
    </div>

</body>
</html>
