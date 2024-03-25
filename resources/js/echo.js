// echo.js
import Echo from 'laravel-echo';
import io from 'pusher-js';
window.io = io;

window.Echo = new Echo({
    broadcaster: 'app',
    key: 'anyKeyYouLike',
    cluster: 'your-cluster',
    forceTLS: true
});

export default window.Echo;
