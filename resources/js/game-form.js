/**
 * resources/js/listings/game-form.js
 */
export function registerGameForm(Alpine) {
    Alpine.data('gameForm', () => ({
        games: [],

        selectedGame:   '',
        selectedRank:   '',
        selectedServer: '',

        ranks:   [],
        servers: [],

        // Stored so we can restore after updateOptions populates the arrays
        _pendingRank:   '',
        _pendingServer: '',

        init() {
            const scriptTag = document.getElementById('games-data');
            if (!scriptTag) return;

            try {
                this.games = JSON.parse(scriptTag.textContent);
            } catch (e) {
                console.error('game-form: invalid JSON in #games-data', e);
                return;
            }

            // Quick sanity check — log what came through so you can verify in DevTools
            console.debug('game-form: loaded games', this.games);

            const el = this.$el;

            const oldGame   = el.dataset.oldGame   || '';
            const oldRank   = el.dataset.oldRank   || '';
            const oldServer = el.dataset.oldServer || '';

            this.selectedGame = oldGame;
            this.updateOptions(false);

            this.selectedRank   = this.ranks.includes(oldRank)   ? oldRank   : (this.ranks[0]   ?? '');
            this.selectedServer = this.servers.includes(oldServer) ? oldServer : (this.servers[0] ?? '');
        },

        updateOptions(reset = false) {
            const game = this.games.find(g => String(g.id) === String(this.selectedGame));

            // Guard: ranks/servers must be arrays — bad JSON decode gives null/string
            this.ranks   = Array.isArray(game?.ranks)   ? game.ranks   : [];
            this.servers = Array.isArray(game?.servers) ? game.servers : [];

            if (reset) {
                this.selectedRank   = this.ranks[0]   ?? '';
                this.selectedServer = this.servers[0] ?? '';
            }
        },

        updateOptions(reset = false) {
            const game = this.games.find(g => String(g.id) === String(this.selectedGame));

            this.ranks   = game?.ranks   ?? [];
            this.servers = game?.servers ?? [];

            if (reset) {
                this.selectedRank   = this.ranks[0]   ?? '';
                this.selectedServer = this.servers[0] ?? '';
            }
        },

        get rankOptions() {
            if (!this.ranks.length) {
                return '<option value="" selected hidden>No ranks available</option>';
            }
            let html = `<option value="" ${!this.selectedRank ? 'selected' : ''} hidden>Select rank</option>`;
            this.ranks.forEach(rank => {
                const sel = String(rank) === String(this.selectedRank) ? 'selected' : '';
                html += `<option value="${rank}" ${sel}>${rank}</option>`;
            });
            return html;
        },

        get serverOptions() {
            if (!this.servers.length) {
                return '<option value="" selected hidden>No servers available</option>';
            }
            let html = `<option value="" ${!this.selectedServer ? 'selected' : ''} hidden>Select server</option>`;
            this.servers.forEach(server => {
                const sel = String(server) === String(this.selectedServer) ? 'selected' : '';
                html += `<option value="${server}" ${sel}>${server}</option>`;
            });
            return html;
        },
    }));
}
