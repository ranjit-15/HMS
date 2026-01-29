/**
 * Profile functionality
 * - Avatar preview and upload
 * - Profile modal (to be implemented)
 * - Client-side validation
 */

const MAX_FILE_SIZE = 2 * 1024 * 1024; // 2MB
const ALLOWED_TYPES = ['image/jpeg', 'image/png', 'image/jpg'];

/**
 * Initialize profile functionality
 */
export function initProfile() {
    const avatarInput = document.getElementById('avatar-input');
    const avatarPreview = document.getElementById('avatar-preview');
    const avatarDropzone = document.getElementById('avatar-dropzone');
    const avatarError = document.getElementById('avatar-error');

    if (avatarInput) {
        avatarInput.addEventListener('change', handleAvatarSelect);
    }

    if (avatarDropzone) {
        initDragDrop(avatarDropzone, avatarInput);
    }
}

/**
 * Handle avatar file selection
 * @param {Event} e
 */
function handleAvatarSelect(e) {
    const file = e.target.files[0];
    if (!file) return;

    const error = validateFile(file);
    if (error) {
        showAvatarError(error);
        e.target.value = '';
        return;
    }

    showAvatarPreview(file);
    clearAvatarError();
}

/**
 * Validate avatar file
 * @param {File} file
 * @returns {string|null} Error message or null if valid
 */
function validateFile(file) {
    if (!ALLOWED_TYPES.includes(file.type)) {
        return 'Please upload a JPG or PNG image.';
    }
    if (file.size > MAX_FILE_SIZE) {
        return 'File size must be less than 2MB.';
    }
    return null;
}

/**
 * Show avatar preview
 * @param {File} file
 */
function showAvatarPreview(file) {
    const preview = document.getElementById('avatar-preview');
    if (!preview) return;

    const reader = new FileReader();
    reader.onload = (e) => {
        preview.src = e.target.result;
        preview.classList.remove('hidden');
    };
    reader.readAsDataURL(file);
}

/**
 * Show avatar error message
 * @param {string} message
 */
function showAvatarError(message) {
    const errorEl = document.getElementById('avatar-error');
    if (errorEl) {
        errorEl.textContent = message;
        errorEl.classList.remove('hidden');
    }
}

/**
 * Clear avatar error message
 */
function clearAvatarError() {
    const errorEl = document.getElementById('avatar-error');
    if (errorEl) {
        errorEl.textContent = '';
        errorEl.classList.add('hidden');
    }
}

/**
 * Initialize drag and drop for avatar upload
 * @param {HTMLElement} dropzone
 * @param {HTMLInputElement} input
 */
function initDragDrop(dropzone, input) {
    ['dragenter', 'dragover', 'dragleave', 'drop'].forEach(eventName => {
        dropzone.addEventListener(eventName, preventDefaults, false);
    });

    function preventDefaults(e) {
        e.preventDefault();
        e.stopPropagation();
    }

    ['dragenter', 'dragover'].forEach(eventName => {
        dropzone.addEventListener(eventName, () => {
            dropzone.classList.add('border-indigo-500', 'bg-indigo-50');
        });
    });

    ['dragleave', 'drop'].forEach(eventName => {
        dropzone.addEventListener(eventName, () => {
            dropzone.classList.remove('border-indigo-500', 'bg-indigo-50');
        });
    });

    dropzone.addEventListener('drop', (e) => {
        const files = e.dataTransfer.files;
        if (files.length > 0) {
            input.files = files;
            input.dispatchEvent(new Event('change'));
        }
    });
}

/**
 * Upload avatar via AJAX
 * @param {File} file
 * @param {string} url
 * @returns {Promise}
 */
export async function uploadAvatar(file, url) {
    const formData = new FormData();
    formData.append('avatar', file);
    formData.append('_token', document.querySelector('meta[name="csrf-token"]').content);

    const response = await fetch(url, {
        method: 'POST',
        body: formData,
        headers: {
            'X-Requested-With': 'XMLHttpRequest',
        },
    });

    if (!response.ok) {
        const data = await response.json();
        throw new Error(data.message || 'Upload failed');
    }

    return response.json();
}
