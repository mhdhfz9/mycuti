<button
    type="button"
    x-data="{
        toggle() {
            const next = $flux.appearance === 'dark' ? 'light' : 'dark';

            if (! document.startViewTransition || window.matchMedia('(prefers-reduced-motion: reduce)').matches) {
                $flux.appearance = next;
                return;
            }

            const rect = $el.getBoundingClientRect();
            const x = rect.left + rect.width / 2;
            const y = rect.top + rect.height / 2;
            const r = Math.hypot(
                Math.max(x, window.innerWidth - x),
                Math.max(y, window.innerHeight - y),
            );

            document.documentElement.style.setProperty('--theme-toggle-x', `${x}px`);
            document.documentElement.style.setProperty('--theme-toggle-y', `${y}px`);
            document.documentElement.style.setProperty('--theme-toggle-r', `${r}px`);

            document.startViewTransition(() => { $flux.appearance = next; });
        },
    }"
    x-on:click="toggle()"
    class="inline-flex size-9 shrink-0 items-center justify-center rounded-xl border border-zinc-200 bg-white/50 text-zinc-600 backdrop-blur-xl transition hover:bg-zinc-900/5 dark:border-white/15 dark:bg-white/5 dark:text-white/80 dark:hover:bg-white/10"
    aria-label="{{ __('Toggle appearance') }}"
>
    <flux:icon icon="sun" class="size-4.5 dark:hidden" />
    <flux:icon icon="moon" class="hidden size-4.5 dark:block" />
</button>
