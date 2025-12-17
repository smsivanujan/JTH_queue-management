/**
 * Screen Heartbeat System
 * Tracks active second screens in database instead of session
 * 
 * Usage:
 * - Call screenHeartbeat.init() when second screen loads
 * - Call screenHeartbeat.register() when opening second screen from parent
 */

const screenHeartbeat = {
    screenToken: null,
    heartbeatInterval: null,
    heartbeatIntervalMs: 12000, // 12 seconds (between 10-15 as requested)
    signedUrls: {}, // Store signed URLs by screen token
    pairingUrls: {}, // Store pairing URLs by screen token

    /**
     * Initialize heartbeat for second screen window
     * Call this when the second screen page loads
     */
    init(screenToken) {
        if (!screenToken) {
            console.error('Screen token is required for heartbeat');
            return;
        }

        this.screenToken = screenToken;

        // Send heartbeat immediately
        this.sendHeartbeat();

        // Set up interval for periodic heartbeats
        this.heartbeatInterval = setInterval(() => {
            this.sendHeartbeat();
        }, this.heartbeatIntervalMs);

        // Send heartbeat on page visibility change (when tab/window becomes visible)
        document.addEventListener('visibilitychange', () => {
            if (!document.hidden) {
                this.sendHeartbeat();
            }
        });

        // Cleanup on page unload
        window.addEventListener('beforeunload', () => {
            this.cleanup();
        });
    },

    /**
     * Send heartbeat to server
     */
    async sendHeartbeat() {
        if (!this.screenToken) {
            return;
        }

        try {
            // Get CSRF token from meta tag or cookie
            const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') 
                || this.getCookieValue('XSRF-TOKEN');
            
            const response = await fetch('/screens/heartbeat', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken || '',
                },
                body: JSON.stringify({
                    screen_token: this.screenToken
                })
            });

            const data = await response.json();

            if (!data.success && response.status === 404) {
                // Screen token not found, stop heartbeat
                console.warn('Screen token not found, stopping heartbeat');
                this.cleanup();
            }
        } catch (error) {
            console.error('Heartbeat error:', error);
            // Continue trying - network errors are temporary
        }
    },

    /**
     * Get cookie value by name
     */
    getCookieValue(name) {
        const value = `; ${document.cookie}`;
        const parts = value.split(`; ${name}=`);
        if (parts.length === 2) return parts.pop().split(';').shift();
        return null;
    },

    /**
     * Register a new screen (called from parent window when opening second screen)
     */
    async register(screenType, clinicId = null) {
        try {
            // Get CSRF token from meta tag or cookie
            const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content')
                || this.getCookieValue('XSRF-TOKEN');
            
            const response = await fetch('/screens/register', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken || '',
                },
                body: JSON.stringify({
                    screen_type: screenType,
                    clinic_id: clinicId
                })
            });

            const data = await response.json();

            if (data.success && data.screen_token) {
                // Store signed URL if provided
                if (data.signed_url) {
                    this.signedUrls = this.signedUrls || {};
                    this.signedUrls[data.screen_token] = data.signed_url;
                }
                // Store pairing URL if provided
                if (data.pairing_url) {
                    this.pairingUrls = this.pairingUrls || {};
                    this.pairingUrls[data.screen_token] = data.pairing_url;
                }
                return data.screen_token;
            } else {
                console.error('Screen registration failed:', data.message);
                return null;
            }
        } catch (error) {
            console.error('Screen registration error:', error);
            return null;
        }
    },

    /**
     * Get signed URL for a screen token (after registration)
     */
    getSignedUrl(screenType, screenToken) {
        // Return stored signed URL if available
        if (this.signedUrls && this.signedUrls[screenToken]) {
            return this.signedUrls[screenToken];
        }
        return null;
    },

    /**
     * Get pairing URL for QR code (after registration)
     */
    getPairingUrl(screenType, screenToken) {
        // Return stored pairing URL if available
        if (this.pairingUrls && this.pairingUrls[screenToken]) {
            return this.pairingUrls[screenToken];
        }
        return null;
    },

    /**
     * Cleanup heartbeat interval
     */
    cleanup() {
        if (this.heartbeatInterval) {
            clearInterval(this.heartbeatInterval);
            this.heartbeatInterval = null;
        }
    }
};

