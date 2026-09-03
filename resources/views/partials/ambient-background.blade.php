<div
    x-data="{
        tScroll: 0,
        cScroll: 0,
        raf: null,
        onScroll() {
            this.tScroll = window.scrollY;
        },
        tick(time) {
            const ease = 0.05;
            this.cScroll += (this.tScroll - this.cScroll) * ease;

            const t = (time || 0) / 1000;

            const f1x = Math.sin(t * 0.45) * 110;
            const f1y = Math.cos(t * 0.33) * 90;
            const f2x = Math.cos(t * 0.38 + 2) * 130;
            const f2y = Math.sin(t * 0.52 + 2) * 80;
            const f3x = Math.sin(t * 0.3 + 4) * 100;
            const f3y = Math.cos(t * 0.42 + 4) * 120;

            this.$refs.blob1.style.transform = `translate3d(${f1x}px, ${this.cScroll * 0.08 + f1y}px, 0)`;
            this.$refs.blob2.style.transform = `translate3d(${f2x}px, ${this.cScroll * -0.05 + f2y}px, 0)`;
            this.$refs.blob3.style.transform = `translate3d(${f3x}px, ${this.cScroll * 0.12 + f3y}px, 0)`;

            this.raf = requestAnimationFrame((t) => this.tick(t));
        },
    }"
    x-init="$nextTick(() => tick())"
    x-on:scroll.window.passive="onScroll()"
    x-on:destroy.window="cancelAnimationFrame(raf)"
    class="pointer-events-none fixed inset-0 -z-10 overflow-hidden"
>
    <div class="absolute inset-0 bg-gradient-to-br from-white via-emerald-50/60 to-white dark:from-zinc-950 dark:via-emerald-950/40 dark:to-zinc-950"></div>
    <div x-ref="blob1" class="absolute -top-40 left-1/4 h-96 w-96 rounded-full bg-emerald-500/15 blur-[120px] will-change-transform dark:bg-emerald-500/35"></div>
    <div x-ref="blob2" class="absolute top-1/3 -right-32 h-96 w-96 rounded-full bg-teal-400/15 blur-[120px] will-change-transform dark:bg-teal-400/25"></div>
    <div x-ref="blob3" class="absolute bottom-0 left-1/3 h-96 w-96 rounded-full bg-cyan-500/10 blur-[120px] will-change-transform dark:bg-cyan-500/15"></div>
</div>
