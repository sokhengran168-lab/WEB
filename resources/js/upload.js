/**
 * upload.js — Image upload / drag-drop / modal preview
 *
 * Shared between auction (create.blade.php) and listing (sell.blade.php).
 * Call initImageUpload() after DOMContentLoaded in app.js.
 */
export function initImageUpload() {
    const dropZone         = document.getElementById('dropZone');
    const fileInput        = document.getElementById('imageInput');
    const previewContainer = document.getElementById('imagePreview');
    const placeholder      = document.getElementById('uploadPlaceholder');
    const modal            = document.getElementById('imageModal');
    const modalImage       = document.getElementById('modalImage');
    const closeModalBtn    = document.getElementById('closeModal');

    if (!dropZone || !fileInput || !previewContainer || !placeholder) return;

    // ── Modal ──────────────────────────────────────────────────────────────
    function openModal(src) {
        if (!modal || !modalImage) return;
        modalImage.src = src;
        modal.classList.remove('hidden');
        modal.classList.add('flex');
        setTimeout(() => {
            modalImage.classList.remove('scale-95');
            modalImage.classList.add('scale-100');
        }, 50);
    }

    function closeModal() {
        if (!modal || !modalImage) return;
        modal.classList.add('hidden');
        modal.classList.remove('flex');
        modalImage.classList.remove('scale-100');
        modalImage.classList.add('scale-95');
    }

    closeModalBtn?.addEventListener('click', closeModal);
    modal?.addEventListener('click', (e) => { if (e.target === modal) closeModal(); });
    document.addEventListener('keydown', (e) => { if (e.key === 'Escape') closeModal(); });

    // ── Drop zone ──────────────────────────────────────────────────────────
    dropZone.addEventListener('click', (e) => {
        if (!e.target.closest('#imagePreview')) fileInput.click();
    });

    dropZone.addEventListener('dragover', (e) => {
        e.preventDefault();
        dropZone.classList.add('border-indigo-500');
    });

    dropZone.addEventListener('dragleave', () => {
        dropZone.classList.remove('border-indigo-500');
    });

    dropZone.addEventListener('drop', (e) => {
        e.preventDefault();
        dropZone.classList.remove('border-indigo-500');

        // Sync dropped files into the real input so the form submits them
        const dt = new DataTransfer();
        Array.from(e.dataTransfer.files).forEach(f => dt.items.add(f));
        fileInput.files = dt.files;

        handleFiles(fileInput.files);
    });

    fileInput.addEventListener('change', function () {
        handleFiles(this.files);
    });

    // ── Validation ─────────────────────────────────────────────────────────
    function handleFiles(files) {
        if (!files || files.length === 0) {
            resetPreview();
            return;
        }

        if (files.length > 8) {
            alert('Maximum 8 images allowed.');
            fileInput.value = '';
            resetPreview();
            return;
        }

        for (const file of files) {
            if (file.size > 5 * 1024 * 1024) {
                alert('Each image must be under 5 MB.');
                fileInput.value = '';
                resetPreview();
                return;
            }
        }

        renderPreview(files);
    }

    // ── Preview — first image hero + count badge ───────────────────────────
    function renderPreview(files) {
        const file = files[0];
        if (!file.type.startsWith('image/')) return;

        const reader = new FileReader();
        reader.onload = (e) => {
            previewContainer.className = 'absolute inset-0 bg-gray-900';
            previewContainer.innerHTML = `
                <div class="relative w-full h-full">
                    <img src="${e.target.result}"
                         class="w-full h-full object-cover cursor-zoom-in"
                         alt="Preview">

                    <div class="absolute top-3 left-3 bg-black/70 text-white text-sm px-3 py-1 rounded">
                        1 / ${files.length}
                    </div>

                    <button onclick="window._clearImages()"
                            class="absolute top-3 right-3 bg-red-500 hover:bg-red-600 text-white
                                   w-8 h-8 rounded-full flex items-center justify-center transition">
                        ✕
                    </button>
                </div>
            `;

            previewContainer.querySelector('img')
                .addEventListener('click', () => openModal(e.target.result));

            placeholder.classList.add('hidden');
            previewContainer.classList.remove('hidden');
        };
        reader.readAsDataURL(file);
    }

    // ── Reset ──────────────────────────────────────────────────────────────
    function resetPreview() {
        previewContainer.innerHTML = '';
        previewContainer.className = 'absolute inset-0 hidden bg-gray-900';
        placeholder.classList.remove('hidden');
    }

    // Exposed globally so the inline onclick button can reach it
    window._clearImages = function () {
        fileInput.value = '';
        resetPreview();
    };
}

// ── Telegram helper (used by sell.blade.php only) ──────────────────────────
export function initTelegramInput() {
    function parseTelegram(value) {
        const match = value.trim()
            .match(/(?:https?:\/\/)?(?:t\.me|telegram\.me)\/([a-zA-Z0-9_]+)/i);
        return match ? match[1] : value.trim().replace(/^@/, '');
    }

    const input = document.getElementById('telegramInput');
    if (!input) return;

    input.addEventListener('blur',  function () { this.value = parseTelegram(this.value); });
    input.addEventListener('paste', function () {
        setTimeout(() => this.value = parseTelegram(this.value), 10);
    });
}
