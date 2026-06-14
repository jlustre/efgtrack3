export default function dashboardStats(initial = {}) {
    const detailsUrlTemplate = initial.detailsUrlTemplate ?? '';

    const scopeLabels = {
        full_downline: 'Your full downline',
        direct_downline: 'Your direct downline',
        self: 'Your profile',
    };

    return {
        modalOpen: false,
        loading: false,
        error: null,
        modalTitle: '',
        modalScopeLabel: '',
        members: [],

        detailsUrl(type) {
            return detailsUrlTemplate.replace('__TYPE__', encodeURIComponent(type));
        },

        async openModal(type, label) {
            this.modalOpen = true;
            this.loading = true;
            this.error = null;
            this.members = [];
            this.modalTitle = label;
            this.modalScopeLabel = 'Loading scope...';

            try {
                const response = await fetch(this.detailsUrl(type), {
                    headers: {
                        Accept: 'application/json',
                        'X-Requested-With': 'XMLHttpRequest',
                    },
                    credentials: 'same-origin',
                });

                if (! response.ok) {
                    throw new Error('Unable to load member details right now.');
                }

                const payload = await response.json();
                this.modalTitle = payload.title ?? label;
                this.modalScopeLabel = scopeLabels[payload.scope] ?? 'Members in scope';
                this.members = payload.members ?? [];
            } catch (error) {
                this.error = error instanceof Error ? error.message : 'Unable to load member details right now.';
            } finally {
                this.loading = false;
            }
        },

        closeModal() {
            this.modalOpen = false;
            this.loading = false;
            this.error = null;
            this.members = [];
        },
    };
}
