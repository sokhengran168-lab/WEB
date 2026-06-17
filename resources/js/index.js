/**
 * resources/js/listings/index.js
 * AJAX category switching, pagination, and filter state for the home/index page.
 */

export function initListingIndex() {
    const listingsArea = document.getElementById('listingsArea');
    if (!listingsArea) return; // not on the index page

    function setLoading(on) {
        listingsArea.style.opacity    = on ? '0.4' : '1';
        listingsArea.style.pointerEvents = on ? 'none' : '';
        listingsArea.style.transition = 'opacity 0.15s ease';
    }

    function updateActiveBtn(url) {
        const activeGameId = new URL(url).searchParams.get('game_id');

        document.querySelectorAll('.filter-btn').forEach(btn => {
            const btnGameId = new URL(btn.href).searchParams.get('game_id');
            const isActive  = activeGameId ? btnGameId === activeGameId : !btnGameId;

            // Strip old state classes then apply correct ones
            btn.classList.remove(
                'bg-indigo-600', 'text-white',
                'bg-gray-900', 'text-gray-400',
                'hover:bg-gray-800', 'hover:text-white',
                'border', 'border-gray-800'
            );

            if (isActive) {
                btn.classList.add('bg-indigo-600', 'text-white');
            } else {
                btn.classList.add(
                    'bg-gray-900', 'text-gray-400',
                    'hover:bg-gray-800', 'hover:text-white',
                    'border', 'border-gray-800'
                );
            }
        });
    }

    async function loadListings(url) {
        setLoading(true);
        try {
            const res  = await fetch(url, { headers: { 'X-Requested-With': 'XMLHttpRequest' } });
            const html = await res.text();

            const parser = new DOMParser();
            const doc    = parser.parseFromString(html, 'text/html');

            const freshArea    = doc.getElementById('listingsArea');
            const freshHeading = doc.getElementById('browseHeading');
            const heading      = document.getElementById('browseHeading');

            if (freshArea)    listingsArea.innerHTML = freshArea.innerHTML;
            if (freshHeading && heading) heading.innerHTML = freshHeading.innerHTML;

            history.pushState({}, '', url);
            updateActiveBtn(url);
        } catch {
            window.location.href = url; // graceful fallback
        } finally {
            setLoading(false);
        }
    }

    // Category filter buttons
    document.querySelectorAll('[data-filter]').forEach(btn => {
        btn.addEventListener('click', e => {
            e.preventDefault();
            loadListings(btn.href);
        });
    });

    // Pagination links (delegated — pagination HTML is re-rendered on each AJAX swap)
    document.addEventListener('click', e => {
        const pagerLink = e.target.closest('#listingsArea a[href*="page="]');
        if (pagerLink) {
            e.preventDefault();
            loadListings(pagerLink.href);
            window.scrollTo({ top: 0, behavior: 'smooth' });
        }
    });

    // Browser back / forward
    window.addEventListener('popstate', () => {
        loadListings(window.location.href);
    });
}
