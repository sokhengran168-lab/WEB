/**
 * resources/js/listings/edit.js
 * Handles the "mark existing image for deletion" toggle on the edit listing form.
 */

export function initListingEdit() {
    const container = document.getElementById('existingImages');
    if (!container) return;

    container.querySelectorAll('.existing-image-item').forEach(item => {
        const checkbox = item.querySelector('.delete-checkbox');
        const btn      = item.querySelector('.delete-toggle');
        const img      = item.querySelector('img');

        btn.addEventListener('click', () => {
            const isMarked = !checkbox.checked;
            checkbox.checked = isMarked;

            if (isMarked) {
                // Visually mark as "will be deleted"
                img.classList.add('opacity-30', 'scale-95', 'border-red-500');
                img.classList.remove('border-gray-700');
                btn.textContent = '✕ Undo';
                btn.classList.add('opacity-100', 'bg-red-600/90');   // always visible when marked
                btn.classList.remove('opacity-0', 'hover:opacity-100');
            } else {
                // Restore
                img.classList.remove('opacity-30', 'scale-95', 'border-red-500');
                img.classList.add('border-gray-700');
                btn.textContent = 'Remove';
                btn.classList.remove('opacity-100', 'bg-red-600/90');
                btn.classList.add('opacity-0', 'hover:opacity-100');
            }
        });
    });
}
