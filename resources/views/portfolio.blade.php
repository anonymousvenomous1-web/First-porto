<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="Portfolio of a Full-Stack Laravel and JavaScript Developer specialising in high-throughput backend architecture, resilient APIs, and responsive web applications.">
    <title>Full-Stack Laravel Developer | Systems &amp; Web Applications</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    {{-- Inline critical: prevent flash of unstyled void background --}}
    <style>
        :root { background: #060412; color: #e9e0ff; }
    </style>
</head>

{{--
    Design decisions (R-31):
    - Background void purple (#060412): creates depth, signals premium creative identity, never "dark for tech's sake"
    - Gold accent (#f7d340, #ffe033): warm complement to deep purple; neon-ish at key focus points only (R-13 dose cap)
    - Plum mid-tones (#2e1b5c -> #6b4fa8): surface elevation hierarchy without shadows on every element
    - Instrument Sans: geometric-humanist blend — technical clarity meets craft warmth
    - ENERGY 3 / RHYTHM 3 / MOTION 2
--}}
<body class="antialiased min-h-screen" style="background:#060412;color:#e9e0ff;">

    {{-- SVG radial aura behind hero — purpose: grounds the page with spatial depth, used once only (R-01) --}}
    <div class="pointer-events-none fixed inset-0 overflow-hidden" aria-hidden="true">
        <div class="absolute -top-32 left-1/2 -translate-x-1/2 w-[900px] h-[600px] rounded-full opacity-20"
             style="background:radial-gradient(ellipse at 50% 0%, #6b4fa8 0%, transparent 70%);"></div>
    </div>

    <!-- ━━━ HEADER ━━━ -->
    <header class="sticky top-0 z-40 w-full" style="border-bottom:1px solid rgba(107,79,168,0.3);background:rgba(6,4,18,0.85);backdrop-filter:blur(16px);">
        <div class="mx-auto flex h-18 max-w-7xl items-center justify-between px-4 sm:px-6 lg:px-8">

            <!-- Brand -->
            <a href="{{ route('portfolio.index') }}" class="group flex items-center gap-3" aria-label="Full-Stack Developer portfolio home">
                <span class="flex h-10 w-10 items-center justify-center rounded-xl font-mono text-sm font-black"
                      style="background:linear-gradient(135deg,#4f338a 0%,#f7d340 100%);color:#060412;letter-spacing:-0.02em;">
                    FD
                </span>
                <span class="flex flex-col">
                    <span class="text-sm font-bold tracking-tight" style="color:#e9e0ff;">Full-Stack Dev</span>
                    <span class="font-mono text-[10px]" style="color:#9270d0;">Laravel &amp; JavaScript</span>
                </span>
            </a>

            <!-- Desktop Nav (R-24: each link has a real section destination) -->
            <nav aria-label="Main Navigation" class="hidden md:flex md:items-center md:gap-7">
                @foreach (['projects' => 'Projects', 'architecture' => 'Architecture', 'experience' => 'Experience', 'contact' => 'Contact'] as $anchor => $label)
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
                    Discuss a Project
                </a>

                <!-- Mobile menu toggle (R-03: 44px min tap target) -->
                <button id="mobile-menu-btn" type="button"
                        aria-expanded="false" aria-controls="mobile-menu"
                        aria-label="Open navigation menu"
                        class="flex h-11 w-11 items-center justify-center rounded-xl border md:hidden"
                        style="border-color:rgba(107,79,168,0.5);background:rgba(46,27,92,0.5);color:#c0a8e8;">
                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                    </svg>
                </button>
            </div>
        </div>

        <!-- Mobile Drawer (R-03, R-32) -->
        <div id="mobile-menu" class="hidden border-t md:hidden" style="border-color:rgba(107,79,168,0.3);background:rgba(13,8,32,0.97);">
            <nav class="flex flex-col px-4 py-5 gap-1" aria-label="Mobile Navigation">
                @foreach (['projects' => 'Projects', 'architecture' => 'Architecture', 'experience' => 'Experience', 'contact' => 'Contact'] as $anchor => $label)
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
    {{-- RHYTHM 3: asymmetric 7/5 grid, left narrative + right architectural spec card --}}
    <section class="relative overflow-hidden pt-16 pb-24 md:pt-24 md:pb-32">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 items-center gap-14 lg:grid-cols-12">

                <!-- Left column -->
                <div class="lg:col-span-7 reveal">
                    <!-- Availability indicator (real state: marks availability, not decoration — R-39 dot marks actual state) -->
                    <div class="mb-6 inline-flex items-center gap-2.5 rounded-lg border px-3.5 py-1.5 text-xs font-mono font-semibold"
                         style="border-color:rgba(247,211,64,0.3);background:rgba(247,211,64,0.08);color:#f7d340;">
                        <span class="relative flex h-2 w-2">
                            <span class="absolute inline-flex h-full w-full animate-ping rounded-full opacity-60" style="background:#f7d340;animation-duration:2.4s;"></span>
                            <span class="relative inline-flex h-2 w-2 rounded-full" style="background:#f7d340;"></span>
                        </span>
                        Available for Core Engineering Roles
                    </div>

                    <!-- H1 — one per page, R-39 -->
                    <h1 class="text-4xl font-extrabold tracking-tight sm:text-5xl lg:text-6xl" style="color:#f0ebff;line-height:1.08;">
                        Building reliable
                        <span class="relative">
                            <span style="color:#f7d340;">Laravel backends</span>
                        </span>
                        <br>and responsive web interfaces.
                    </h1>

                    <p class="mt-6 max-w-xl text-lg leading-relaxed" style="color:#a89bc2;">
                        I am a Full-Stack developer focused on clean software design, low-latency APIs, and structured database architecture in PHP&nbsp;8.5 and modern JavaScript.
                    </p>

                    <!-- CTAs (R-15: specific action labels; R-26: both navigate to real sections) -->
                    <div class="mt-9 flex flex-wrap items-center gap-4">
                        <a href="#projects"
                           class="inline-flex min-h-[44px] items-center justify-center rounded-xl px-6 py-3 text-base font-semibold transition-all duration-200 active:scale-95"
                           style="background:#f7d340;color:#0d0820;"
                           onmouseover="this.style.background='#ffe033';this.style.boxShadow='0 0 22px 3px rgba(247,211,64,0.45)'"
                           onmouseout="this.style.background='#f7d340';this.style.boxShadow='none'">
                            View Selected Works
                        </a>
                        <a href="#contact"
                           class="inline-flex min-h-[44px] items-center justify-center rounded-xl border px-6 py-3 text-base font-semibold transition-all duration-200"
                           style="border-color:rgba(107,79,168,0.6);color:#c0a8e8;"
                           onmouseover="this.style.borderColor='rgba(192,132,252,0.7)';this.style.color='#e9e0ff';this.style.background='rgba(79,51,138,0.2)'"
                           onmouseout="this.style.borderColor='rgba(107,79,168,0.6)';this.style.color='#c0a8e8';this.style.background='transparent'">
                            Hire Directly
                        </a>
                    </div>

                    <!-- Stack chips -->
                    <div class="mt-10 border-t pt-6" style="border-color:rgba(107,79,168,0.25);">
                        <p class="mb-3 font-mono text-xs font-semibold uppercase tracking-widest" style="color:#6b4fa8;">Primary Stack</p>
                        <div class="flex flex-wrap gap-2">
                            @foreach (['Laravel 12', 'PHP 8.5', 'PostgreSQL', 'Redis Streams', 'Tailwind CSS', 'TypeScript', 'Docker'] as $tech)
                                <span class="rounded-lg border px-3 py-1 font-mono text-xs"
                                      style="border-color:rgba(107,79,168,0.4);background:rgba(46,27,92,0.5);color:#c0a8e8;">
                                    {{ $tech }}
                                </span>
                            @endforeach
                        </div>
                    </div>
                </div>

                <!-- Right: Spec card — RHYTHM 3: different composition from rest of page -->
                <div class="lg:col-span-5 reveal reveal-delay-2">
                    <div class="rounded-2xl border p-6"
                         style="border-color:rgba(107,79,168,0.4);background:rgba(22,13,48,0.8);box-shadow:0 0 48px 4px rgba(107,79,168,0.12);">
                        <!-- Terminal header dots — purpose: frame as a code/spec artifact, not fake terminal R-06 -->
                        <div class="flex items-center justify-between border-b pb-4" style="border-color:rgba(107,79,168,0.25);">
                            <div class="flex gap-1.5">
                                @foreach (['#c0a8e8', '#6b4fa8', '#2e1b5c'] as $dot)
                                    <span class="h-3 w-3 rounded-full" style="background:{{ $dot }};" aria-hidden="true"></span>
                                @endforeach
                            </div>
                            <span class="font-mono text-xs" style="color:#6b4fa8;">arch_snapshot.json</span>
                        </div>

                        <div class="mt-5 space-y-4 font-mono text-sm">
                            <!-- Block 1 -->
                            <div class="rounded-xl p-4" style="background:rgba(6,4,18,0.7);">
                                <span style="color:#f7d340;" class="font-semibold">$ stack.inspect</span>
                                <p class="mt-1.5 text-xs leading-relaxed" style="color:#9270d0;">
                                    Modular monolith with decoupled domain layers, background queue workers, and strict PHP type checking throughout.
                                </p>
                            </div>

                            <!-- Block 2: 2-col metrics -->
                            <div class="grid grid-cols-2 gap-3">
                                <div class="rounded-xl border p-3.5" style="border-color:rgba(107,79,168,0.3);background:rgba(22,13,48,0.6);">
                                    <span class="text-xs" style="color:#6b4fa8;">Test Coverage</span>
                                    <p class="mt-1 text-sm font-bold" style="color:#e9e0ff;">PHPUnit Suite</p>
                                    <p class="mt-0.5 text-[11px]" style="color:#9270d0;">Feature &amp; Unit tests</p>
                                </div>
                                <div class="rounded-xl border p-3.5" style="border-color:rgba(107,79,168,0.3);background:rgba(22,13,48,0.6);">
                                    <span class="text-xs" style="color:#6b4fa8;">Code Standards</span>
                                    <p class="mt-1 text-sm font-bold" style="color:#e9e0ff;">PSR-12 / Pint</p>
                                    <p class="mt-0.5 text-[11px]" style="color:#9270d0;">Strict types enabled</p>
                                </div>
                            </div>

                            <!-- Block 3 -->
                            <div class="rounded-xl border p-3.5" style="border-color:rgba(107,79,168,0.3);background:rgba(22,13,48,0.6);">
                                <span class="text-xs" style="color:#6b4fa8;">Environment</span>
                                <p class="mt-1 text-xs" style="color:#c0a8e8;">
                                    PHP 8.5.12 &nbsp;|&nbsp; Laravel 12 &nbsp;|&nbsp; SQLite / Postgres &nbsp;|&nbsp; Vite Pipeline
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

            <!-- Section header — RHYTHM 3: full-width header, different from hero asymmetry -->
            <div class="reveal flex flex-col md:flex-row md:items-end md:justify-between gap-6">
                <div>
                    <h2 class="text-3xl font-extrabold tracking-tight sm:text-4xl" style="color:#f0ebff;">
                        Selected Projects
                    </h2>
                    <p class="mt-3 max-w-xl text-base" style="color:#9270d0;">
                        Production systems, open-source utilities, and architectural implementations in Laravel, PostgreSQL, and JavaScript.
                    </p>
                </div>

                <!-- Filter tabs (R-26: working tab behaviour; R-27: empty state shown) -->
                <div class="flex flex-wrap gap-2" role="tablist" aria-label="Project Categories">
                    @foreach ($categories as $key => $label)
                        <button type="button" role="tab"
                                aria-selected="{{ $key === 'all' ? 'true' : 'false' }}"
                                data-category="{{ $key }}"
                                class="project-filter-tab min-h-[44px] rounded-xl px-4 py-2 text-xs font-semibold tracking-wide transition-all duration-200"
                                style="{{ $key === 'all'
                                    ? 'background:#f7d340;color:#0d0820;box-shadow:0 0 10px 1px rgba(247,211,64,0.22);'
                                    : 'background:rgba(46,27,92,0.6);color:#c0a8e8;border:1px solid rgba(107,79,168,0.35);' }}">
                            {{ $label }}
                        </button>
                    @endforeach
                </div>
            </div>

            <!-- Projects Grid -->
            <div class="mt-12 grid grid-cols-1 gap-6 md:grid-cols-2 lg:grid-cols-3">
                @forelse ($projects as $index => $project)
                    <article
                        class="project-card reveal reveal-delay-{{ min($index + 1, 4) }} flex flex-col rounded-2xl border transition-all duration-300"
                        style="border-color:rgba(107,79,168,0.3);background:rgba(22,13,48,0.75);"
                        data-category="{{ $project->category }}"
                        data-project-json="{{ json_encode([
                            'id'               => $project->id,
                            'title'            => $project->title,
                            'slug'             => $project->slug,
                            'category'         => $project->category,
                            'categoryLabel'    => $project->category_label,
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
                                    {{ $project->category_label }}
                                </span>
                                @if ($project->system_metrics)
                                    <span class="max-w-[160px] truncate font-mono text-[10px]" style="color:#6b4fa8;" title="{{ $project->system_metrics }}">
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

                        <!-- Card footer -->
                        <div class="border-t px-6 py-4" style="border-color:rgba(107,79,168,0.2);">
                            <div class="flex items-center justify-between">
                                <button type="button" data-open-project-modal
                                        class="inline-flex min-h-[44px] items-center gap-1.5 text-sm font-semibold transition-colors"
                                        style="color:#f7d340;"
                                        onmouseover="this.style.color='#ffe033'"
                                        onmouseout="this.style.color='#f7d340'">
                                    Case Study
                                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                                    </svg>
                                </button>

                                @if ($project->github_url)
                                    <a href="{{ $project->github_url }}"
                                       target="_blank" rel="noopener noreferrer"
                                       aria-label="View {{ $project->title }} source code on GitHub"
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
                    </article>
                @empty
                    <div class="col-span-full rounded-2xl border border-dashed p-12 text-center" style="border-color:rgba(107,79,168,0.3);">
                        <p class="font-semibold" style="color:#f0ebff;">No projects found.</p>
                        <p class="mt-1 text-sm" style="color:#6b4fa8;">Run the project seeder to populate portfolio data.</p>
                    </div>
                @endforelse
            </div>

            <!-- Filter empty state (R-27) -->
            <div id="projects-empty-state" class="hidden mt-8 rounded-2xl border border-dashed p-12 text-center" style="border-color:rgba(107,79,168,0.3);">
                <p class="font-semibold" style="color:#f0ebff;">No projects in this category.</p>
                <p class="mt-1 text-sm" style="color:#6b4fa8;">Select another filter tab to view additional work.</p>
            </div>
        </div>
    </section>

    <!-- ━━━ ARCHITECTURE — RHYTHM 3: Staggered layout, different internal composition ━━━ -->
    <section id="architecture" class="border-t py-20" style="border-color:rgba(107,79,168,0.2);">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">

            <div class="reveal mb-14 max-w-2xl">
                <h2 class="text-3xl font-extrabold tracking-tight sm:text-4xl" style="color:#f0ebff;">
                    Architecture &amp; Core Skills
                </h2>
                <p class="mt-3 text-base" style="color:#9270d0;">
                    A disciplined approach to software focused on correctness, maintainability, and measurable runtime performance.
                </p>
            </div>

            <!-- 3-col staggered cards with varying internal layouts (R-14, R-05) -->
            <div class="grid grid-cols-1 gap-6 md:grid-cols-3">

                <!-- Card 1 — taller accent treatment -->
                <div class="reveal reveal-delay-1 rounded-2xl border p-6"
                     style="border-color:rgba(107,79,168,0.35);background:rgba(22,13,48,0.75);">
                    <div class="flex h-12 w-12 items-center justify-center rounded-xl"
                         style="background:rgba(192,132,252,0.12);color:#c084fc;">
                        <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 7v10c0 2.21 3.582 4 8 4s8-1.79 8-4V7M4 7c0 2.21 3.582 4 8 4s8-1.79 8-4M4 7c0-2.21 3.582-4 8-4s8 1.79 8 4"/>
                        </svg>
                    </div>
                    <h3 class="mt-5 text-lg font-bold" style="color:#f0ebff;">High-Throughput Backends</h3>
                    <p class="mt-2 text-sm leading-relaxed" style="color:#9270d0;">
                        Database indexing, Redis caching, queue worker optimisation, and PostgreSQL partitioning for high-volume systems.
                    </p>
                    <ul class="mt-5 space-y-2">
                        @foreach (['Postgres Partitioning &amp; JSONB', 'Redis Streams &amp; Pipelines', 'Idempotent Webhook Consumers'] as $item)
                            <li class="flex items-center gap-2.5 font-mono text-xs" style="color:#c0a8e8;">
                                <span class="h-1.5 w-1.5 shrink-0 rounded-full" style="background:#f7d340;" aria-hidden="true"></span>
                                {!! $item !!}
                            </li>
                        @endforeach
                    </ul>
                </div>

                <!-- Card 2 — gold border to create hierarchy differentiation (R-14: not all cards identical) -->
                <div class="reveal reveal-delay-2 rounded-2xl border p-6"
                     style="border-color:rgba(247,211,64,0.3);background:rgba(22,13,48,0.75);">
                    <div class="flex h-12 w-12 items-center justify-center rounded-xl"
                         style="background:rgba(247,211,64,0.12);color:#f7d340;">
                        <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M10 20l4-16m4 4l4 4-4 4M6 16l-4-4 4-4"/>
                        </svg>
                    </div>
                    <h3 class="mt-5 text-lg font-bold" style="color:#f7d340;">Accessible Web Interfaces</h3>
                    <p class="mt-2 text-sm leading-relaxed" style="color:#9270d0;">
                        Semantic HTML5, WCAG AA contrast, keyboard focus flows, responsive reflow at every breakpoint.
                    </p>
                    <ul class="mt-5 space-y-2">
                        @foreach (['Tailwind CSS Utility Architecture', 'WAI-ARIA Dialogs &amp; Focus Trapping', 'Progressive Enhancement Forms'] as $item)
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
                    <h3 class="mt-5 text-lg font-bold" style="color:#f0ebff;">Testing &amp; Code Quality</h3>
                    <p class="mt-2 text-sm leading-relaxed" style="color:#9270d0;">
                        Automated PHPUnit test suites, PSR standards verification, and reproducible CI pipeline configurations.
                    </p>
                    <ul class="mt-5 space-y-2">
                        @foreach (['PHPUnit Feature &amp; Unit Coverage', 'Laravel Pint &amp; Static Analysis', 'Reproducible Docker Configs'] as $item)
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

    <!-- ━━━ EXPERIENCE — RHYTHM 3: Timeline layout (different from card grids above) ━━━ -->
    <section id="experience" class="border-t py-20" style="border-color:rgba(107,79,168,0.2);background:rgba(13,8,32,0.5);">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">

            <div class="reveal mb-14 max-w-2xl">
                <h2 class="text-3xl font-extrabold tracking-tight sm:text-4xl" style="color:#f0ebff;">
                    Work &amp; Experience
                </h2>
                <p class="mt-3 text-base" style="color:#9270d0;">
                    Professional track record delivering complex systems, scalable APIs, and production-grade web applications.
                </p>
            </div>

            <!-- Timeline — left border spine with offset dot markers -->
            <div class="ml-2 space-y-10 border-l sm:ml-4" style="border-color:rgba(107,79,168,0.3);">

                @foreach ([
                    ['title' => 'Senior Full-Stack Engineer',   'period' => '2023 to Present', 'sub' => 'Distributed Systems &amp; Web Applications', 'gold' => true,  'body' => 'Led architecture and migration of a legacy monolithic platform into a modern modular Laravel system. Implemented Redis queue workers processing 200,000+ daily scheduled tasks, reducing P95 job latency by 45%.'],
                    ['title' => 'Backend Laravel Developer',    'period' => '2021 to 2023',    'sub' => 'API Services &amp; Database Architecture',    'gold' => false, 'body' => 'Built secure multi-tenant payment gateway integrations, automated webhook verification systems, and RESTful APIs serving iOS, Android, and web clients with strict OpenAPI 3.0 documentation.'],
                    ['title' => 'Software Engineer',            'period' => '2019 to 2021',    'sub' => 'Full-Stack Web Development',                   'gold' => false, 'body' => 'Developed client portals, custom CRM dashboards, and automated PDF reporting engines using Laravel, MySQL, and responsive JavaScript frameworks.'],
                ] as $i => $role)
                    <div class="reveal reveal-delay-{{ $i + 1 }} relative pl-8 sm:pl-10">
                        <!-- Timeline marker dot -->
                        <span class="absolute -left-[9px] mt-1.5 h-4 w-4 rounded-full border-2"
                              style="background:{{ $role['gold'] ? '#f7d340' : '#2e1b5c' }};border-color:{{ $role['gold'] ? '#0d0820' : '#6b4fa8' }};{{ $role['gold'] ? 'box-shadow:0 0 10px 2px rgba(247,211,64,0.3);' : '' }}"
                              aria-hidden="true"></span>

                        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-1">
                            <h3 class="text-lg font-bold" style="color:#f0ebff;">{{ $role['title'] }}</h3>
                            <span class="font-mono text-xs" style="color:#6b4fa8;">{{ $role['period'] }}</span>
                        </div>
                        <p class="mt-1 text-sm font-semibold" style="color:{{ $role['gold'] ? '#f7d340' : '#9270d0' }};">{!! $role['sub'] !!}</p>
                        <p class="mt-3 text-sm leading-relaxed" style="color:#9270d0;">{{ $role['body'] }}</p>
                    </div>
                @endforeach
            </div>
        </div>
    </section>

    <!-- ━━━ CONTACT — RHYTHM 3: 5/7 split layout, different from prior sections ━━━ -->
    <section id="contact" class="border-t py-20" style="border-color:rgba(107,79,168,0.2);">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 gap-14 lg:grid-cols-12">

                <!-- Left narrative -->
                <div class="lg:col-span-5 reveal">
                    <h2 class="text-3xl font-extrabold tracking-tight sm:text-4xl" style="color:#f0ebff;">
                        Get in Touch
                    </h2>
                    <p class="mt-4 text-base leading-relaxed" style="color:#9270d0;">
                        Have a project in mind, an engineering problem to solve, or looking to discuss full-stack Laravel opportunities? I reply within one business day.
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
                                <span class="block font-mono text-xs" style="color:#6b4fa8;">Direct Email</span>
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
                                <span class="block font-mono text-xs" style="color:#6b4fa8;">Location</span>
                                <span class="font-semibold" style="color:#e9e0ff;">West Java, Indonesia</span>
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

                            {{-- Honeypot anti-spam field (hidden from real users) --}}
                            <div class="hidden" aria-hidden="true">
                                <label for="company_hp">Leave this field blank</label>
                                <input type="text" id="company_hp" name="company_hp" tabindex="-1" autocomplete="off">
                            </div>

                            <div class="grid grid-cols-1 gap-5 sm:grid-cols-2">
                                <!-- Name (R-39: visible label) -->
                                <div>
                                    <label for="contact-name" class="block text-xs font-semibold uppercase tracking-widest" style="color:#9270d0;">
                                        Name <span style="color:#f7d340;" aria-label="required">*</span>
                                    </label>
                                    <input type="text" id="contact-name" name="name"
                                           value="{{ old('name') }}" required
                                           placeholder="Your name"
                                           class="mt-2 block w-full rounded-xl border px-4 py-3 text-sm transition placeholder:text-opacity-40 focus:outline-none focus:ring-2"
                                           style="border-color:rgba(107,79,168,0.4);background:rgba(6,4,18,0.7);color:#e9e0ff;--placeholder-color:#6b4fa8;"
                                           onfocus="this.style.borderColor='rgba(247,211,64,0.5)';this.style.boxShadow='0 0 0 2px rgba(247,211,64,0.12)'"
                                           onblur="this.style.borderColor='rgba(107,79,168,0.4)';this.style.boxShadow='none'">
                                    <p data-error-feedback="name" class="mt-1.5 text-xs {{ $errors->has('name') ? '' : 'hidden' }}" style="color:#f87171;">
                                        {{ $errors->first('name') }}
                                    </p>
                                </div>

                                <!-- Email -->
                                <div>
                                    <label for="contact-email" class="block text-xs font-semibold uppercase tracking-widest" style="color:#9270d0;">
                                        Email <span style="color:#f7d340;" aria-label="required">*</span>
                                    </label>
                                    <input type="email" id="contact-email" name="email"
                                           value="{{ old('email') }}" required
                                           placeholder="your@email.com"
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
                                    Subject <span style="color:#f7d340;" aria-label="required">*</span>
                                </label>
                                <input type="text" id="contact-subject" name="subject"
                                       value="{{ old('subject') }}" required
                                       placeholder="Laravel Backend Consultation / Project Inquiry"
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
                                    Message <span style="color:#f7d340;" aria-label="required">*</span>
                                </label>
                                <textarea id="contact-message" name="message" rows="4" required
                                          placeholder="Tell me about your architecture requirements, timeline, or role details..."
                                          class="mt-2 block w-full rounded-xl border px-4 py-3 text-sm transition focus:outline-none resize-none"
                                          style="border-color:rgba(107,79,168,0.4);background:rgba(6,4,18,0.7);color:#e9e0ff;"
                                          onfocus="this.style.borderColor='rgba(247,211,64,0.5)';this.style.boxShadow='0 0 0 2px rgba(247,211,64,0.12)'"
                                          onblur="this.style.borderColor='rgba(107,79,168,0.4)';this.style.boxShadow='none'">{{ old('message') }}</textarea>
                                <p data-error-feedback="message" class="mt-1.5 text-xs {{ $errors->has('message') ? '' : 'hidden' }}" style="color:#f87171;">
                                    {{ $errors->first('message') }}
                                </p>
                            </div>

                            <!-- Submit (R-03: min 44px; R-26: real submit action) -->
                            <button type="submit"
                                    class="inline-flex min-h-[44px] w-full items-center justify-center gap-2 rounded-xl px-6 py-3 text-sm font-semibold transition-all duration-200 active:scale-[0.98] disabled:opacity-60"
                                    style="background:#f7d340;color:#0d0820;"
                                    onmouseover="if(!this.disabled){this.style.background='#ffe033';this.style.boxShadow='0 0 22px 3px rgba(247,211,64,0.4)'}"
                                    onmouseout="this.style.background='#f7d340';this.style.boxShadow='none'">
                                <svg id="submit-btn-spinner" class="hidden h-4 w-4 animate-spin" fill="none" viewBox="0 0 24 24" aria-hidden="true">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                </svg>
                                <span id="submit-btn-text">Send Message</span>
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
            <span class="text-sm" style="color:#6b4fa8;">
                <span style="color:#c0a8e8;font-weight:600;">Full-Stack Laravel Developer</span>
                &nbsp;&bull;&nbsp; Crafted with Laravel 12 &amp; Tailwind CSS
            </span>
            <div class="flex items-center gap-6 text-sm" style="color:#6b4fa8;">
                @foreach (['projects' => 'Projects', 'architecture' => 'Architecture', 'experience' => 'Experience', 'contact' => 'Contact'] as $anchor => $label)
                    <a href="#{{ $anchor }}" class="transition-colors"
                       onmouseover="this.style.color='#f7d340'" onmouseout="this.style.color='#6b4fa8'">{{ $label }}</a>
                @endforeach
            </div>
        </div>
    </footer>

    <!-- ━━━ PROJECT MODAL (R-26, R-32, R-39) ━━━ -->
    <div id="project-modal" role="dialog" aria-modal="true" aria-labelledby="modal-project-title"
         class="hidden fixed inset-0 z-50 flex items-center justify-center p-4 sm:p-6 lg:p-8"
         style="background:rgba(6,4,18,0.85);backdrop-filter:blur(8px);">

        <div class="relative w-full max-w-3xl max-h-[90vh] overflow-y-auto rounded-2xl border p-6 sm:p-8"
             style="border-color:rgba(107,79,168,0.5);background:#0d0820;box-shadow:0 0 60px 8px rgba(107,79,168,0.2);">

            <!-- Close button (R-32: Escape also closes, R-39: aria-label) -->
            <button type="button" data-modal-close aria-label="Close project case study"
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
                <span class="font-bold">Benchmark:</span>
                <span id="modal-project-metrics" class="ml-1"></span>
            </div>

            <!-- Description -->
            <div class="mt-6">
                <h3 class="text-xs font-semibold uppercase tracking-widest" style="color:#6b4fa8;">Overview</h3>
                <p id="modal-project-description" class="mt-2 text-sm leading-relaxed" style="color:#c0a8e8;"></p>
            </div>

            <!-- Architecture -->
            <div id="modal-project-architecture-wrapper" class="mt-5 rounded-xl border p-4"
                 style="border-color:rgba(107,79,168,0.35);background:rgba(6,4,18,0.7);">
                <h3 class="font-mono text-xs font-semibold uppercase tracking-widest" style="color:#c084fc;">Architectural Strategy</h3>
                <p id="modal-project-architecture" class="mt-2 font-mono text-xs leading-relaxed" style="color:#9270d0;"></p>
            </div>

            <!-- Features -->
            <div class="mt-6">
                <h3 class="text-xs font-semibold uppercase tracking-widest" style="color:#6b4fa8;">Key Features</h3>
                <ul id="modal-project-features" class="mt-3 space-y-2.5"></ul>
            </div>

            <!-- Tech stack -->
            <div class="mt-6 border-t pt-5" style="border-color:rgba(107,79,168,0.25);">
                <h3 class="font-mono text-xs font-semibold uppercase tracking-widest" style="color:#6b4fa8;">Technology &amp; Tooling</h3>
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
                    GitHub Repository
                </a>
                <a id="modal-project-demo" href="#" target="_blank" rel="noopener noreferrer"
                   class="inline-flex min-h-[44px] items-center gap-2 rounded-xl px-5 py-2.5 text-sm font-semibold transition-all active:scale-95"
                   style="background:#f7d340;color:#0d0820;"
                   onmouseover="this.style.background='#ffe033';this.style.boxShadow='0 0 18px 2px rgba(247,211,64,0.4)'"
                   onmouseout="this.style.background='#f7d340';this.style.boxShadow='none'">
                    Live Preview
                </a>
            </div>
        </div>
    </div>

</body>
</html>
