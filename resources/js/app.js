import './bootstrap';

import Alpine from 'alpinejs';

import { registerGameForm } from './game-form';
import { initImageUpload, initTelegramInput } from './upload';

// Pass Alpine instance in so game-form.js doesn't need a global
registerGameForm(Alpine);

window.Alpine = Alpine;
Alpine.start();

document.addEventListener('DOMContentLoaded', () => {
    initImageUpload();
    initTelegramInput();

    const auctionEndsAt = document.getElementById('auctionEndsAt');
    if (auctionEndsAt) {
        const now = new Date();
        now.setHours(now.getHours() + 1);
        auctionEndsAt.min = now.toISOString().slice(0, 16);
    }
});
