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
    <header class="sticky top-0 z-10 border-b border-line bg-card/90 backdrop-blur">
        <div class="mx-auto flex h-16 max-w-6xl items-center gap-3 px-4 sm:px-6">
            <x-logo />
            <nav class="ml-auto flex items-center gap-1 sm:gap-2" aria-label="{{ __('Main') }}">
                <x-language-switcher />
                <a href="{{ route('login') }}"
                    class="inline-flex items-center gap-1.5 rounded-md bg-soft px-3 py-2 text-sm font-medium text-head hover:bg-line">
                    <x-icon name="user" class="size-4" /> {{ __('Log in') }} </a>
                @auth
                    <a href="{{ route('dashboard') }}"
                        class="inline-flex items-center gap-1.5 rounded-md bg-primary px-3 py-2 text-sm font-medium text-white hover:bg-[#2862d8]">
                        <x-icon name="home" class="size-4" /> {{ __('Dashboard') }} </a>
                @else
                    @if ($admissionOn)
                        <a href="{{ route('register') }}"
                            class="inline-flex items-center gap-1.5 rounded-md bg-primary px-3 py-2 text-sm font-medium text-white hover:bg-[#2862d8]">
                            {{ __('Register') }} </a>
                    @endif
                @endauth
            </nav>
        </div>
    </header>

    <main>
        {{-- Hero --}}
        <section class="mx-auto grid max-w-6xl items-center gap-10 px-4 py-14 sm:px-6 lg:grid-cols-2 lg:py-20">
            <div>
                @if ($siteSettings->site_subtitle)
                    <p class="mb-3 inline-flex items-center gap-2 rounded-full bg-tint px-3 py-1 text-xs font-medium text-primary">
                        {{ $siteSettings->site_subtitle }}
                    </p>
                @endif
                <h1 class="text-4xl font-semibold leading-tight text-head sm:text-5xl">{{ $siteSettings->site_title }}</h1>
                <p class="mt-5 max-w-lg text-base text-mute">
                    {{ $siteSettings->site_description ?? $siteSettings->site_tagline ?? __('Admissions, users, roles and settings in one secure dashboard.') }}
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
                                {{ __('Get started') }} </a>
                        @endif
                        <a href="{{ route('login') }}"
                            class="inline-flex items-center gap-2 rounded-md border border-line bg-card px-5 py-2.5 text-sm font-medium text-head hover:bg-soft">
                            {{ __('Log in') }} </a>
                    @endauth
                </div>
                <p class="mt-6 inline-flex items-center gap-2 text-xs font-medium {{ $admissionOn ? 'text-ok' : 'text-warn' }}">
                    <span class="size-2 rounded-full {{ $admissionOn ? 'bg-ok' : 'bg-warn' }}"></span>
                    {{ $admissionOn ? __('Admissions') . ': ' . __('Open') : __('Registration is currently closed') }}
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
                    @if ($siteSettings->site_tagline)
                        <p class="text-center text-sm text-mute">{{ $siteSettings->site_tagline }}</p>
                    @endif
                </div>
            </div>
        </section>

        {{-- Features --}}
        <section class="border-t border-line bg-card">
            <div class="mx-auto max-w-6xl px-4 py-14 sm:px-6">
                <h2 class="text-center text-2xl font-semibold text-head">{{ __('Key features') }}</h2>
                <div class="mt-10 grid grid-cols-1 gap-5 sm:grid-cols-2 lg:grid-cols-3">
                    @foreach ([
                        ['icon' => 'ticket', 'title' => __('Online admission'), 'text' => __('Enable or pause new admissions at any time.')],
                        ['icon' => 'users', 'title' => __('Users'), 'text' => __('User accounts with roles and fine-grained permissions.')],
                        ['icon' => 'lock', 'title' => __('Roles & permissions'), 'text' => __('Role-based access control for every screen.')],
                        ['icon' => 'clock', 'title' => __('Activity log'), 'text' => __('Every change is recorded and reviewable.')],
                        ['icon' => 'gear', 'title' => __('Site settings'), 'text' => __('Title, tagline, logo, favicon and contact details in one place.')],
                        ['icon' => 'check', 'title' => __('Email verification'), 'text' => __('Verified email-based access and password reset.')],
                    ] as $feature)
                        <div class="rounded-lg border border-line bg-page p-5">
                            <span class="grid size-11 place-items-center rounded-md bg-tint text-primary">
                                <x-icon :name="$feature['icon']" class="size-6" />
                            </span>
                            <h3 class="mt-4 font-medium text-head">{{ $feature['title'] }}</h3>
                            <p class="mt-1 text-mute">{{ $feature['text'] }}</p>
                        </div>
                    @endforeach
                </div>
            </div>
        </section>

        {{-- Contact --}}
        @if (filled($siteSettings->phone) || filled($siteSettings->email) || filled($siteSettings->address) || filled($siteSettings->website))
            <section class="border-t border-line">
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
</body>

</html>
