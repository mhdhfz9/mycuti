<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        @include('partials.head')
    </head>
    <body class="min-h-screen bg-white text-zinc-900 antialiased selection:bg-emerald-400/30 dark:bg-zinc-950 dark:text-white">
        <div class="relative isolate flex min-h-svh flex-col overflow-hidden">
            @include('partials.ambient-background')

            {{-- Navbar --}}
            <header class="px-4 pt-4 sm:px-6 lg:px-8">
                <nav class="mx-auto flex max-w-6xl items-center justify-between rounded-2xl border border-zinc-200 bg-white/70 px-5 py-3 shadow-lg shadow-zinc-900/5 backdrop-blur-xl dark:border-white/10 dark:bg-white/5 dark:shadow-black/10">
                    <a href="{{ route('home') }}" class="flex items-center gap-2" wire:navigate>
                        <x-app-logo-icon class="size-10" />
                        <span class="text-lg font-semibold tracking-tight">
                            <span class="text-zinc-900 dark:text-white">My</span><span class="bg-gradient-to-r from-emerald-300 to-teal-300 bg-clip-text text-transparent">Cuti</span>
                        </span>
                    </a>

                    @include('partials.appearance-toggle')
                </nav>
            </header>

            {{-- Content --}}
            <main class="flex flex-1 items-center justify-center px-4 py-12 sm:px-6 lg:px-8">
                <div class="w-full max-w-sm">
                    <div class="rounded-3xl border border-zinc-200 bg-white/70 p-8 shadow-2xl shadow-zinc-900/10 backdrop-blur-2xl dark:border-white/10 dark:bg-white/5 dark:shadow-black/30">
                        {{ $slot }}
                    </div>
                </div>
            </main>
        </div>

        @persist('toast')
            <flux:toast.group>
                <flux:toast />
            </flux:toast.group>
        @endpersist

        @fluxScripts
    </body>
</html>
