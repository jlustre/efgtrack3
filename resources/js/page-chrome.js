let initialized = false;

function getOverlay() {
    return document.getElementById('efg-page-loading');
}

function showLoading() {
    const overlay = getOverlay();

    if (! overlay) {
        return;
    }

    overlay.classList.remove('hidden');
    overlay.style.removeProperty('display');
    overlay.setAttribute('aria-hidden', 'false');
}

function hideLoading() {
    const overlay = getOverlay();

    if (! overlay) {
        return;
    }

    overlay.classList.add('hidden');
    overlay.style.display = 'none';
    overlay.setAttribute('aria-hidden', 'true');
}

function shouldHandleLink(anchor) {
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
}

function initGoToTop() {
    const goTop = document.getElementById('efg-go-to-top');

    if (! goTop) {
        return;
    }

    const scrollPosition = () => Math.max(
        window.scrollY || 0,
        document.documentElement.scrollTop || 0,
        document.body.scrollTop || 0,
    );

    const updateGoTop = () => {
        goTop.classList.toggle('efg-go-to-top--visible', scrollPosition() > 100);
    };

    const scrollToTop = () => {
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

        const step = (now) => {
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
}

export function initPageChrome() {
    if (initialized) {
        return;
    }

    initialized = true;

    showLoading();

    const hideWhenReady = () => hideLoading();

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', hideWhenReady, { once: true });
    } else {
        hideWhenReady();
    }

    window.addEventListener('load', hideWhenReady, { once: true });
    window.setTimeout(hideWhenReady, 12000);

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
    initGoToTop();
}

if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', initPageChrome);
} else {
    initPageChrome();
}
