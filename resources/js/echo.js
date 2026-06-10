import Echo from 'laravel-echo';

import Pusher from 'pusher-js';

window.Pusher = Pusher;

export function getEcho() {
    const useTls = import.meta.env.VITE_REVERB_SCHEME === 'https';

    window.Echo = new Echo({
        broadcaster: 'reverb',
        key: import.meta.env.VITE_REVERB_APP_KEY,
        wsHost: import.meta.env.VITE_REVERB_HOST,
        wsPort: import.meta.env.VITE_REVERB_PORT ?? 80,
        wssPort: import.meta.env.VITE_REVERB_PORT ?? 443,
        forceTLS: useTls,
        enabledTransports: [useTls ? 'wss' : 'ws'],
    });

    return window.Echo;
}

