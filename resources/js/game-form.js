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
    const scriptTag = document.getElementById('games-data');
    if (scriptTag) {
        this.games = JSON.parse(scriptTag.textContent);
    }

    const el = this.$el;

    // STEP 1: get old values
    const oldGame   = el.dataset.oldGame   || '';
    const oldRank   = el.dataset.oldRank   || '';
    const oldServer = el.dataset.oldServer || '';

    // STEP 2: set game first
    this.selectedGame = oldGame;

    // STEP 3: load options
    this.updateOptions();

    // STEP 4: wait for DOM + options, then restore values
    this.$nextTick(() => {
        this.selectedRank   = this.ranks.find(r => r == oldRank) || '';
        this.selectedServer = this.servers.find(s => s == oldServer) || '';
    });
},

updateOptions(reset = false) {
    const game = this.games.find(g => g.id == this.selectedGame);

    this.ranks   = game ? game.ranks   : [];
    this.servers = game ? game.servers : [];

    if (reset) {
        this.selectedRank   = '';
        this.selectedServer = '';
    }
}

        // get rankOptions() {
        //     let html = `<option value="" ${!this.selectedRank ? 'selected' : ''} hidden>Select rank</option>`;
        //     this.ranks.forEach(rank => {
        //         const sel = rank == this.selectedRank ? 'selected' : '';
        //         html += `<option value="${rank}" ${sel}>${rank}</option>`;
        //     });
        //     return html;
        // },

        // get serverOptions() {
        //     let html = `<option value="" ${!this.selectedServer ? 'selected' : ''} hidden>Select server</option>`;
        //     this.servers.forEach(server => {
        //         const sel = server == this.selectedServer ? 'selected' : '';
        //         html += `<option value="${server}" ${sel}>${server}</option>`;
        //     });
        //     return html;
        // },
    }));
}
