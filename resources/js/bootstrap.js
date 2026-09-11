import Echo from 'laravel-echo';
import Pusher from 'pusher-js';

/*
|--------------------------------------------------------------------------
| Bootstrap JavaScript
|--------------------------------------------------------------------------
|
| This enables Bootstrap components such as:
| - Dropdown
| - Navbar collapse
| - Modal
| - Tooltip
| - Popover
|
*/
import 'bootstrap/dist/js/bootstrap.bundle.min.js';

/*
|--------------------------------------------------------------------------
| Pusher
|--------------------------------------------------------------------------
*/

window.Pusher = Pusher;

/*
|--------------------------------------------------------------------------
| Laravel Echo + Reverb
|--------------------------------------------------------------------------
*/

window.Echo = new Echo({
    broadcaster: 'reverb',

    key: import.meta.env.VITE_REVERB_APP_KEY,

    wsHost: import.meta.env.VITE_REVERB_HOST,

    wsPort: import.meta.env.VITE_REVERB_PORT,

    wssPort: import.meta.env.VITE_REVERB_PORT,

    forceTLS: false,

    enabledTransports: [
        'ws',
        'wss'
    ],
});