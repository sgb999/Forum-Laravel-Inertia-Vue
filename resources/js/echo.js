import Echo from 'laravel-echo';
import Pusher from 'pusher-js';

window.Pusher = Pusher;

let echoInstance = null;

export function getEcho() {
    if (echoInstance) return echoInstance;

    const useTls = import.meta.env.VITE_REVERB_SCHEME === 'https';

    echoInstance = new Echo({
        broadcaster: 'reverb',
        key: import.meta.env.VITE_REVERB_APP_KEY,
        wsHost: import.meta.env.VITE_REVERB_HOST,
        wsPort: import.meta.env.VITE_REVERB_PORT ?? 80,
        wssPort: import.meta.env.VITE_REVERB_PORT ?? 443,
        forceTLS: useTls,
        enabledTransports: ['ws', 'wss'],
        cluster: 'mt1',
    });

    return echoInstance;
}
