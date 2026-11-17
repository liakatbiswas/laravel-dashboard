<x-app-layout title="Dashboard">
    @php
        $stats = [];
        if ($showStats) {
            $stats[] = ['label' => 'Total users', 'value' => $totalUsers, 'icon' => 'users', 'tone' => 'b'];
            $stats[] = ['label' => 'Active users', 'value' => $activeUsers, 'icon' => 'check', 'tone' => 'g'];
            $stats[] = ['label' => 'Roles', 'value' => $totalRoles, 'icon' => 'lock', 'tone' => 's'];
        }
        $stats[] = [
            'label' => 'Registration',
            'value' => $admissionOn ? __('Open') : __('Closed'),
            'icon' => 'gear',
            'tone' => $admissionOn ? 'g' : 'y',
        ];
        $tones = [
            'b' => 'bg-tint text-primary',
            'g' => 'bg-ok/15 text-ok',
            's' => 'bg-info/15 text-info',
            'y' => 'bg-warn/15 text-warn',
        ];
    @endphp

    <div class="grid grid-cols-1 gap-5 sm:grid-cols-2 xl:grid-cols-4">
        @foreach ($stats as $stat)
            <x-card class="flex items-center gap-4 p-5">
                <span class="grid size-12 flex-none place-items-center rounded-md {{ $tones[$stat['tone']] }}">
                    <x-icon :name="$stat['icon']" class="size-6" />
                </span>
                <div>
                    <p class="text-sm text-mute">{{ __($stat['label']) }}</p>
                    <p class="text-2xl font-medium text-head">{{ is_string($stat['value']) ? __($stat['value']) : $stat['value'] }}</p>
                </div>
            </x-card>
        @endforeach
    </div>

    <x-card class="mt-6 p-6">
        <h2 class="text-[15px] font-medium text-head">{{ __('Welcome, :name', ['name' => auth()->user()->name]) }}</h2>
        <p class="mt-1 text-mute">
            @canany(['users.view', 'users.create', 'roles.manage', 'activity.view', 'settings.site.manage', 'admission.manage'])
                {{ __('Use the menu on the left to manage users, roles and settings. Your own profile is under My profile.') }}
            @else
                {{ __('Your profile is available under My profile in the account menu.') }}
            @endcanany
        </p>
    </x-card>
</x-app-layout>
