<button
    type="button"
    x-data="{
        toggle() {
            const next = $flux.appearance === 'dark' ? 'light' : 'dark';
            const isExpanding = next === 'dark';

            if (! document.startViewTransition || window.matchMedia('(prefers-reduced-motion: reduce)').matches) {
                $flux.appearance = next;
                return;
            }

            const rect = $el.getBoundingClientRect();
            const x = rect.left + rect.width / 2;
            const y = rect.top + rect.height / 2;

            // Position as a percentage of the viewport rather than raw
            // pixels — pixel values were landing off-target in this app's
            // layout, percentages line up correctly.
            const xPct = (x / window.innerWidth) * 100;
            const yPct = (y / window.innerHeight) * 100;

            // The farthest corner from the button, so the circle always
            // reaches the edge of the viewport — with extra room on top so
            // the reveal feels bigger/more generous than the bare minimum.
            const radius = Math.hypot(
                Math.max(x, window.innerWidth - x),
                Math.max(y, window.innerHeight - y),
            ) * 2;

            // Light → dark grows a circle of the new (dark) screen outward.
            // Dark → light instead shrinks the old (dark) screen inward,
            // revealing light underneath — so it needs the old snapshot
            // stacked on top instead of the new one.
            if (! isExpanding) {
                document.documentElement.classList.add('theme-contracting');
            }

            const transition = document.startViewTransition(() => {
                $flux.appearance = next;
            });

            transition.ready.then(() => {
                document.documentElement.animate(
                    {
                        clipPath: isExpanding
                            ? [`circle(0px at ${xPct}% ${yPct}%)`, `circle(${radius}px at ${xPct}% ${yPct}%)`]
                            : [`circle(${radius}px at ${xPct}% ${yPct}%)`, `circle(0px at ${xPct}% ${yPct}%)`],
                    },
                    {
                        duration: 1500,
                        easing: 'cubic-bezier(0.65, 0, 0.35, 1)',
                        pseudoElement: isExpanding ? '::view-transition-new(root)' : '::view-transition-old(root)',
                    },
                );
            });

            transition.finished.finally(() => {
                document.documentElement.classList.remove('theme-contracting');
            });
        },
    }"
    x-on:click="toggle()"
    class="inline-flex size-9 shrink-0 items-center justify-center rounded-xl border border-zinc-200 bg-white/50 text-zinc-600 backdrop-blur-xl transition hover:bg-zinc-900/5 dark:border-white/15 dark:bg-white/5 dark:text-white/80 dark:hover:bg-white/10"
    aria-label="{{ __('Toggle appearance') }}"
>
    <flux:icon icon="sun" class="size-4.5 dark:hidden" />
    <flux:icon icon="moon" class="hidden size-4.5 dark:block" />
</button>
