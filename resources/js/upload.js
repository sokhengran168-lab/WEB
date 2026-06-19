/**
 * resources/js/listings/upload.js
 * Image upload / drag-drop / grid preview / modal
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

    // ── Accumulated files across multiple picks/drops ──────────────────────
    let allFiles = new DataTransfer();

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
    // ── Drop onto the preview area too ────────────────────────────────────────
    previewContainer.addEventListener('dragover', (e) => {
        e.preventDefault();
        dropZone.classList.add('border-indigo-500');
    });

    previewContainer.addEventListener('dragleave', (e) => {
        // Only remove highlight if leaving the dropZone entirely
        if (!dropZone.contains(e.relatedTarget)) {
            dropZone.classList.remove('border-indigo-500');
        }
    });

    previewContainer.addEventListener('drop', (e) => {
        e.preventDefault();
        e.stopPropagation();
        dropZone.classList.remove('border-indigo-500');
        mergeAndHandle(Array.from(e.dataTransfer.files));
    });

    closeModalBtn?.addEventListener('click', closeModal);
    modal?.addEventListener('click', (e) => { if (e.target === modal) closeModal(); });
    document.addEventListener('keydown', (e) => { if (e.key === 'Escape') closeModal(); });

    // ── Drop zone click — always allow re-picking ──────────────────────────
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
        mergeAndHandle(Array.from(e.dataTransfer.files));
    });

    fileInput.addEventListener('change', function () {
        mergeAndHandle(Array.from(this.files));
        // Reset input so the same file can be re-selected if removed
        // this.value = '';
    });

    // ── Merge new files into allFiles, skip duplicates by name+size ────────
    function mergeAndHandle(newFiles) {
        console.log('FILES:', allFiles.files);
        const existing = Array.from(allFiles.files);

        for (const file of newFiles) {
            const isDupe = existing.some(
                f => f.name === file.name && f.size === file.size
            );
            if (!isDupe) allFiles.items.add(file);
        }

        // Enforce max 8
        if (allFiles.files.length > 8) {
            alert('Maximum 8 images allowed.');
            // Keep only first 8
            const kept = Array.from(allFiles.files).slice(0, 8);
            allFiles = new DataTransfer();
            kept.forEach(f => allFiles.items.add(f));
        }

        // Validate sizes
        for (const file of Array.from(allFiles.files)) {
            if (file.size > 10 * 1024 * 1024) {
                alert(`"${file.name}" exceeds 10 MB and was removed.`);
                const kept = Array.from(allFiles.files).filter(f => f !== file);
                allFiles = new DataTransfer();
                kept.forEach(f => allFiles.items.add(f));
            }
        }

        // Sync to the real input so the form submits all files
        fileInput.files = allFiles.files;

        if (allFiles.files.length === 0) {
            resetPreview();
        } else {
            renderPreview(allFiles.files);
        }
    }

    // ── Preview — full grid ────────────────────────────────────────────────
    function renderPreview(files) {
        const fileArray = Array.from(files);

        const readers = fileArray.map(file => new Promise((resolve) => {
            if (!file.type.startsWith('image/')) { resolve(null); return; }
            const reader = new FileReader();
            reader.onload  = (e) => resolve({ src: e.target.result, name: file.name });
            reader.onerror = () => resolve(null);
            reader.readAsDataURL(file);
        }));

        Promise.all(readers).then(results => {
            const valid = results.filter(Boolean);
            if (!valid.length) return;

            previewContainer.innerHTML = `
                <div class="p-4">
                    <div class="flex items-center justify-between mb-3">
                        <span class="text-sm text-gray-400 font-medium">
                            ${valid.length} image${valid.length > 1 ? 's' : ''} selected
                        </span>
                        <div class="flex items-center gap-3">
                            ${valid.length < 8 ? `
                                <button type="button" id="addMoreBtn"
                                        class="text-xs text-indigo-400 hover:text-indigo-300 transition flex items-center gap-1">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5" fill="none"
                                        viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M12 4v16m8-8H4"/>
                                    </svg>
                                    Add more
                                </button>
                            ` : ''}
                            <button type="button" id="clearImagesBtn"
                                    class="text-xs text-red-400 hover:text-red-300 transition flex items-center gap-1">
                                <svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5" fill="none"
                                    viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M6 18L18 6M6 6l12 12"/>
                                </svg>
                                Clear all
                            </button>
                        </div>
                    </div>

                    <div class="grid grid-cols-3 sm:grid-cols-4 gap-3" id="previewGrid">
                        ${valid.map((item, i) => `
                            <div class="relative group" data-preview-index="${i}">
                                <img src="${item.src}"
                                    class="w-full aspect-square object-cover rounded-2xl
                                            border border-gray-700 transition-all duration-150
                                            group-hover:border-indigo-500 group-hover:brightness-90
                                            cursor-zoom-in"
                                    alt="Preview ${i + 1}">
                                ${i === 0 ? `
                                    <span class="absolute bottom-2 left-2 bg-indigo-600 text-white
                                                text-xs px-2 py-0.5 rounded-lg font-medium pointer-events-none">
                                        Cover
                                    </span>
                                ` : ''}
                                <button type="button"
                                        class="remove-img absolute top-2 right-2
                                            opacity-0 group-hover:opacity-100
                                            bg-black/60 hover:bg-red-600 text-white
                                            w-6 h-6 rounded-full flex items-center justify-center
                                            transition-all duration-150 text-xs"
                                        data-remove-index="${i}">
                                    ✕
                                </button>
                            </div>
                        `).join('')}

                        ${valid.length < 8 ? `
                            <div id="addMoreCell"
                                class="aspect-square rounded-2xl border-2 border-dashed border-gray-700
                                        hover:border-indigo-500 flex flex-col items-center justify-center
                                        cursor-pointer transition group">
                                <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6 text-gray-600 group-hover:text-indigo-400 transition" fill="none"
                                    viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M12 4v16m8-8H4"/>
                                </svg>
                                <span class="text-xs text-gray-600 group-hover:text-indigo-400 mt-1 transition">Add</span>
                            </div>
                        ` : ''}
                    </div>

                    <p class="text-xs text-gray-600 mt-3">
                        ${valid.length}/8 images · drag more onto this area to add
                    </p>
                </div>
            `;

            // Zoom on image click
            previewContainer.querySelectorAll('[data-preview-index] img').forEach((img, i) => {
                img.addEventListener('click', (e) => {
                    e.stopPropagation();
                    openModal(valid[i].src);
                });
            });

            // Remove individual image
            previewContainer.querySelectorAll('.remove-img').forEach(btn => {
                btn.addEventListener('click', (e) => {
                    e.stopPropagation();
                    const idx = parseInt(btn.dataset.removeIndex, 10);
                    const kept = Array.from(allFiles.files).filter((_, i) => i !== idx);
                    allFiles = new DataTransfer();
                    kept.forEach(f => allFiles.items.add(f));
                    fileInput.files = allFiles.files;

                    if (allFiles.files.length === 0) {
                        resetPreview();
                    } else {
                        renderPreview(allFiles.files);
                    }
                });
            });

            // + Add more button (top bar)
            document.getElementById('addMoreBtn')?.addEventListener('click', (e) => {
                e.stopPropagation();
                fileInput.click();
            });

            // + Add more cell (grid)
            document.getElementById('addMoreCell')?.addEventListener('click', (e) => {
                e.stopPropagation();
                fileInput.click();
            });

            // Clear all
            document.getElementById('clearImagesBtn')?.addEventListener('click', (e) => {
                e.stopPropagation();
                resetPreview();
            });

            // Show grid
            previewContainer.classList.remove('hidden', 'absolute', 'inset-0');
            previewContainer.classList.add('block', 'relative');
            placeholder.classList.add('hidden');
        });
    }

    // ── Reset ──────────────────────────────────────────────────────────────
    function resetPreview() {
        allFiles = new DataTransfer();          // ← wipe accumulated files
        fileInput.files = allFiles.files;       // ← sync the empty state
        previewContainer.innerHTML = '';
        previewContainer.classList.add('hidden', 'absolute', 'inset-0');
        previewContainer.classList.remove('block', 'relative');
        placeholder.classList.remove('hidden');
    }

    window._clearImages = function () { resetPreview(); };

    const form = dropZone.closest('form');
    if (form) {
        // form.addEventListener('submit', () => {
        //     fileInput.files = allFiles.files;
        // }, true); // true = capture phase, fires before everything else
            form.addEventListener('submit', function () {

            if (allFiles.files.length === 0) return;

            const newInput = fileInput.cloneNode();
            const dataTransfer = new DataTransfer();

            Array.from(allFiles.files).forEach(file => {
                dataTransfer.items.add(file);
            });

            newInput.files = dataTransfer.files;

            fileInput.parentNode.replaceChild(newInput, fileInput);
        });
    }
}

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
