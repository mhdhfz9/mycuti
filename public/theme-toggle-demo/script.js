(() => {
    const root = document.documentElement;
    const STORAGE_KEY = 'theme';

    /**
     * Resolve the theme to use on first paint: whatever was saved last
     * time, falling back to the OS-level preference.
     */
    function getPreferredTheme() {
        const stored = localStorage.getItem(STORAGE_KEY);
        if (stored === 'light' || stored === 'dark') {
            return stored;
        }

        return window.matchMedia('(prefers-color-scheme: dark)').matches ? 'dark' : 'light';
    }

    function getCurrentTheme() {
        return root.getAttribute('data-theme') === 'dark' ? 'dark' : 'light';
    }

    function applyTheme(theme) {
        root.setAttribute('data-theme', theme);
        localStorage.setItem(STORAGE_KEY, theme);

        document.querySelectorAll('.theme-toggle').forEach((button) => {
            button.setAttribute('aria-pressed', theme === 'dark' ? 'true' : 'false');
        });
    }

    // Apply the resolved theme immediately, before anything else runs.
    applyTheme(getPreferredTheme());

    const prefersReducedMotion = () =>
        window.matchMedia('(prefers-reduced-motion: reduce)').matches;

    /**
     * Toggle the theme, animating the change as a circle that expands
     * outward from (x, y) to cover the full viewport.
     */
    function toggleThemeFrom(x, y) {
        const next = getCurrentTheme() === 'dark' ? 'light' : 'dark';

        // Fallback: no View Transitions support, or the user asked for
        // reduced motion — just swap the theme instantly.
        if (!document.startViewTransition || prefersReducedMotion()) {
            applyTheme(next);
            return;
        }

        const endRadius = Math.hypot(
            Math.max(x, window.innerWidth - x),
            Math.max(y, window.innerHeight - y),
        );

        // Suspend the CSS color transitions declared in style.css for the
        // duration of the view transition, so they can't run underneath
        // the clip-path reveal and cause a flash of the wrong colors.
        root.classList.add('disable-transitions');

        const transition = document.startViewTransition(() => {
            applyTheme(next);
        });

        transition.ready
            .then(() => {
                root.animate(
                    {
                        clipPath: [
                            `circle(0px at ${x}px ${y}px)`,
                            `circle(${endRadius}px at ${x}px ${y}px)`,
                        ],
                    },
                    {
                        duration: 600,
                        easing: 'ease-in-out',
                        pseudoElement: '::view-transition-new(root)',
                    },
                );
            })
            .catch(() => {
                // The transition can be skipped (e.g. the page was hidden
                // mid-animation) — that's fine, the theme is already applied.
            });

        transition.finished.finally(() => {
            root.classList.remove('disable-transitions');
        });
    }

    document.querySelectorAll('.theme-toggle, #cta-toggle').forEach((button) => {
        button.addEventListener('click', (event) => {
            toggleThemeFrom(event.clientX, event.clientY);
        });
    });

    // Keep this tab in sync if the theme is changed in another tab.
    window.addEventListener('storage', (event) => {
        if (event.key === STORAGE_KEY && (event.newValue === 'light' || event.newValue === 'dark')) {
            applyTheme(event.newValue);
        }
    });
})();
