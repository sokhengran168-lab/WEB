import './bootstrap';

import Alpine from 'alpinejs';

import { registerGameForm } from './game-form';
import { initImageUpload, initTelegramInput } from './upload';
import { initListingShow } from './show';
import { initListingIndex } from '.';
import { initListingEdit } from './edit';

// Pass Alpine instance in so game-form.js doesn't need a global
registerGameForm(Alpine);

window.Alpine = Alpine;
Alpine.start();

document.addEventListener('DOMContentLoaded', () => {
    initImageUpload();
    initTelegramInput();
    initListingShow(); // safe to call on every page — guards check for element existence
    initListingIndex();  // index page — guards check for #listingsArea
    initListingEdit(); // edit page  — guards check for #existingImages

    if (document.getElementById('existingImages')) {
            initListingEdit();
        }

    const auctionEndsAt = document.getElementById('auctionEndsAt');
    if (auctionEndsAt) {
        const now = new Date();
        now.setHours(now.getHours() + 1);
        auctionEndsAt.min = now.toISOString().slice(0, 16);
    }
});
