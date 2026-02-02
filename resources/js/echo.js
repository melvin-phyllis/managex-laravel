import Echo from 'laravel-echo';
import Pusher from 'pusher-js';

// Only initialize Echo if VITE_REVERB_APP_KEY is configured and not empty
const reverbKey = import.meta.env.VITE_REVERB_APP_KEY;
const reverbHost = import.meta.env.VITE_REVERB_HOST;
const reverbPort = import.meta.env.VITE_REVERB_PORT;

// Check if Reverb is properly configured
const isReverbConfigured = reverbKey && 
    reverbKey !== 'undefined' && 
    reverbKey !== '' && 
    reverbKey !== 'null' &&
    reverbHost && 
    reverbHost !== '' &&
    reverbPort && 
    reverbPort !== '';

if (isReverbConfigured) {
    window.Pusher = Pusher;

    // Disable Pusher logging in production
    if (import.meta.env.PROD) {
        Pusher.logToConsole = false;
    }

    try {
        window.Echo = new Echo({
            broadcaster: 'reverb',
            key: reverbKey,
            wsHost: reverbHost,
            wsPort: parseInt(reverbPort) || 8080,
            wssPort: parseInt(reverbPort) || 443,
            forceTLS: (import.meta.env.VITE_REVERB_SCHEME ?? 'https') === 'https',
            enabledTransports: ['ws', 'wss'],
            // Reduce reconnection attempts
            activityTimeout: 30000,
            pongTimeout: 10000,
        });

        // Log successful initialization in dev
        if (import.meta.env.DEV) {
            console.log('[Echo] WebSocket broadcasting enabled');
        }
    } catch (error) {
        console.warn('[Echo] Failed to initialize:', error.message);
        window.Echo = null;
    }
} else {
    // Echo disabled - WebSocket server not configured
    // This is normal for shared hosting deployments
    window.Echo = null;
    
    // Only log in development mode
    if (import.meta.env.DEV) {
        console.log('[Echo] WebSocket broadcasting disabled (VITE_REVERB_APP_KEY not configured)');
        console.log('[Echo] Real-time features will use AJAX polling as fallback');
    }
}
