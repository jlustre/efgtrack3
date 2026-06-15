const INTERACTIVE_SELECTOR = 'a, button, input, select, textarea, label, [role="button"]';

export default function genealogyTreePan(config = {}) {
    const searchMembers = config.searchMembers ?? [];

    return {
        searchMembers,
        memberSearch: '',
        memberSearchOpen: false,
        memberSearchHighlight: -1,
        zoom: 1,
        compact: false,
        panning: false,
        panStartX: 0,
        panScrollLeft: 0,

        memberSearchMatches() {
            const term = this.memberSearch.trim().toLowerCase();

            if (term.length < 3) {
                return [];
            }

            return this.searchMembers.filter((member) => {
                const name = member.name.toLowerCase();

                return name.startsWith(term) || name.includes(term);
            });
        },

        onMemberSearchInput() {
            this.memberSearchOpen = this.memberSearchMatches().length > 0;
            this.memberSearchHighlight = -1;
        },

        closeMemberSearch() {
            this.memberSearchOpen = false;
            this.memberSearchHighlight = -1;
        },

        selectMember(member) {
            if (!member?.tree_top_url) {
                return;
            }

            window.location.assign(member.tree_top_url);
        },

        highlightNextMatch() {
            const matches = this.memberSearchMatches();

            if (matches.length === 0) {
                return;
            }

            this.memberSearchOpen = true;
            this.memberSearchHighlight = (this.memberSearchHighlight + 1) % matches.length;
        },

        highlightPreviousMatch() {
            const matches = this.memberSearchMatches();

            if (matches.length === 0) {
                return;
            }

            this.memberSearchOpen = true;
            this.memberSearchHighlight = this.memberSearchHighlight <= 0
                ? matches.length - 1
                : this.memberSearchHighlight - 1;
        },

        selectHighlightedMember() {
            const matches = this.memberSearchMatches();
            const member = matches[this.memberSearchHighlight];

            if (member) {
                this.selectMember(member);
            }
        },

        isInteractiveTarget(target) {
            return target instanceof Element && Boolean(target.closest(INTERACTIVE_SELECTOR));
        },

        startPan(event) {
            if (event.button !== 0 || this.isInteractiveTarget(event.target)) {
                return;
            }

            this.panning = true;
            this.panStartX = event.clientX;
            this.panScrollLeft = this.$refs.surface.scrollLeft;
            this.setPanCursor(true);
        },

        startPanTouch(event) {
            if (event.touches.length !== 1 || this.isInteractiveTarget(event.target)) {
                return;
            }

            this.panning = true;
            this.panStartX = event.touches[0].clientX;
            this.panScrollLeft = this.$refs.surface.scrollLeft;
            this.setPanCursor(true);
        },

        pan(event) {
            if (!this.panning) {
                return;
            }

            const clientX = event.touches ? event.touches[0].clientX : event.clientX;

            if (event.cancelable) {
                event.preventDefault();
            }

            this.$refs.surface.scrollLeft = this.panScrollLeft - (clientX - this.panStartX);
        },

        endPan() {
            if (!this.panning) {
                return;
            }

            this.panning = false;
            this.setPanCursor(false);
        },

        setPanCursor(active) {
            const surface = this.$refs.surface;

            if (!surface) {
                return;
            }

            surface.classList.toggle('cursor-grabbing', active);
            surface.classList.toggle('cursor-grab', !active);
            surface.classList.toggle('select-none', active);
        },

        zoomIn() {
            this.zoom = Math.min(1.4, Math.round((this.zoom + 0.1) * 10) / 10);
        },

        zoomOut() {
            this.zoom = Math.max(0.7, Math.round((this.zoom - 0.1) * 10) / 10);
        },
    };
}
