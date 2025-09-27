import axios from 'axios';
window.axios = axios;

window.axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';

import Echo from 'laravel-echo';
import Pusher from 'pusher-js';

window.Pusher = Pusher;
Pusher.logToConsole = true;

try {
    window.Echo = new Echo({
        broadcaster: 'pusher',
        key: import.meta.env.VITE_PUSHER_APP_KEY,
        cluster: import.meta.env.VITE_PUSHER_APP_CLUSTER,
        forceTLS: true,
        enabledTransports: ['ws', 'wss'],
        auth: {
            headers: {
                'Authorization': 'Bearer ' + localStorage.getItem('wallet_token'), // or your token storage method
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '',
            },
        },
    });
    console.log('Echo initialized successfully');
} catch (error) {
    console.error('Echo initialization failed:', error);
}