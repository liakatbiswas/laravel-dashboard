{{-- Shared by create and edit forms. Expects $roles (role names), $selectedRoles, and $user when editing. --}}
@php
    $editing = isset($user) && $user->exists;
    $checkedRoles = (array) old('roles', $selectedRoles ?? []);
@endphp

<div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
    <x-field label="Full name" name="name" :value="$editing ? $user->name : null" required />
    <x-field label="Email address" name="email" type="email" :value="$editing ? $user->email : null" required />
    <x-field label="Phone" name="phone" :value="$editing ? $user->phone : null" />

    <fieldset class="sm:col-span-2">
        <legend class="mb-1.5 block text-sm font-medium text-head">{{ __('Roles') }}</legend>
        <div class="grid grid-cols-1 gap-2 rounded-md border border-line bg-card px-3 py-3 sm:grid-cols-2">
            @forelse ($roles as $role)
                <label class="flex items-center gap-2 text-sm text-ink">
                    <input type="checkbox" name="roles[]" value="{{ $role }}" class="size-4 accent-primary"
                        @checked(in_array($role, $checkedRoles, true))>
                    {{ ucfirst($role) }}
                </label>
            @empty
                <p class="text-sm text-mute">{{ __('No roles are available to you.') }}</p>
            @endforelse
        </div>
        @error('roles')
            <p class="mt-1 text-sm text-bad">{{ $message }}</p>
        @enderror
        @error('roles.*')
            <p class="mt-1 text-sm text-bad">{{ $message }}</p>
        @enderror
    </fieldset>
</div>

@if (!$editing)
    <p class="mt-4 rounded-md bg-tint px-4 py-3 text-sm text-primary">{{ __('An invitation link will be sent to the user\'s email address. They can use this link to set their own password.') }} </p>
@endif
