const ALLOWED_PHOTO_TYPES = ['image/jpeg', 'image/png', 'image/webp'];
const MAX_PHOTO_BYTES = 2 * 1024 * 1024;

export default function profilePhotoUpload(currentUrl = null, initials = 'EF') {
    return {
        previewUrl: currentUrl,
        currentUrl,
        initials,
        imageFailed: false,

        onFileSelected(event) {
            const file = event.target.files?.[0];

            if (! file) {
                this.resetToCurrent();
                return;
            }

            if (! ALLOWED_PHOTO_TYPES.includes(file.type) || file.size > MAX_PHOTO_BYTES) {
                if (this.$refs.photoInput) {
                    this.$refs.photoInput.value = '';
                }

                this.resetToCurrent();
                return;
            }

            this.revokeBlobUrl();
            this.previewUrl = URL.createObjectURL(file);
            this.imageFailed = false;
        },

        clearPreview() {
            this.revokeBlobUrl();
            this.resetToCurrent();

            if (this.$refs.photoInput) {
                this.$refs.photoInput.value = '';
            }
        },

        onImageError() {
            if (! this.previewUrl?.startsWith('blob:')) {
                this.imageFailed = true;
            }
        },

        resetToCurrent() {
            this.previewUrl = this.currentUrl;
            this.imageFailed = false;
        },

        revokeBlobUrl() {
            if (this.previewUrl?.startsWith('blob:')) {
                URL.revokeObjectURL(this.previewUrl);
            }
        },
    };
}
