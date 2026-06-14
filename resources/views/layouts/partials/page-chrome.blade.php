<div
    id="efg-page-loading"
    class="fixed inset-0 z-[100] flex flex-col items-center justify-center gap-4 bg-[#F5F7FA]/90 backdrop-blur-sm"
    role="status"
    aria-live="polite"
    aria-label="Loading page"
>
    <div class="efg-page-spinner" aria-hidden="true"></div>
    <p class="text-sm font-semibold text-[#0B1F3A]">Loading…</p>
</div>

<button
    id="efg-go-to-top"
    type="button"
    class="efg-go-to-top"
    aria-label="Back to top"
    title="Back to top"
>
    <span class="efg-go-to-top__surface" aria-hidden="true">
        <svg class="efg-go-to-top__icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round">
            <path d="M6 14l6-6 6 6"></path>
        </svg>
    </span>
</button>

<script>
    (function () {
        const loader = document.getElementById('efg-page-loading');
        const goTop = document.getElementById('efg-go-to-top');

        const hideLoader = function () {
            if (loader) {
                loader.classList.add('hidden');
                loader.style.display = 'none';
                loader.setAttribute('aria-hidden', 'true');
            }
        };

        const scheduleHideLoader = function () {
            hideLoader();
            window.setTimeout(hideLoader, 8000);
        };

        if (document.readyState === 'interactive' || document.readyState === 'complete') {
            scheduleHideLoader();
        } else {
            document.addEventListener('DOMContentLoaded', scheduleHideLoader, { once: true });
        }

        window.addEventListener('load', hideLoader, { once: true });

        if (! goTop) {
            return;
        }

        const scrollPosition = function () {
            return Math.max(
                window.scrollY || 0,
                document.documentElement.scrollTop || 0,
                document.body.scrollTop || 0
            );
        };

        const updateGoTop = function () {
            goTop.classList.toggle('efg-go-to-top--visible', scrollPosition() > 100);
        };

        const scrollToTop = function () {
            const startY = scrollPosition();

            if (startY <= 0) {
                return;
            }

            const prefersReduced = window.matchMedia('(prefers-reduced-motion: reduce)').matches;

            if (! prefersReduced && 'scrollBehavior' in document.documentElement.style) {
                window.scrollTo({ top: 0, left: 0, behavior: 'smooth' });
                document.documentElement.scrollTo({ top: 0, left: 0, behavior: 'smooth' });

                return;
            }

            const duration = prefersReduced ? 0 : 550;
            const startTime = performance.now();

            const step = function (now) {
                const progress = Math.min((now - startTime) / duration, 1);
                const eased = 1 - Math.pow(1 - progress, 3);
                const y = Math.round(startY * (1 - eased));

                window.scrollTo(0, y);
                document.documentElement.scrollTop = y;
                document.body.scrollTop = y;

                if (progress < 1) {
                    requestAnimationFrame(step);
                }
            };

            requestAnimationFrame(step);
        };

        goTop.addEventListener('click', scrollToTop);

        window.addEventListener('scroll', updateGoTop, { passive: true });
        document.addEventListener('scroll', updateGoTop, { passive: true });
        window.addEventListener('resize', updateGoTop, { passive: true });
        window.addEventListener('load', updateGoTop);
        updateGoTop();
    })();
</script>
