import axios from 'axios';
import Echo from 'laravel-echo';
import Pusher from 'pusher-js';

window.axios = axios;
window.axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';

window.Pusher = Pusher;

const pusherKey = import.meta.env.VITE_PUSHER_APP_KEY;

if (pusherKey && pusherKey !== 'YOUR_PUSHER_APP_KEY') {

    window.Echo = new Echo({
        broadcaster: "pusher",
        key: pusherKey,
        cluster: import.meta.env.VITE_PUSHER_APP_CLUSTER,
        forceTLS: true,
    });

    window.Echo.connector.pusher.connection.bind('connected', () => {
        console.log('✅ Pusher connected');
    });

} else {
    console.log('⚠️ Pusher not configured — real-time disabled');
}