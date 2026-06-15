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
