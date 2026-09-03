<x-layouts::app :title="__('Dashboard')">
    @php $user = auth()->user(); @endphp

    <div class="flex w-full flex-1 flex-col gap-6">
        <div>
            <flux:heading size="xl">{{ __('Welcome back, :name', ['name' => explode(' ', $user->name)[0]]) }}</flux:heading>
            <flux:subheading>{{ __('Here is an overview of your account.') }}</flux:subheading>
        </div>

        <div class="grid gap-6 lg:grid-cols-3">
            {{-- Profile --}}
            <div class="rounded-2xl border border-zinc-200 bg-white/70 p-6 backdrop-blur-xl lg:col-span-2 dark:border-white/10 dark:bg-white/5">
                <div class="flex items-center gap-4">
                    <flux:avatar :name="$user->name" :initials="$user->initials()" size="xl" color="auto" />

                    <div>
                        <flux:heading size="lg">{{ $user->name }}</flux:heading>
                        <flux:text class="text-zinc-500 dark:text-white/50">
                            {{ __('Member since :date', ['date' => $user->created_at->format('F Y')]) }}
                        </flux:text>
                    </div>
                </div>

                <dl class="mt-6">
                    <dt class="text-xs font-medium tracking-wide text-zinc-500 uppercase dark:text-white/40">
                        {{ __('Email address') }}
                    </dt>
                    <dd class="mt-1 font-medium">{{ $user->email }}</dd>
                </dl>

                <div class="mt-6">
                    <flux:input
                        :label="__('IC number (encrypted)')"
                        :description="__('Stored using AES-256 encryption — this is the ciphertext, never the plaintext value.')"
                        :value="$user->encryptedIcNumber()"
                        icon="lock-closed"
                        readonly
                        copyable
                        class="font-mono text-xs"
                    />
                </div>

                <div class="mt-6">
                    <flux:button :href="route('profile.edit')" wire:navigate variant="outline" icon="pencil-square">
                        {{ __('Edit profile') }}
                    </flux:button>
                </div>
            </div>

            {{-- Security status --}}
            <div class="rounded-2xl border border-zinc-200 bg-white/70 p-6 backdrop-blur-xl dark:border-white/10 dark:bg-white/5">
                <flux:heading size="lg">{{ __('Security') }}</flux:heading>

                <ul class="mt-4 space-y-4 text-sm">
                    <li class="flex items-center justify-between gap-4">
                        <span class="text-zinc-600 dark:text-white/60">{{ __('Email verified') }}</span>
                        @if ($user->email_verified_at)
                            <flux:badge color="emerald" size="sm">{{ __('Verified') }}</flux:badge>
                        @else
                            <flux:badge color="amber" size="sm">{{ __('Unverified') }}</flux:badge>
                        @endif
                    </li>

                    <li class="flex items-center justify-between gap-4">
                        <span class="text-zinc-600 dark:text-white/60">{{ __('Two-factor authentication') }}</span>
                        @if ($user->hasEnabledTwoFactorAuthentication())
                            <flux:badge color="emerald" size="sm">{{ __('Enabled') }}</flux:badge>
                        @else
                            <flux:badge size="sm">{{ __('Disabled') }}</flux:badge>
                        @endif
                    </li>

                    <li class="flex items-center justify-between gap-4">
                        <span class="text-zinc-600 dark:text-white/60">{{ __('Passkeys') }}</span>
                        <flux:badge size="sm">{{ $user->passkeys()->count() }}</flux:badge>
                    </li>
                </ul>

                <flux:button :href="route('security.edit')" wire:navigate variant="ghost" class="mt-6 w-full" icon="shield-check">
                    {{ __('Manage security') }}
                </flux:button>
            </div>
        </div>
    </div>
</x-layouts::app>
