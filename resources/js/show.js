/**
 * resources/js/listings/show.js
 */

export function initListingShow() {
    initThumbnails();
    initCopyButtons();
    initCopyLink();
    initDeleteConfirm();
}

function initThumbnails() {
    const mainImg = document.getElementById('mainImg');
    if (!mainImg) return;

    document.querySelectorAll('.listing-thumb').forEach(thumb => {
        thumb.addEventListener('click', () => {
            mainImg.src = thumb.dataset.src;
            document.querySelectorAll('.listing-thumb')
                .forEach(t => t.classList.remove('border-indigo-500'));
            thumb.classList.add('border-indigo-500');
        });
    });
}

function initCopyButtons() {
    document.querySelectorAll('.copy-btn').forEach(btn => {
        btn.addEventListener('click', async () => {
            const text = btn.dataset.copy;
            if (!text) return;
            try {
                await navigator.clipboard.writeText(text);
                const original = btn.textContent;
                btn.textContent = '✅ Copied';
                setTimeout(() => { btn.textContent = original; }, 2000);
            } catch {
                btn.textContent = 'Failed';
            }
        });
    });
}

function initCopyLink() {
    const btn = document.getElementById('copyLinkBtn');
    if (!btn) return;

    btn.addEventListener('click', async () => {
        try {
            await navigator.clipboard.writeText(btn.dataset.url);
            btn.textContent = '✅ Copied!';
            btn.classList.replace('text-indigo-400', 'text-green-400');
            setTimeout(() => {
                btn.textContent = 'Copy Link';
                btn.classList.replace('text-green-400', 'text-indigo-400');
            }, 2500);
        } catch {
            btn.textContent = 'Failed';
        }
    });
}

function initDeleteConfirm() {
    const btn  = document.getElementById('deleteBtn');
    const form = document.getElementById('deleteForm');
    if (!btn || !form) return;

    btn.addEventListener('click', () => {
        if (confirm('Delete this listing? This cannot be undone.')) {
            form.submit();
        }
    });
}
