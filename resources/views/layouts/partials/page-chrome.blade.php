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

<script>
    (function () {
        function showPageLoader() {
            var el = document.getElementById('efg-page-loading');

            if (! el) {
                return;
            }

            el.classList.remove('hidden');
            el.style.removeProperty('display');
            el.setAttribute('aria-hidden', 'false');
        }

        document.addEventListener('click', function (event) {
            var link = event.target.closest('a[href]');

            if (! link || link.target === '_blank' || link.hasAttribute('download')) {
                return;
            }

            var href = link.getAttribute('href');

            if (! href || href.charAt(0) === '#' || href.indexOf('mailto:') === 0 || href.indexOf('tel:') === 0) {
                return;
            }

            if (event.metaKey || event.ctrlKey || event.shiftKey || event.altKey) {
                return;
            }

            try {
                if (new URL(link.href, window.location.href).origin === window.location.origin) {
                    showPageLoader();
                }
            } catch (error) {
                return;
            }
        }, true);

        document.addEventListener('submit', function (event) {
            var form = event.target;

            if (! form || form.tagName !== 'FORM' || form.target === '_blank') {
                return;
            }

            if (event.defaultPrevented || form.hasAttribute('data-no-page-loader')) {
                return;
            }

            if (form.closest('[wire\\:id]') || Array.prototype.some.call(form.attributes, function (attribute) {
                return attribute.name.indexOf('wire:') === 0;
            })) {
                return;
            }

            showPageLoader();
            window.setTimeout(hidePageLoader, 15000);
        });

        window.addEventListener('beforeunload', showPageLoader);

        function hidePageLoader() {
            var el = document.getElementById('efg-page-loading');

            if (! el) {
                return;
            }

            el.classList.add('hidden');
            el.style.display = 'none';
            el.setAttribute('aria-hidden', 'true');
        }

        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', hidePageLoader, { once: true });
        } else {
            hidePageLoader();
        }

        window.addEventListener('load', hidePageLoader, { once: true });
        window.setTimeout(hidePageLoader, 12000);
        document.addEventListener('livewire:navigated', hidePageLoader);
    })();
</script>

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
