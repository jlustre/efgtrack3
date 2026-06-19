const ALLOWED_PHOTO_TYPES = ['image/jpeg', 'image/png', 'image/webp'];
const MAX_PHOTO_BYTES = 2 * 1024 * 1024;

export default function profilePhotoUpload(currentUrl = null, initials = 'EF', { ajax = false, destroyUrl = null } = {}) {
    return {
        previewUrl: currentUrl,
        currentUrl,
        initials,
        imageFailed: false,
        uploading: false,
        removing: false,
        feedback: null,
        feedbackType: null,
        hasStoredPhoto: Boolean(currentUrl),
        ajax,
        destroyUrl,

        onFileSelected(event) {
            const file = event.target.files?.[0];

            if (! file) {
                this.resetToCurrent();
                return;
            }

            const validationMessage = this.validatePhotoFile(file);

            if (validationMessage) {
                if (this.$refs.photoInput) {
                    this.$refs.photoInput.value = '';
                }

                this.resetToCurrent();
                this.setFeedback('error', validationMessage);

                return;
            }

            this.revokeBlobUrl();
            this.previewUrl = URL.createObjectURL(file);
            this.imageFailed = false;
            this.clearFeedback();
        },

        validatePhotoFile(file) {
            if (! file) {
                return 'Please choose a profile photo to upload.';
            }

            if (! ALLOWED_PHOTO_TYPES.includes(file.type)) {
                return 'Please choose a valid profile photo (JPEG, PNG, or WebP, up to 2 MB).';
            }

            if (file.size > MAX_PHOTO_BYTES) {
                return 'Profile photos must be 2 MB or smaller.';
            }

            return null;
        },

        clearPreview() {
            this.revokeBlobUrl();
            this.resetToCurrent();

            if (this.$refs.photoInput) {
                this.$refs.photoInput.value = '';
            }

            this.clearFeedback();
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

        clearFeedback() {
            this.feedback = null;
            this.feedbackType = null;
        },

        setFeedback(type, message) {
            this.feedbackType = type;
            this.feedback = message;
        },

        async handlePhotoSubmit(event) {
            if (! this.ajax) {
                return;
            }

            event.preventDefault();

            const form = event.target.closest('form') ?? event.target;
            const fileInput = form.querySelector('input[type="file"][name="photo"]');
            const file = fileInput?.files?.[0] ?? null;
            const validationMessage = this.validatePhotoFile(file);

            if (validationMessage) {
                this.setFeedback('error', validationMessage);

                return;
            }

            this.uploading = true;
            this.clearFeedback();

            try {
                const response = await fetch(form.action, {
                    method: 'POST',
                    body: new FormData(form),
                    headers: {
                        Accept: 'application/json',
                        'X-Requested-With': 'XMLHttpRequest',
                    },
                    credentials: 'same-origin',
                });

                const data = await this.parseJsonResponse(response);

                if (! response.ok) {
                    const message = data.errors?.photo?.[0]
                        ?? data.message
                        ?? 'Please choose a valid profile photo (JPEG, PNG, or WebP, up to 2 MB).';
                    this.setFeedback('error', message);

                    return;
                }

                this.applyPhotoUpdate(data);
                this.setFeedback('success', data.message);
            } catch {
                this.setFeedback('error', 'Upload failed. Please try again.');
            } finally {
                this.uploading = false;
            }
        },

        async parseJsonResponse(response) {
            const contentType = response.headers.get('content-type') ?? '';

            if (contentType.includes('application/json')) {
                return response.json();
            }

            throw new Error('Unexpected response format.');
        },

        async removePhotoViaAjax() {
            if (! this.ajax || ! this.destroyUrl) {
                return;
            }

            if (! confirm('Remove your profile photo?')) {
                return;
            }

            this.removing = true;
            this.clearFeedback();

            try {
                const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content ?? '';
                const response = await fetch(this.destroyUrl, {
                    method: 'POST',
                    headers: {
                        Accept: 'application/json',
                        'Content-Type': 'application/x-www-form-urlencoded',
                        'X-CSRF-TOKEN': csrfToken,
                        'X-Requested-With': 'XMLHttpRequest',
                    },
                    body: new URLSearchParams({
                        _method: 'DELETE',
                        redirect_to: 'dashboard',
                    }),
                    credentials: 'same-origin',
                });

                const data = await response.json();

                if (! response.ok) {
                    this.setFeedback('error', data.message ?? 'Unable to remove your profile photo.');

                    return;
                }

                this.applyPhotoUpdate(data);
                this.setFeedback('success', data.message);
            } catch {
                this.setFeedback('error', 'Unable to remove your profile photo. Please try again.');
            } finally {
                this.removing = false;
            }
        },

        applyPhotoUpdate(data) {
            this.revokeBlobUrl();
            this.currentUrl = data.photo_url;
            this.previewUrl = data.photo_url;
            this.hasStoredPhoto = Boolean(data.photo_url);
            this.imageFailed = false;

            if (this.$refs.photoInput) {
                this.$refs.photoInput.value = '';
            }

            if (data.profile_completion) {
                window.dispatchEvent(new CustomEvent('profile-completion-updated', {
                    detail: {
                        profile_completion: data.profile_completion,
                    },
                }));
            }
        },
    };
}
