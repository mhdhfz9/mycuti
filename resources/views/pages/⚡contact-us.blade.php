<?php

use App\Concerns\ContactUsValidationRules;
use App\Models\ContactMessage;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

new #[Layout('layouts::public')] #[Title('Hubungi Kami')] class extends Component {
    use ContactUsValidationRules;

    public string $name = '';

    public string $phone = '';

    public string $email = '';

    public string $message = '';

    public bool $showSuccess = false;

    /**
     * Validate and store the contact-us submission.
     */
    public function submit(): void
    {
        $validated = $this->validate($this->contactUsRules());

        ContactMessage::create($validated);

        $this->reset(['name', 'phone', 'email', 'message']);

        $this->showSuccess = true;
    }

    /**
     * Dismiss the success confirmation.
     */
    public function dismissSuccess(): void
    {
        $this->showSuccess = false;
    }
}; ?>

<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        @include('partials.head', ['title' => __('Hubungi Kami')])
    </head>
    <body class="min-h-screen bg-white text-zinc-900 antialiased selection:bg-emerald-400/30 dark:bg-zinc-950 dark:text-white">
        <div class="relative isolate overflow-hidden">
            @include('partials.ambient-background')

            {{-- Navbar --}}
            <header class="sticky top-0 z-50 px-4 pt-4 sm:px-6 lg:px-8">
                <nav class="mx-auto flex max-w-6xl items-center justify-between rounded-2xl border border-zinc-200 bg-white/70 px-5 py-3 shadow-lg shadow-zinc-900/5 backdrop-blur-xl dark:border-white/10 dark:bg-white/5 dark:shadow-black/10">
                    <a href="{{ url('/') }}" class="flex items-center gap-2" wire:navigate>
                        <x-app-logo-icon class="size-10" />
                        <span class="text-lg font-semibold tracking-tight">
                            <span class="text-zinc-900 dark:text-white">My</span><span class="bg-gradient-to-r from-emerald-300 to-teal-300 bg-clip-text text-transparent">Cuti</span>
                        </span>
                    </a>

                    <div class="hidden items-center gap-8 text-sm font-medium text-zinc-600 md:flex dark:text-white/70">
                        <a href="{{ url('/') }}#features" class="transition hover:text-zinc-900 dark:hover:text-white" wire:navigate>{{ __('Features') }}</a>
                        <a href="{{ url('/') }}#workflow" class="transition hover:text-zinc-900 dark:hover:text-white" wire:navigate>{{ __('How it works') }}</a>
                        <a href="{{ url('/') }}#faq" class="transition hover:text-zinc-900 dark:hover:text-white" wire:navigate>{{ __('FAQ') }}</a>
                        <a href="{{ route('contact-us') }}" class="text-zinc-900 dark:text-white" wire:navigate>{{ __('Contact Us') }}</a>
                    </div>

                    <div class="flex items-center gap-2">
                        @include('partials.appearance-toggle')

                        @auth
                            <a
                                href="{{ route('dashboard') }}"
                                wire:navigate
                                class="inline-flex items-center rounded-xl bg-gradient-to-br from-emerald-400 to-teal-500 px-4 py-2 text-sm font-semibold text-zinc-900 shadow-md shadow-emerald-500/30 transition hover:brightness-110"
                            >
                                {{ __('Dashboard') }}
                            </a>
                        @else
                            @if (Route::has('login'))
                                <a
                                    href="{{ route('login') }}"
                                    wire:navigate
                                    class="inline-flex items-center rounded-xl border border-zinc-200 bg-zinc-900/5 px-4 py-2 text-sm font-medium text-zinc-700 backdrop-blur-xl transition hover:bg-zinc-900/10 dark:border-white/15 dark:bg-white/5 dark:text-white/90 dark:hover:bg-white/10"
                                >
                                    {{ __('Log in') }}
                                </a>
                            @endif
                        @endauth
                    </div>
                </nav>
            </header>

            <main class="px-4 py-16 sm:px-6 lg:px-8">
                <div class="mx-auto max-w-6xl">
                    <div class="mx-auto max-w-2xl text-center">
                        <h1 class="text-4xl font-semibold tracking-tight text-balance sm:text-5xl">
                            {{ __('Hubungi') }} <span class="bg-gradient-to-r from-emerald-300 via-teal-300 to-cyan-300 bg-clip-text text-transparent">{{ __('Kami') }}</span>
                        </h1>
                        <p class="mx-auto mt-4 max-w-xl text-zinc-600 dark:text-white/60">
                            {{ __('Ada soalan atau perlukan bantuan? Isikan borang di bawah dan pasukan kami akan menghubungi anda.') }}
                        </p>
                    </div>

                    <div class="mt-14 grid gap-8 lg:grid-cols-2 lg:items-stretch">
                        {{-- Left: image --}}
                        <div class="relative hidden overflow-hidden rounded-3xl border border-zinc-200 bg-white/70 shadow-2xl shadow-zinc-900/10 backdrop-blur-2xl lg:block dark:border-white/10 dark:bg-white/5 dark:shadow-black/30">
                            <img
                                src="{{ asset('images/contact-us-placeholder.svg') }}"
                                alt="{{ __('Hubungi pasukan MyCuti') }}"
                                class="h-full w-full object-cover"
                            />
                        </div>

                        {{-- Right: form --}}
                        <div class="rounded-3xl border border-zinc-200 bg-white/70 p-6 shadow-2xl shadow-zinc-900/10 backdrop-blur-2xl sm:p-8 dark:border-white/10 dark:bg-white/5 dark:shadow-black/30">
                            <form wire:submit="submit" class="flex flex-col gap-6">
                                <flux:input
                                    wire:model="name"
                                    :label="__('Nama Penuh')"
                                    type="text"
                                    required
                                    autocomplete="name"
                                    :placeholder="__('Nama penuh anda')"
                                />

                                <flux:input
                                    wire:model="phone"
                                    :label="__('Nombor Telefon')"
                                    type="tel"
                                    required
                                    autocomplete="tel"
                                    placeholder="012-3456789"
                                />

                                <flux:input
                                    wire:model="email"
                                    :label="__('Emel')"
                                    type="email"
                                    required
                                    autocomplete="email"
                                    placeholder="nama@contoh.com"
                                />

                                <flux:textarea
                                    wire:model="message"
                                    :label="__('Mesej')"
                                    required
                                    rows="5"
                                    maxlength="1000"
                                    :placeholder="__('Tulis mesej anda di sini...')"
                                    :description="__(':count / 1000 aksara', ['count' => strlen($message)])"
                                />

                                <flux:button type="submit" variant="primary" class="w-full">
                                    {{ __('Hantar Mesej') }}
                                </flux:button>
                            </form>
                        </div>
                    </div>
                </div>
            </main>

            {{-- Footer --}}
            <footer class="border-t border-zinc-200 px-4 py-10 sm:px-6 lg:px-8 dark:border-white/10">
                <div class="mx-auto flex max-w-6xl flex-col items-center justify-between gap-4 text-sm text-zinc-500 sm:flex-row dark:text-white/40">
                    <div class="flex items-center gap-2">
                        <x-app-logo-icon class="size-7" />
                        <span>MyCuti &copy; {{ date('Y') }}</span>
                    </div>
                    <p>{{ __('Built with Laravel & Livewire.') }}</p>
                </div>
            </footer>

            <flux:modal name="contact-success" :show="$showSuccess" class="max-w-md">
                <div class="flex flex-col items-center gap-4 text-center">
                    <div class="flex size-14 items-center justify-center rounded-full bg-gradient-to-br from-emerald-400/20 to-teal-400/20 text-emerald-600 ring-1 ring-emerald-500/20 dark:text-emerald-300">
                        <flux:icon icon="check-circle" class="size-8" />
                    </div>

                    <div>
                        <flux:heading size="lg">{{ __('Terima kasih!') }}</flux:heading>
                        <flux:subheading class="mt-2">
                            {{ __('Mesej anda telah diterima. Pegawai kami akan menghubungi anda dalam tempoh 2 hari bekerja.') }}
                        </flux:subheading>
                    </div>

                    <flux:button variant="primary" wire:click="dismissSuccess" class="w-full">
                        {{ __('Tutup') }}
                    </flux:button>
                </div>
            </flux:modal>

            <flux:toast.group>
                <flux:toast />
            </flux:toast.group>
        </div>

        @fluxScripts
    </body>
</html>
