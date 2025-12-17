/**
 * WebSocket Client for Real-time Updates
 * Uses Laravel Echo with Reverb as fallback to polling
 */
(function() {
    'use strict';

    const WebSocketClient = {
        echo: null,
        isConnected: false,
        reconnectAttempts: 0,
        maxReconnectAttempts: 5,
        reconnectDelay: 3000,

        /**
         * Initialize WebSocket connection
         * @param {Object} config Configuration object
         * @param {string} config.appKey Reverb app key
         * @param {string} config.host Reverb host
         * @param {number} config.port Reverb port
         * @param {string} config.scheme Reverb scheme (http/https/ws/wss)
         * @param {string} config.screenToken Screen token for public screens
         */
        init(config) {
            // Check if Laravel Echo and Pusher are available
            if (typeof Echo === 'undefined' || typeof Pusher === 'undefined') {
                console.warn('Laravel Echo or Pusher not available. Falling back to polling.');
                return false;
            }

            try {
                // Initialize Echo with Reverb configuration
                this.echo = new Echo({
                    broadcaster: 'reverb',
                    key: config.appKey,
                    wsHost: config.host,
                    wsPort: config.port,
                    wssPort: config.port,
                    forceTLS: config.scheme === 'https' || config.scheme === 'wss',
                    enabledTransports: ['ws', 'wss'],
                    authEndpoint: '/broadcasting/auth',
                    auth: {
                        headers: config.screenToken ? {
                            'X-Screen-Token': config.screenToken
                        } : {}
                    }
                });

                // Set up connection event handlers
                this.setupEventHandlers(config.onConnectionChange);

                return true;
            } catch (error) {
                console.error('Failed to initialize WebSocket:', error);
                return false;
            }
        },

        /**
         * Set up connection event handlers
         */
        setupEventHandlers(onConnectionChange) {
            if (!this.echo) return;

            // Handle connection success
            this.echo.connector.pusher.connection.bind('connected', () => {
                this.isConnected = true;
                this.reconnectAttempts = 0;
                console.log('WebSocket connected');
                if (onConnectionChange) {
                    onConnectionChange(true);
                }
            });

            // Handle connection failure
            this.echo.connector.pusher.connection.bind('disconnected', () => {
                this.isConnected = false;
                console.warn('WebSocket disconnected');
                if (onConnectionChange) {
                    onConnectionChange(false);
                }
            });

            // Handle connection errors
            this.echo.connector.pusher.connection.bind('error', (error) => {
                console.error('WebSocket error:', error);
                this.isConnected = false;
                if (onConnectionChange) {
                    onConnectionChange(false);
                }
            });
        },

        /**
         * Subscribe to a private channel
         * @param {string} channelName Channel name (e.g., 'tenant.1.queue.2')
         * @param {Function} callback Event callback function
         * @param {string} eventName Event name (e.g., 'queue.updated')
         */
        subscribe(channelName, eventName, callback) {
            if (!this.echo) {
                console.warn('Echo not initialized. Cannot subscribe to channel.');
                return;
            }

            try {
                this.echo.private(channelName)
                    .listen(eventName, callback);
                console.log(`Subscribed to ${channelName} for event ${eventName}`);
            } catch (error) {
                console.error(`Failed to subscribe to ${channelName}:`, error);
            }
        },

        /**
         * Unsubscribe from a channel
         * @param {string} channelName Channel name
         */
        unsubscribe(channelName) {
            if (!this.echo) {
                return;
            }

            try {
                this.echo.leave(channelName);
                console.log(`Unsubscribed from ${channelName}`);
            } catch (error) {
                console.error(`Failed to unsubscribe from ${channelName}:`, error);
            }
        },

        /**
         * Disconnect WebSocket
         */
        disconnect() {
            if (this.echo) {
                this.echo.disconnect();
                this.isConnected = false;
            }
        }
    };

    // Export globally
    window.WebSocketClient = WebSocketClient;
})();

