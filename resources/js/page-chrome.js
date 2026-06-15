let initialized = false;

export function initPageChrome() {
    if (initialized) {
        return;
    }

    initialized = true;

    const overlay = document.getElementById('efg-page-loading');

    const showLoading = () => {
        overlay?.classList.remove('hidden');
        overlay?.setAttribute('aria-hidden', 'false');
    };

    const hideLoading = () => {
        overlay?.classList.add('hidden');
        if (overlay) {
            overlay.style.display = 'none';
        }
        overlay?.setAttribute('aria-hidden', 'true');
    };

    const shouldHandleLink = (anchor) => {
        if (! anchor || anchor.target === '_blank' || anchor.hasAttribute('download')) {
            return false;
        }

        const href = anchor.getAttribute('href');

        if (! href || href.startsWith('#') || href.startsWith('mailto:') || href.startsWith('tel:') || href.startsWith('javascript:')) {
            return false;
        }

        try {
            const url = new URL(anchor.href, window.location.href);

            return url.origin === window.location.origin;
        } catch {
            return false;
        }
    };

    const scheduleHideLoading = () => {
        hideLoading();
        window.setTimeout(hideLoading, 8000);
    };

    if (document.readyState === 'interactive' || document.readyState === 'complete') {
        scheduleHideLoading();
    } else {
        document.addEventListener('DOMContentLoaded', scheduleHideLoading, { once: true });
    }

    window.addEventListener('load', hideLoading, { once: true });

    window.addEventListener('pageshow', (event) => {
        if (event.persisted) {
            hideLoading();
        }
    });

    document.addEventListener('click', (event) => {
        const link = event.target.closest('a[href]');

        if (! shouldHandleLink(link)) {
            return;
        }

        if (event.metaKey || event.ctrlKey || event.shiftKey || event.altKey) {
            return;
        }

        showLoading();
    });

    document.addEventListener('submit', (event) => {
        const form = event.target;

        if (! (form instanceof HTMLFormElement) || form.target === '_blank') {
            return;
        }

        if (form.hasAttribute('data-no-page-loader')) {
            return;
        }

        showLoading();
    });

    window.addEventListener('beforeunload', showLoading);
}

if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', initPageChrome);
} else {
    initPageChrome();
}
