<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        @include('partials.head', ['title' => __('Modern Leave Management')])
    </head>
    <body class="min-h-screen bg-white text-zinc-900 antialiased selection:bg-emerald-400/30 dark:bg-zinc-950 dark:text-white">
        <div class="relative isolate overflow-hidden">
            @include('partials.ambient-background')

            {{-- Navbar --}}
            <header class="sticky top-0 z-50 px-4 pt-4 sm:px-6 lg:px-8">
                <nav class="mx-auto grid max-w-6xl grid-cols-2 items-center gap-4 rounded-2xl border border-zinc-200 bg-white/70 px-5 py-3 shadow-lg shadow-zinc-900/5 backdrop-blur-xl md:grid-cols-[1fr_auto_1fr] dark:border-white/10 dark:bg-white/5 dark:shadow-black/10">
                    <a href="{{ url('/') }}" class="flex items-center gap-2 justify-self-start" wire:navigate>
                        <x-app-logo-icon class="size-10" />
                        <span class="text-lg font-semibold tracking-tight">
                            <span class="text-zinc-900 dark:text-white">My</span><span class="bg-gradient-to-r from-emerald-300 to-teal-300 bg-clip-text text-transparent">Cuti</span>
                        </span>
                    </a>

                    <div class="hidden items-center gap-8 text-sm font-medium text-zinc-600 md:flex dark:text-white/70">
                        <a href="#features" class="transition hover:text-zinc-900 dark:hover:text-white">{{ __('Features') }}</a>
                        <a href="#workflow" class="transition hover:text-zinc-900 dark:hover:text-white">{{ __('How it works') }}</a>
                        <a href="#faq" class="transition hover:text-zinc-900 dark:hover:text-white">{{ __('FAQ') }}</a>
                    </div>

                    <div class="flex items-center justify-self-end gap-2">
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
                            @if (Route::has('register'))
                                <a
                                    href="{{ route('register') }}"
                                    wire:navigate
                                    class="hidden items-center rounded-xl bg-gradient-to-br from-emerald-400 to-teal-500 px-4 py-2 text-sm font-semibold text-zinc-900 shadow-md shadow-emerald-500/30 transition hover:brightness-110 sm:inline-flex"
                                >
                                    {{ __('Get started') }}
                                </a>
                            @endif
                        @endauth
                    </div>
                </nav>
            </header>

            {{-- Hero --}}
            <main>
                <section class="relative px-4 pt-20 pb-24 sm:px-6 sm:pt-28 lg:px-8">
                    <div class="mx-auto max-w-4xl text-center">
                        <span class="inline-flex items-center gap-2 rounded-full border border-zinc-200 bg-white/70 px-4 py-1.5 text-xs font-medium text-emerald-600 backdrop-blur-xl dark:border-white/10 dark:bg-white/5 dark:text-emerald-300">
                            <span class="size-1.5 rounded-full bg-emerald-400"></span>
                            {{ __('Leave management, simplified') }}
                        </span>

                        <h1 class="mt-6 text-4xl font-semibold tracking-tight text-balance sm:text-6xl">
                            {{ __('Time off, tracked') }}
                            <span class="bg-gradient-to-r from-emerald-300 via-teal-300 to-cyan-300 bg-clip-text text-transparent">{{ __('beautifully.') }}</span>
                        </h1>

                        <p class="mx-auto mt-6 max-w-2xl text-lg text-zinc-600 dark:text-white/60">
                            {{ __('MyCuti gives your team a clean, transparent way to request, approve, and track leave — balances, approvals, and calendars, all in one place.') }}
                        </p>

                        <div class="mt-10 flex flex-col items-center justify-center gap-4 sm:flex-row">
                            @auth
                                <a
                                    href="{{ route('dashboard') }}"
                                    wire:navigate
                                    class="inline-flex w-full items-center justify-center rounded-xl bg-gradient-to-br from-emerald-400 to-teal-500 px-6 py-3 text-sm font-semibold text-zinc-900 shadow-lg shadow-emerald-500/30 transition hover:brightness-110 sm:w-auto"
                                >
                                    {{ __('Go to dashboard') }}
                                </a>
                            @else
                                @if (Route::has('register'))
                                    <a
                                        href="{{ route('register') }}"
                                        wire:navigate
                                        class="inline-flex w-full items-center justify-center rounded-xl bg-gradient-to-br from-emerald-400 to-teal-500 px-6 py-3 text-sm font-semibold text-zinc-900 shadow-lg shadow-emerald-500/30 transition hover:brightness-110 sm:w-auto"
                                    >
                                        {{ __('Create free account') }}
                                    </a>
                                @endif
                                @if (Route::has('login'))
                                    <a
                                        href="{{ route('login') }}"
                                        wire:navigate
                                        class="inline-flex w-full items-center justify-center rounded-xl border border-zinc-200 bg-zinc-900/5 px-6 py-3 text-sm font-medium text-zinc-700 backdrop-blur-xl transition hover:bg-zinc-900/10 sm:w-auto dark:border-white/15 dark:bg-white/5 dark:text-white/90 dark:hover:bg-white/10"
                                    >
                                        {{ __('Log in') }}
                                    </a>
                                @endif
                            @endauth
                        </div>
                    </div>

                    {{-- Glass preview card --}}
                    <div class="mx-auto mt-16 max-w-4xl">
                        <div class="rounded-3xl border border-zinc-200 bg-white/70 p-2 shadow-2xl shadow-zinc-900/10 backdrop-blur-2xl dark:border-white/10 dark:bg-white/5 dark:shadow-black/30">
                            <div class="rounded-2xl border border-zinc-200 bg-gradient-to-b from-zinc-900/[0.03] to-transparent p-6 sm:p-8 dark:border-white/10 dark:from-white/[0.06]">
                                <div class="grid gap-4 sm:grid-cols-3">
                                    <div class="rounded-2xl border border-zinc-200 bg-white/70 p-5 backdrop-blur-xl dark:border-white/10 dark:bg-white/5">
                                        <p class="text-xs font-medium text-zinc-500 dark:text-white/50">{{ __('Annual leave balance') }}</p>
                                        <p class="mt-2 text-3xl font-semibold">14<span class="text-base font-normal text-zinc-400 dark:text-white/40"> / 18 {{ __('days') }}</span></p>
                                        <div class="mt-4 h-1.5 w-full overflow-hidden rounded-full bg-zinc-200 dark:bg-white/10">
                                            <div class="h-full w-4/5 rounded-full bg-gradient-to-r from-emerald-400 to-teal-400"></div>
                                        </div>
                                    </div>
                                    <div class="rounded-2xl border border-zinc-200 bg-white/70 p-5 backdrop-blur-xl dark:border-white/10 dark:bg-white/5">
                                        <p class="text-xs font-medium text-zinc-500 dark:text-white/50">{{ __('Pending approvals') }}</p>
                                        <p class="mt-2 text-3xl font-semibold">3</p>
                                        <p class="mt-4 inline-flex items-center gap-1.5 rounded-full bg-amber-400/10 px-2.5 py-1 text-xs font-medium text-amber-600 dark:text-amber-300">
                                            <span class="size-1.5 rounded-full bg-amber-400"></span>
                                            {{ __('Awaiting review') }}
                                        </p>
                                    </div>
                                    <div class="rounded-2xl border border-zinc-200 bg-white/70 p-5 backdrop-blur-xl dark:border-white/10 dark:bg-white/5">
                                        <p class="text-xs font-medium text-zinc-500 dark:text-white/50">{{ __('Team out this week') }}</p>
                                        <p class="mt-2 text-3xl font-semibold">2</p>
                                        <div class="mt-4 flex -space-x-2">
                                            <span class="size-6 rounded-full border-2 border-white bg-gradient-to-br from-emerald-400 to-teal-500 dark:border-zinc-900"></span>
                                            <span class="size-6 rounded-full border-2 border-white bg-gradient-to-br from-cyan-400 to-blue-500 dark:border-zinc-900"></span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </section>

                {{-- Features --}}
                <section id="features" class="px-4 py-24 sm:px-6 lg:px-8">
                    <div class="mx-auto max-w-6xl">
                        <div class="mx-auto max-w-2xl text-center">
                            <h2 class="text-3xl font-semibold tracking-tight sm:text-4xl">{{ __('Everything leave management needs') }}</h2>
                            <p class="mt-4 text-zinc-600 dark:text-white/60">{{ __('No spreadsheets, no chasing approvals over chat — just a clear system everyone trusts.') }}</p>
                        </div>

                        <div class="mt-14 grid gap-6 sm:grid-cols-2 lg:grid-cols-3">
                            @foreach ([
                                ['icon' => 'calendar-days', 'title' => __('One-click requests'), 'desc' => __('Submit a leave request in seconds and track its status in real time.')],
                                ['icon' => 'check-circle', 'title' => __('Streamlined approvals'), 'desc' => __('Managers review, approve, or decline requests from one clean queue.')],
                                ['icon' => 'chart-pie', 'title' => __('Live balances'), 'desc' => __('Everyone always knows exactly how many days they have left.')],
                                ['icon' => 'users', 'title' => __('Team visibility'), 'desc' => __('See who is out and when, so schedules never collide.')],
                                ['icon' => 'bell', 'title' => __('Instant notifications'), 'desc' => __('Requests, approvals, and reminders land the moment they happen.')],
                                ['icon' => 'shield-check', 'title' => __('Policy-aware'), 'desc' => __('Leave types and entitlements enforced automatically, every time.')],
                            ] as $feature)
                                <div class="group relative overflow-hidden rounded-2xl border border-zinc-200 bg-white/70 p-6 backdrop-blur-xl transition hover:bg-zinc-900/[0.03] dark:border-white/10 dark:bg-white/5 dark:hover:bg-white/[0.08]">
                                    <flux:icon
                                        :icon="$feature['icon']"
                                        class="pointer-events-none absolute -top-5 -right-6 size-32 rotate-6 text-emerald-500/10 transition duration-500 group-hover:rotate-3 group-hover:scale-105 dark:text-emerald-300/15"
                                    />

                                    <div class="relative flex size-11 items-center justify-center rounded-xl bg-gradient-to-br from-emerald-400/20 to-teal-400/20 text-emerald-600 ring-1 ring-zinc-200 transition group-hover:scale-105 dark:text-emerald-300 dark:ring-white/10">
                                        <flux:icon :icon="$feature['icon']" class="size-5" />
                                    </div>
                                    <h3 class="relative mt-4 font-semibold">{{ $feature['title'] }}</h3>
                                    <p class="relative mt-2 text-sm text-zinc-600 dark:text-white/60">{{ $feature['desc'] }}</p>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </section>

                {{-- How it works --}}
                <section id="workflow" class="px-4 py-24 sm:px-6 lg:px-8">
                    <div class="mx-auto max-w-6xl rounded-3xl border border-zinc-200 bg-white/70 p-8 backdrop-blur-2xl sm:p-12 dark:border-white/10 dark:bg-white/5">
                        <div class="mx-auto max-w-2xl text-center">
                            <h2 class="text-3xl font-semibold tracking-tight sm:text-4xl">{{ __('How MyCuti works') }}</h2>
                            <p class="mt-4 text-zinc-600 dark:text-white/60">{{ __('Three steps from request to approved time off.') }}</p>
                        </div>

                        <div class="mt-12 grid gap-8 sm:grid-cols-3">
                            @foreach ([
                                ['step' => '01', 'title' => __('Request'), 'desc' => __('Pick your dates and leave type, add a note, and submit.')],
                                ['step' => '02', 'title' => __('Review'), 'desc' => __('Your manager gets notified and approves right from the dashboard.')],
                                ['step' => '03', 'title' => __('Track'), 'desc' => __('Balances update instantly and your calendar reflects the change.')],
                            ] as $item)
                                <div class="relative">
                                    <span class="text-5xl font-bold text-zinc-900/10 dark:text-white/10">{{ $item['step'] }}</span>
                                    <h3 class="mt-2 text-lg font-semibold">{{ $item['title'] }}</h3>
                                    <p class="mt-2 text-sm text-zinc-600 dark:text-white/60">{{ $item['desc'] }}</p>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </section>

                {{-- FAQ --}}
                <section id="faq" class="px-4 py-24 sm:px-6 lg:px-8">
                    <div class="mx-auto max-w-3xl">
                        <div class="text-center">
                            <h2 class="text-3xl font-semibold tracking-tight sm:text-4xl">{{ __('Frequently asked questions') }}</h2>
                        </div>

                        <div class="mt-12 space-y-4">
                            @foreach ([
                                [__('Is MyCuti free to use?'), __('Yes — creating an account and managing your team\'s leave is free to get started.')],
                                [__('Can managers see their whole team\'s schedule?'), __('Absolutely. Managers get a live view of who is on leave and when.')],
                                [__('Does it support different leave types?'), __('Yes, annual, sick, and custom leave types with their own entitlements are supported.')],
                            ] as [$q, $a])
                                <div class="rounded-2xl border border-zinc-200 bg-white/70 p-6 backdrop-blur-xl dark:border-white/10 dark:bg-white/5">
                                    <h3 class="font-semibold">{{ $q }}</h3>
                                    <p class="mt-2 text-sm text-zinc-600 dark:text-white/60">{{ $a }}</p>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </section>

                {{-- CTA --}}
                @guest
                    <section class="px-4 pb-24 sm:px-6 lg:px-8">
                        <div class="mx-auto max-w-4xl rounded-3xl border border-zinc-200 bg-gradient-to-br from-emerald-400/10 via-teal-400/10 to-cyan-400/10 p-10 text-center backdrop-blur-2xl sm:p-16 dark:border-white/10">
                            <h2 class="text-3xl font-semibold tracking-tight sm:text-4xl">{{ __('Ready to simplify leave management?') }}</h2>
                            <p class="mx-auto mt-4 max-w-xl text-zinc-600 dark:text-white/60">{{ __('Set up MyCuti for your team in minutes.') }}</p>
                            <div class="mt-8 flex flex-col items-center justify-center gap-4 sm:flex-row">
                                @if (Route::has('register'))
                                    <a
                                        href="{{ route('register') }}"
                                        wire:navigate
                                        class="inline-flex w-full items-center justify-center rounded-xl bg-gradient-to-br from-emerald-400 to-teal-500 px-6 py-3 text-sm font-semibold text-zinc-900 shadow-lg shadow-emerald-500/30 transition hover:brightness-110 sm:w-auto"
                                    >
                                        {{ __('Get started for free') }}
                                    </a>
                                @endif
                            </div>
                        </div>
                    </section>
                @endguest
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
        </div>

        @fluxScripts
    </body>
</html>
