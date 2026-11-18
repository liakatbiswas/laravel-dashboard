<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="scroll-smooth">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $siteSettings->site_title }}@if ($siteSettings->site_tagline) | {{ $siteSettings->site_tagline }}@endif</title>
    <meta name="description" content="{{ $siteSettings->site_description ?? $siteSettings->site_tagline ?? $siteSettings->site_title }}">
    <link rel="icon"
        href="{{ $siteSettings->favicon_path
            ? \Illuminate\Support\Facades\Storage::disk('public')->url($siteSettings->favicon_path)
            : asset('assets/images/logo/favicon.ico') }}">

    <script>
        (function() {
            var h = document.documentElement;
            try {
                var t = localStorage.getItem('theme');
                if (t === 'dark' || (!t && matchMedia('(prefers-color-scheme: dark)').matches)) h.classList.add('dark');
            } catch (e) {}
        })();
    </script>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="bg-page font-sans text-sm text-ink antialiased">
    @php $admissionOn = \App\Models\AdmissionOnOff::isOn(); @endphp

    {{-- Header --}}
    <header class="sticky top-0 z-20 border-b border-line bg-card/90 backdrop-blur">
        <div class="mx-auto flex h-16 max-w-6xl items-center gap-2 px-4 sm:px-6">
            <a href="{{ url('/') }}" class="flex h-9 shrink-0 items-center" aria-label="{{ $siteSettings->site_title }}">
                <img src="{{ $siteSettings->light_logo_path
                        ? \Illuminate\Support\Facades\Storage::disk('public')->url($siteSettings->light_logo_path)
                        : asset('assets/images/logo/logo-dark.png') }}"
                    alt="{{ $siteSettings->site_title }}"
                    class="h-9 w-auto max-w-[140px] object-contain dark:hidden sm:max-w-[165px]">
                <img src="{{ $siteSettings->dark_logo_path
                        ? \Illuminate\Support\Facades\Storage::disk('public')->url($siteSettings->dark_logo_path)
                        : asset('assets/images/logo/logo-dark.png') }}"
                    alt="{{ $siteSettings->site_title }}"
                    class="hidden h-9 w-auto max-w-[140px] object-contain dark:block sm:max-w-[165px]{{ ! $siteSettings->dark_logo_path ? ' rounded-md bg-white px-2' : '' }}">
            </a>

            <nav class="ml-2 hidden items-center gap-5 text-sm font-medium text-mute lg:flex" aria-label="{{ __('Main') }}">
                <a href="#about" class="transition-colors hover:text-head">{{ __('About EFA') }}</a>
                <a href="#training" class="transition-colors hover:text-head">{{ __('Training') }}</a>
                <a href="#crafts" class="transition-colors hover:text-head">{{ __('Crafts') }}</a>
                <a href="#contact" class="transition-colors hover:text-head">{{ __('Contact') }}</a>
            </nav>

            <div class="ml-auto flex items-center gap-0.5 sm:gap-1.5">
                <button id="th" type="button"
                    class="relative grid size-10 shrink-0 cursor-pointer place-items-center rounded-full text-mute hover:bg-soft hover:text-head"
                    aria-label="{{ __('Light/Dark mode') }}">
                    <x-icon name="moon" class="dark:hidden" />
                    <x-icon name="sun" class="hidden dark:block" />
                </button>
                <x-language-switcher />
                <a href="{{ route('login') }}"
                    class="hidden items-center gap-1.5 rounded-md bg-soft px-3 py-2 text-sm font-medium text-head hover:bg-line lg:inline-flex">
                    <x-icon name="user" class="size-4" /> {{ __('Log in') }} </a>
                @auth
                    <a href="{{ route('dashboard') }}"
                        class="hidden items-center gap-1.5 rounded-md bg-primary px-3 py-2 text-sm font-medium text-white hover:bg-[#2862d8] lg:inline-flex">
                        <x-icon name="home" class="size-4" /> {{ __('Dashboard') }} </a>
                @else
                    @if ($admissionOn)
                        <a href="{{ route('register') }}"
                            class="hidden items-center gap-1.5 rounded-md bg-primary px-3 py-2 text-sm font-medium text-white hover:bg-[#2862d8] lg:inline-flex">
                            {{ __('Register') }} </a>
                    @endif
                @endauth
                <button id="mm" type="button"
                    class="grid size-10 shrink-0 cursor-pointer place-items-center rounded-full text-mute hover:bg-soft hover:text-head lg:hidden"
                    aria-expanded="false" aria-controls="mobile-menu" aria-label="{{ __('Menu') }}">
                    <span data-mi="open"><x-icon name="menu" /></span>
                    <span data-mi="close" class="hidden"><x-icon name="x" /></span>
                </button>
            </div>
        </div>

        {{-- Mobile menu panel --}}
        <div id="mobile-menu" class="hidden border-t border-line bg-card lg:hidden">
            <div class="mx-auto max-w-6xl space-y-1 px-4 py-4 sm:px-6">
                <a href="#about" class="block rounded-md px-3 py-2.5 font-medium text-head hover:bg-soft">{{ __('About EFA') }}</a>
                <a href="#training" class="block rounded-md px-3 py-2.5 font-medium text-head hover:bg-soft">{{ __('Training') }}</a>
                <a href="#crafts" class="block rounded-md px-3 py-2.5 font-medium text-head hover:bg-soft">{{ __('Crafts') }}</a>
                <a href="#contact" class="block rounded-md px-3 py-2.5 font-medium text-head hover:bg-soft">{{ __('Contact') }}</a>
                <hr class="my-2 border-line">
                @auth
                    <a href="{{ route('dashboard') }}"
                        class="flex items-center gap-2 rounded-md bg-primary px-4 py-3 font-medium text-white hover:bg-[#2862d8]">
                        <x-icon name="home" class="size-4" /> {{ __('Dashboard') }} </a>
                @else
                    <div class="flex flex-wrap gap-2">
                        <a href="{{ route('login') }}"
                            class="inline-flex flex-1 basis-40 items-center justify-center gap-1.5 rounded-md bg-soft px-4 py-3 text-sm font-medium text-head hover:bg-line">
                            <x-icon name="user" class="size-4" /> {{ __('Log in') }} </a>
                        @if ($admissionOn)
                            <a href="{{ route('register') }}"
                                class="inline-flex flex-1 basis-40 items-center justify-center gap-1.5 rounded-md bg-primary px-4 py-3 text-sm font-medium text-white hover:bg-[#2862d8]">
                                {{ __('Register') }} </a>
                        @endif
                    </div>
                @endauth
            </div>
        </div>
    </header>

    <main>
        {{-- Hero --}}
        <section class="relative overflow-hidden">
            <div class="pointer-events-none absolute -right-24 top-8 size-96 rounded-full bg-tint blur-3xl" aria-hidden="true"></div>
            <div class="mx-auto grid max-w-6xl items-center gap-12 px-4 py-14 sm:px-6 lg:grid-cols-2 lg:py-20">
                <div class="relative">
                    <p class="mb-4 inline-flex items-center gap-2 rounded-full bg-tint px-3 py-1 text-xs font-semibold uppercase tracking-wider text-primary">
                        {{ $siteSettings->site_subtitle ?: __('EFA Bangladesh') }}
                    </p>
                    <h1 class="text-4xl font-semibold leading-[1.15] tracking-tight text-head sm:text-5xl">
                        {{ $siteSettings->site_tagline ?: __('Smart Skills for a Sustainable Future') }}
                    </h1>
                    <p class="mt-5 max-w-lg text-base leading-relaxed text-mute">
                        {{ $siteSettings->site_description ?? __('An integrated platform for skills development, education, entrepreneurship, crafts manufacturing, e-commerce and export management.') }}
                    </p>
                    <div class="mt-8 flex flex-wrap items-center gap-3">
                        @auth
                            <a href="{{ route('dashboard') }}"
                                class="inline-flex items-center gap-2 rounded-md bg-primary px-5 py-2.5 text-sm font-medium text-white hover:bg-[#2862d8]">
                                <x-icon name="home" class="size-4" /> {{ __('Dashboard') }} </a>
                        @else
                            @if ($admissionOn)
                                <a href="{{ route('register') }}"
                                    class="inline-flex items-center gap-2 rounded-md bg-primary px-5 py-2.5 text-sm font-medium text-white hover:bg-[#2862d8]">
                                    {{ __('Apply for admission') }} </a>
                            @endif
                        @endauth
                        <a href="#crafts"
                            class="inline-flex items-center gap-2 rounded-md border border-line bg-card px-5 py-2.5 text-sm font-medium text-head hover:bg-soft">
                            <x-icon name="box" class="size-4" /> {{ __('Explore EFA Crafts') }} </a>
                    </div>
                    <p class="mt-6 inline-flex items-center gap-2 text-xs font-medium {{ $admissionOn ? 'text-ok' : 'text-warn' }}">
                        <span class="size-2 rounded-full {{ $admissionOn ? 'bg-ok' : 'bg-warn' }}"></span>
                        {{ $admissionOn ? __('Admission open') : __('Registration is currently closed') }}
                    </p>
                </div>

                {{-- Decorative panel --}}
                <div class="relative mx-auto w-full max-w-md" aria-hidden="true">
                    <div class="absolute inset-0 -rotate-3 rounded-3xl bg-tint"></div>
                    <div class="relative flex aspect-square flex-col items-center justify-center gap-6 rounded-3xl border border-line bg-card p-8 shadow-card">
                        <img src="{{ $siteSettings->light_logo_path
                                ? \Illuminate\Support\Facades\Storage::disk('public')->url($siteSettings->light_logo_path)
                                : asset('assets/images/logo/logo-dark.png') }}"
                            alt="{{ $siteSettings->site_title }}"
                            class="w-56 max-w-full object-contain object-center dark:hidden">
                        <img src="{{ $siteSettings->dark_logo_path
                                ? \Illuminate\Support\Facades\Storage::disk('public')->url($siteSettings->dark_logo_path)
                                : asset('assets/images/logo/logo-sm.png') }}"
                            alt="{{ $siteSettings->site_title }}"
                            class="hidden w-40 max-w-full object-contain object-center dark:block">
                        <div class="flex flex-wrap justify-center gap-2">
                            @foreach ([__('Skills & Training'), __('EFA Crafts'), __('Education'), __('Export')] as $tag)
                                <span class="rounded-full bg-tint px-3 py-1 text-xs font-medium text-primary">{{ $tag }}</span>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
        </section>

        {{-- About EFA --}}
        <section id="about" class="scroll-mt-20 border-t border-line bg-card">
            <div class="mx-auto max-w-6xl px-4 py-14 sm:px-6">
                <p class="text-xs font-semibold uppercase tracking-widest text-primary">{{ __('About EFA') }}</p>
                <h2 class="mt-2 max-w-2xl text-2xl font-semibold tracking-tight text-head sm:text-3xl">{{ __('One roof for skills, crafts and trade') }}</h2>
                <p class="mt-4 max-w-3xl text-base leading-relaxed text-mute">{{ __('EFA Bangladesh works to grow skilled human capital and handmade craft heritage — combining training, education, entrepreneurship support, production and export in one national initiative.') }}</p>
            </div>
        </section>

        {{-- Focus areas --}}
        <section class="border-t border-line">
            <div class="mx-auto max-w-6xl px-4 py-14 sm:px-6">
                <p class="text-xs font-semibold uppercase tracking-widest text-primary">{{ __('What we do') }}</p>
                <h2 class="mt-2 text-2xl font-semibold tracking-tight text-head sm:text-3xl">{{ __('Seven focus areas, one mission') }}</h2>
                <div class="mt-10 grid grid-cols-1 gap-5 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4">
                    @foreach ([
                        ['icon' => 'info', 'title' => __('About EFA'), 'text' => __('Our mission, vision and activities.')],
                        ['icon' => 'board', 'title' => __('Skills & Training'), 'text' => __('Certificate courses, admission and student care.')],
                        ['icon' => 'box', 'title' => __('EFA Crafts — Shop & Export'), 'text' => __('Handmade crafts — from catalog to your doorstep and beyond.')],
                        ['icon' => 'briefcase', 'title' => __('Entrepreneurship'), 'text' => __('Idea submission, mentorship and startup support.')],
                        ['icon' => 'doc', 'title' => __('Education'), 'text' => __('Digital learning resources and online programs.')],
                        ['icon' => 'folder', 'title' => __('Projects & Partnerships'), 'text' => __('Government, NGO and international donor partnerships.')],
                        ['icon' => 'bell', 'title' => __('News & Media'), 'text' => __('Latest news, articles and announcements.')],
                    ] as $area)
                        <div class="group rounded-lg border border-line bg-card p-5 transition-shadow hover:shadow-card">
                            <span class="grid size-11 place-items-center rounded-md bg-tint text-primary">
                                <x-icon :name="$area['icon']" class="size-6" />
                            </span>
                            <h3 class="mt-4 font-medium leading-snug text-head">{{ $area['title'] }}</h3>
                            <p class="mt-1 text-mute">{{ $area['text'] }}</p>
                        </div>
                    @endforeach

                    {{-- Admissions status card --}}
                    <div class="rounded-lg border border-line {{ $admissionOn ? 'bg-tint' : 'bg-card' }} p-5">
                        <span class="grid size-11 place-items-center rounded-md {{ $admissionOn ? 'bg-primary text-white' : 'bg-soft text-warn' }}">
                            <x-icon name="ticket" class="size-6" />
                        </span>
                        <h3 class="mt-4 font-medium leading-snug text-head">{{ __('Admissions') }}</h3>
                        <p class="mt-1 text-mute">{{ $admissionOn ? __('Admission open') : __('Registration is currently closed') }}</p>
                        @if ($admissionOn && ! auth()->check())
                            <a href="{{ route('register') }}" class="mt-3 inline-flex items-center gap-1.5 text-xs font-semibold text-primary hover:underline">
                                {{ __('Apply for admission') }} <x-icon name="chev" class="size-3.5" /> </a>
                        @endif
                    </div>
                </div>
            </div>
        </section>

        {{-- Two flagship systems --}}
        <section id="training" class="scroll-mt-20 border-t border-line bg-card">
            <div class="mx-auto max-w-6xl px-4 py-14 sm:px-6">
                <p class="text-xs font-semibold uppercase tracking-widest text-primary">{{ __('Two flagship systems') }}</p>
                <h2 class="mt-2 max-w-2xl text-2xl font-semibold tracking-tight text-head sm:text-3xl">{{ __('Built around training and crafts commerce') }}</h2>

                <div class="mt-10 grid gap-6 lg:grid-cols-2">
                    {{-- Training Management System --}}
                    <div class="flex flex-col rounded-xl border border-line bg-page p-6">
                        <span class="grid size-12 place-items-center rounded-lg bg-primary text-white">
                            <x-icon name="board" class="size-6" />
                        </span>
                        <h3 class="mt-5 text-lg font-semibold text-head">{{ __('Training Management System') }}</h3>
                        <p class="mt-1 text-mute">{{ __('Everything from admission to certificate — in one place.') }}</p>
                        <ul class="mt-5 space-y-2.5 text-sm text-ink">
                            @foreach ([
                                __('Admission & enrollment'),
                                __('Student management'),
                                __('Courses, batches & trainers'),
                                __('Attendance & examination'),
                                __('Certificates & fee payment'),
                            ] as $item)
                                <li class="flex items-start gap-2.5">
                                    <x-icon name="check" class="mt-0.5 size-4 shrink-0 text-ok" />
                                    <span>{{ $item }}</span>
                                </li>
                            @endforeach
                        </ul>
                    </div>

                    {{-- EFA Crafts Commerce System --}}
                    <div id="crafts" class="flex scroll-mt-20 flex-col rounded-xl border border-line bg-page p-6">
                        <span class="grid size-12 place-items-center rounded-lg bg-primary text-white">
                            <x-icon name="box" class="size-6" />
                        </span>
                        <h3 class="mt-5 text-lg font-semibold text-head">{{ __('EFA Crafts Commerce System') }}</h3>
                        <p class="mt-1 text-mute">{{ __('Jute, bamboo, natural fiber, textile and block print crafts.') }}</p>
                        <ul class="mt-5 space-y-2.5 text-sm text-ink">
                            @foreach ([
                                __('Product catalog & categories'),
                                __('Retail & wholesale pricing'),
                                __('Cart & guest checkout'),
                                __('Orders, payment & invoice'),
                                __('Export inquiry & shipment'),
                            ] as $item)
                                <li class="flex items-start gap-2.5">
                                    <x-icon name="check" class="mt-0.5 size-4 shrink-0 text-ok" />
                                    <span>{{ $item }}</span>
                                </li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            </div>
        </section>

        {{-- Call to action --}}
        <section class="border-t border-line">
            <div class="mx-auto max-w-6xl px-4 py-12 sm:px-6">
                <div class="rounded-2xl bg-tint px-6 py-10 text-center sm:px-12">
                    <h2 class="text-2xl font-semibold tracking-tight text-head sm:text-3xl">{{ __('Ready to start your journey?') }}</h2>
                    <p class="mx-auto mt-2 max-w-xl text-mute">{{ __('Join a course or send an export inquiry today.') }}</p>
                    <div class="mt-6 flex flex-wrap items-center justify-center gap-3">
                        @if ($admissionOn && ! auth()->check())
                            <a href="{{ route('register') }}"
                                class="inline-flex items-center gap-2 rounded-md bg-primary px-5 py-2.5 text-sm font-medium text-white hover:bg-[#2862d8]">
                                {{ __('Apply for admission') }} </a>
                        @endif
                        <a href="#contact"
                            class="inline-flex items-center gap-2 rounded-md border border-line bg-card px-5 py-2.5 text-sm font-medium text-head hover:bg-soft">
                            {{ __('Get in touch') }} </a>
                    </div>
                </div>
            </div>
        </section>

        {{-- Contact --}}
        @if (filled($siteSettings->phone) || filled($siteSettings->email) || filled($siteSettings->address) || filled($siteSettings->website))
            <section id="contact" class="scroll-mt-20 border-t border-line bg-card">
                <div class="mx-auto max-w-6xl px-4 py-12 sm:px-6">
                    <h2 class="text-xl font-semibold text-head">{{ __('Contact us') }}</h2>
                    <dl class="mt-6 grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-4">
                        @if (filled($siteSettings->phone))
                            <div>
                                <dt class="text-xs font-medium uppercase tracking-wide text-mute">{{ __('Phone') }}</dt>
                                <dd class="mt-1"><a href="tel:{{ $siteSettings->phone }}" class="hover:text-primary">{{ $siteSettings->phone }}</a></dd>
                            </div>
                        @endif
                        @if (filled($siteSettings->email))
                            <div>
                                <dt class="text-xs font-medium uppercase tracking-wide text-mute">{{ __('Email') }}</dt>
                                <dd class="mt-1"><a href="mailto:{{ $siteSettings->email }}" class="hover:text-primary">{{ $siteSettings->email }}</a></dd>
                            </div>
                        @endif
                        @if (filled($siteSettings->address))
                            <div>
                                <dt class="text-xs font-medium uppercase tracking-wide text-mute">{{ __('Address') }}</dt>
                                <dd class="mt-1 whitespace-pre-line">{{ $siteSettings->address }}</dd>
                            </div>
                        @endif
                        @if (filled($siteSettings->website))
                            <div>
                                <dt class="text-xs font-medium uppercase tracking-wide text-mute">{{ __('Website') }}</dt>
                                <dd class="mt-1"><a href="{{ $siteSettings->website }}" target="_blank" rel="noopener noreferrer" class="hover:text-primary">{{ $siteSettings->website }}</a></dd>
                            </div>
                        @endif
                    </dl>
                </div>
            </section>
        @endif
    </main>

    @include('layouts.app-parts.footer')

    <script>
        (function () {
            var btn = document.getElementById('mm');
            var panel = document.getElementById('mobile-menu');
            if (!btn || !panel) return;

            var setOpen = function (open) {
                panel.classList.toggle('hidden', !open);
                btn.setAttribute('aria-expanded', String(open));
                btn.querySelector('[data-mi="open"]').classList.toggle('hidden', open);
                btn.querySelector('[data-mi="close"]').classList.toggle('hidden', !open);
            };

            btn.addEventListener('click', function (e) {
                e.stopPropagation();
                setOpen(panel.classList.contains('hidden'));
            });

            panel.addEventListener('click', function (e) {
                if (e.target.closest('a')) setOpen(false);
            });

            document.addEventListener('click', function (e) {
                if (!panel.classList.contains('hidden')
                    && !e.target.closest('#mobile-menu')
                    && !e.target.closest('#mm')) {
                    setOpen(false);
                }
            });

            document.addEventListener('keydown', function (e) {
                if (e.key === 'Escape') setOpen(false);
            });
        })();
    </script>
</body>

</html>
