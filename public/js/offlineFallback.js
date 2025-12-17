/**
 * Offline Fallback Handler for Public Second Screens
 * Provides graceful offline mode with cached data display
 */

(function() {
    'use strict';

    const OfflineFallback = {
        cacheKey: 'screen_cache',
        cacheTimestampKey: 'screen_cache_timestamp',
        isOffline: false,
        retryInterval: null,
        retryIntervalMs: 15000, // Retry every 15 seconds when offline (graceful, not aggressive)
        fetchTimeoutMs: 8000, // 8 second timeout for fetch requests

        /**
         * Initialize offline fallback
         * @param {string} cacheKeyPrefix - Unique prefix for this screen type
         */
        init(cacheKeyPrefix = 'default') {
            this.cacheKey = `${cacheKeyPrefix}_cache`;
            this.cacheTimestampKey = `${cacheKeyPrefix}_cache_timestamp`;

            // Listen for browser offline/online events
            window.addEventListener('online', () => this.handleOnline());
            window.addEventListener('offline', () => this.handleOffline());

            // Check if already offline on load
            if (!navigator.onLine) {
                this.handleOffline();
            }

            // Load cached data if available
            this.loadCachedData();
        },

        /**
         * Cache successful data response
         * @param {Object} data - Data to cache
         */
        cacheData(data) {
            try {
                localStorage.setItem(this.cacheKey, JSON.stringify(data));
                localStorage.setItem(this.cacheTimestampKey, new Date().toISOString());
            } catch (e) {
                console.warn('Failed to cache data:', e);
            }
        },

        /**
         * Get cached data
         * @returns {Object|null}
         */
        getCachedData() {
            try {
                const cached = localStorage.getItem(this.cacheKey);
                return cached ? JSON.parse(cached) : null;
            } catch (e) {
                console.warn('Failed to read cached data:', e);
                return null;
            }
        },

        /**
         * Get cache timestamp
         * @returns {Date|null}
         */
        getCacheTimestamp() {
            try {
                const timestamp = localStorage.getItem(this.cacheTimestampKey);
                return timestamp ? new Date(timestamp) : null;
            } catch (e) {
                return null;
            }
        },

        /**
         * Load cached data into UI
         */
        loadCachedData() {
            const cached = this.getCachedData();
            if (cached && typeof window.applyCachedData === 'function') {
                window.applyCachedData(cached);
            }
        },

        /**
         * Handle offline state
         */
        handleOffline() {
            if (this.isOffline) return;

            this.isOffline = true;
            this.showOfflineBanner();
            this.loadCachedData();
            
            // Notify that offline state changed (for polling adjustments)
            if (typeof window.onOfflineStateChange === 'function') {
                window.onOfflineStateChange(true);
            }
        },

        /**
         * Handle online state
         */
        handleOnline() {
            if (!this.isOffline) return;

            this.isOffline = false;
            this.hideOfflineBanner();
            this.stopGracefulRetry();
            
            // Notify that online state changed (for polling resumption)
            if (typeof window.onOfflineStateChange === 'function') {
                window.onOfflineStateChange(false);
            }

            // Trigger immediate retry
            if (typeof window.retryFetch === 'function') {
                window.retryFetch();
            }
        },

        /**
         * Show offline banner
         */
        showOfflineBanner() {
            // Remove existing banner if any
            const existing = document.getElementById('offline-banner');
            if (existing) {
                existing.remove();
            }

            const timestamp = this.getCacheTimestamp();
            const timeText = timestamp 
                ? this.formatTimestamp(timestamp)
                : 'Unknown';

            const banner = document.createElement('div');
            banner.id = 'offline-banner';
            banner.className = 'offline-banner';
            banner.innerHTML = `
                <div class="offline-banner-content">
                    <div class="offline-banner-icon">
                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 5.636a9 9 0 010 12.728m0 0l-2.829-2.829m2.829 2.829L21 21M15.536 8.464a5 5 0 010 7.072m0 0l-2.829-2.829m-4.243 2.829a4.978 4.978 0 01-1.414-2.83m-1.414 5.658a9 9 0 01-2.167-9.238m7.824 2.167a1 1 0 111.414 1.414m-1.414-1.414L3 3m8.293 8.293l1.414 1.414"/>
                        </svg>
                    </div>
                    <div class="offline-banner-text">
                        <div class="offline-banner-title">Displaying Cached Information</div>
                        <div class="offline-banner-subtitle">Last updated: ${timeText}</div>
                    </div>
                </div>
            `;

            document.body.insertBefore(banner, document.body.firstChild);
        },

        /**
         * Hide offline banner
         */
        hideOfflineBanner() {
            const banner = document.getElementById('offline-banner');
            if (banner) {
                banner.classList.add('offline-banner-hiding');
                setTimeout(() => banner.remove(), 300);
            }
        },

        /**
         * Format timestamp for display
         * @param {Date} date
         * @returns {string}
         */
        formatTimestamp(date) {
            const now = new Date();
            const diffMs = now - date;
            const diffMins = Math.floor(diffMs / 60000);

            if (diffMins < 1) {
                return 'Just now';
            } else if (diffMins < 60) {
                return `${diffMins} minute${diffMins > 1 ? 's' : ''} ago`;
            } else {
                const diffHours = Math.floor(diffMins / 60);
                if (diffHours < 24) {
                    return `${diffHours} hour${diffHours > 1 ? 's' : ''} ago`;
                } else {
                    return date.toLocaleString();
                }
            }
        },

        /**
         * Start graceful retry interval (slower when offline)
         */
        startGracefulRetry() {
            this.stopGracefulRetry();
            this.retryInterval = setInterval(() => {
                if (navigator.onLine && typeof window.retryFetch === 'function') {
                    window.retryFetch();
                }
            }, this.retryIntervalMs);
        },

        /**
         * Stop graceful retry interval
         */
        stopGracefulRetry() {
            if (this.retryInterval) {
                clearInterval(this.retryInterval);
                this.retryInterval = null;
            }
        },

        /**
         * Wrapper for fetch with timeout and offline detection
         * @param {string} url
         * @param {Object} options
         * @returns {Promise<Response>}
         */
        async fetchWithOffline(url, options = {}) {
            // Check browser offline state
            if (!navigator.onLine) {
                this.handleOffline();
                throw new Error('Network offline');
            }

            // Create timeout promise
            const timeoutPromise = new Promise((_, reject) => {
                setTimeout(() => reject(new Error('Request timeout')), this.fetchTimeoutMs);
            });

            // Race fetch against timeout
            try {
                const response = await Promise.race([
                    fetch(url, options),
                    timeoutPromise
                ]);

                // Check if response is ok
                if (!response.ok) {
                    throw new Error(`HTTP ${response.status}`);
                }

                // Success - hide offline banner if shown
                if (this.isOffline) {
                    this.handleOnline();
                }

                return response;
            } catch (error) {
                // Network error or timeout
                this.handleOffline();
                throw error;
            }
        }
    };

    // Export globally
    window.OfflineFallback = OfflineFallback;

    // Add CSS for offline banner
    const style = document.createElement('style');
    style.textContent = `
        .offline-banner {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);
            color: white;
            padding: 1rem 2rem;
            z-index: 99998;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
            animation: slideDown 0.3s ease-out;
        }

        .offline-banner-hiding {
            animation: slideUp 0.3s ease-out forwards;
        }

        .offline-banner-content {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 1rem;
            max-width: 1920px;
            margin: 0 auto;
        }

        .offline-banner-icon {
            flex-shrink: 0;
        }

        .offline-banner-text {
            text-align: center;
        }

        .offline-banner-title {
            font-size: 1.5rem;
            font-weight: 700;
            line-height: 1.2;
            margin-bottom: 0.25rem;
        }

        .offline-banner-subtitle {
            font-size: 1rem;
            font-weight: 400;
            opacity: 0.95;
        }

        @keyframes slideDown {
            from {
                transform: translateY(-100%);
                opacity: 0;
            }
            to {
                transform: translateY(0);
                opacity: 1;
            }
        }

        @keyframes slideUp {
            from {
                transform: translateY(0);
                opacity: 1;
            }
            to {
                transform: translateY(-100%);
                opacity: 0;
            }
        }

        /* Adjust body padding when banner is shown */
        body.has-offline-banner {
            padding-top: 5rem;
        }

        @media (min-width: 1920px) {
            .offline-banner-title {
                font-size: 2rem;
            }
            .offline-banner-subtitle {
                font-size: 1.25rem;
            }
        }
    `;
    document.head.appendChild(style);
})();

