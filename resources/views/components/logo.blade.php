@php
    $lightLogo = $siteSettings->light_logo_path
        ? \Illuminate\Support\Facades\Storage::disk('public')->url($siteSettings->light_logo_path)
        : asset('assets/images/logo/logo-dark.png');
    // The default wordmark is dark-on-transparent and unreadable on dark backgrounds,
    // so it is shown on a white chip until a dedicated dark logo is uploaded.
    $usingDefaultDarkLogo = ! $siteSettings->dark_logo_path;
    $darkLogo = $siteSettings->dark_logo_path
        ? \Illuminate\Support\Facades\Storage::disk('public')->url($siteSettings->dark_logo_path)
        : $lightLogo;
    $icon = $siteSettings->favicon_path
        ? \Illuminate\Support\Facades\Storage::disk('public')->url($siteSettings->favicon_path)
        : asset('assets/images/logo/logo-sm.png');
@endphp

<div class="flex h-9 items-center lg:[.sm-nav_&]:justify-center">
    <a href="{{ route('dashboard') }}" class="flex h-9 items-center lg:[.sm-nav_&]:justify-center">
        <img src="{{ $lightLogo }}" alt="{{ $siteSettings->site_title }}"
            class="h-9 w-auto max-w-[165px] object-contain dark:hidden lg:[.sm-nav_&]:hidden">
        <img src="{{ $darkLogo }}" alt="{{ $siteSettings->site_title }}"
            class="hidden h-9 w-auto max-w-[165px] object-contain dark:block lg:[.sm-nav_&]:hidden {{ $usingDefaultDarkLogo ? 'rounded-md bg-white px-2' : '' }}">
        <img src="{{ $icon }}" alt="{{ $siteSettings->site_title }}"
            class="hidden size-9 rounded-md object-contain lg:[.sm-nav_&]:block">
    </a>
</div>
