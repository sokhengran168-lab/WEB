/**
 * game-form.js
 * Registers the Alpine `gameForm` component.
 * Call registerGameForm() in app.js BEFORE Alpine.start()
 */
export function registerGameForm(Alpine) {
    Alpine.data('gameForm', () => ({
        games: [],

        selectedGame:   '',
        selectedRank:   '',
        selectedServer: '',

        ranks:   [],
        servers: [],

        init() {
            // Read games from the JSON script tag — 100% safe, no encoding issues
            const scriptTag = document.getElementById('games-data');
            if (scriptTag) {
                this.games = JSON.parse(scriptTag.textContent);
            }

            // old() values still come from data attributes (simple strings, always safe)
            const el = this.$el.closest('[data-old-game]');
            if (el) {
                this.selectedGame   = el.dataset.oldGame   || '';
                this.selectedRank   = el.dataset.oldRank   || '';
                this.selectedServer = el.dataset.oldServer || '';
            }

            this.updateOptions();
        },

        updateOptions(reset = false) {
            const game = this.games.find(g => g.id == this.selectedGame);

            this.ranks   = game ? game.ranks   : [];
            this.servers = game ? game.servers : [];

            if (reset) {
                this.selectedRank   = '';
                this.selectedServer = this.servers.length > 0 ? this.servers[0] : '';
            }
        },

        get rankOptions() {
            let html = `<option value="" ${!this.selectedRank ? 'selected' : ''} hidden>Select rank</option>`;
            this.ranks.forEach(rank => {
                const sel = rank == this.selectedRank ? 'selected' : '';
                html += `<option value="${rank}" ${sel}>${rank}</option>`;
            });
            return html;
        },

        get serverOptions() {
            let html = `<option value="" ${!this.selectedServer ? 'selected' : ''} hidden>Select server</option>`;
            this.servers.forEach(server => {
                const sel = server == this.selectedServer ? 'selected' : '';
                html += `<option value="${server}" ${sel}>${server}</option>`;
            });
            return html;
        },
    }));
}
