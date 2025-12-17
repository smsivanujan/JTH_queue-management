/**
 * TV Fullscreen Mode Handler
 * Auto-activates fullscreen for TV displays
 * Works with mouse, touch, and remote click
 */

(function() {
    'use strict';

    const TVFullscreen = {
        overlay: null,
        isFullscreen: false,
        hasAttemptedFullscreen: false,

        /**
         * Initialize TV fullscreen mode
         */
        init() {
            // Create overlay if it doesn't exist
            this.createOverlay();

            // Check if already in fullscreen
            this.checkFullscreenState();

            // Listen for fullscreen changes
            this.setupFullscreenListeners();

            // Setup user interaction handlers
            this.setupInteractionHandlers();

            // Apply TV optimizations
            this.applyTVOptimizations();

            // Show overlay on page load if not in fullscreen
            if (!this.isFullscreen) {
                this.showOverlay();
            }
        },

        /**
         * Create fullscreen activation overlay
         */
        createOverlay() {
            if (document.getElementById('tv-fullscreen-overlay')) {
                this.overlay = document.getElementById('tv-fullscreen-overlay');
                return;
            }

            const overlay = document.createElement('div');
            overlay.id = 'tv-fullscreen-overlay';
            overlay.className = 'tv-fullscreen-overlay';
            overlay.innerHTML = `
                <div class="tv-fullscreen-content">
                    <div class="tv-fullscreen-icon">
                        <svg class="w-24 h-24 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 8V4m0 0h4M4 4l5 5m11-1V4m0 0h-4m4 0l-5 5M4 16v4m0 0h4m-4 0l5-5m11 5l-5-5m5 5v-4m0 4h-4"/>
                        </svg>
                    </div>
                    <h2 class="tv-fullscreen-title">Tap anywhere to enter fullscreen</h2>
                    <p class="tv-fullscreen-subtitle">TV Display Mode</p>
                </div>
            `;

            // Add click/touch handler
            overlay.addEventListener('click', (e) => {
                e.preventDefault();
                e.stopPropagation();
                this.enterFullscreen();
            });

            overlay.addEventListener('touchstart', (e) => {
                e.preventDefault();
                e.stopPropagation();
                this.enterFullscreen();
            });

            document.body.appendChild(overlay);
            this.overlay = overlay;
        },

        /**
         * Show fullscreen overlay
         */
        showOverlay() {
            if (this.overlay) {
                this.overlay.classList.add('active');
            }
        },

        /**
         * Hide fullscreen overlay
         */
        hideOverlay() {
            if (this.overlay) {
                this.overlay.classList.remove('active');
            }
        },

        /**
         * Enter fullscreen mode
         */
        enterFullscreen() {
            const element = document.documentElement;

            // Try different fullscreen methods for browser compatibility
            const methods = [
                () => element.requestFullscreen(),
                () => element.webkitRequestFullscreen(), // Safari
                () => element.mozRequestFullScreen(), // Firefox
                () => element.msRequestFullscreen(), // IE/Edge
            ];

            for (const method of methods) {
                try {
                    const promise = method();
                    if (promise) {
                        promise
                            .then(() => {
                                this.isFullscreen = true;
                                this.hasAttemptedFullscreen = true;
                                this.hideOverlay();
                            })
                            .catch((err) => {
                                console.warn('Fullscreen request failed:', err);
                            });
                        return;
                    }
                } catch (err) {
                    // Try next method
                    continue;
                }
            }

            // If all methods fail, hide overlay anyway (might be in kiosk mode)
            this.hideOverlay();
        },

        /**
         * Exit fullscreen mode
         */
        exitFullscreen() {
            const methods = [
                () => document.exitFullscreen(),
                () => document.webkitExitFullscreen(),
                () => document.mozCancelFullScreen(),
                () => document.msExitFullscreen(),
            ];

            for (const method of methods) {
                try {
                    method();
                    break;
                } catch (err) {
                    continue;
                }
            }
        },

        /**
         * Check current fullscreen state
         */
        checkFullscreenState() {
            const isFullscreen = !!(
                document.fullscreenElement ||
                document.webkitFullscreenElement ||
                document.mozFullScreenElement ||
                document.msFullscreenElement
            );

            this.isFullscreen = isFullscreen;
            return isFullscreen;
        },

        /**
         * Setup fullscreen change listeners
         */
        setupFullscreenListeners() {
            const events = [
                'fullscreenchange',
                'webkitfullscreenchange',
                'mozfullscreenchange',
                'MSFullscreenChange',
            ];

            events.forEach(event => {
                document.addEventListener(event, () => {
                    const wasFullscreen = this.isFullscreen;
                    this.checkFullscreenState();

                    // If fullscreen was exited, show overlay again
                    if (wasFullscreen && !this.isFullscreen) {
                        this.showOverlay();
                    } else if (this.isFullscreen) {
                        this.hideOverlay();
                    }
                });
            });
        },

        /**
         * Setup interaction handlers (keyboard shortcuts, etc.)
         */
        setupInteractionHandlers() {
            // F11 key to toggle fullscreen
            document.addEventListener('keydown', (e) => {
                if (e.key === 'F11') {
                    e.preventDefault();
                    if (this.isFullscreen) {
                        this.exitFullscreen();
                    } else {
                        this.enterFullscreen();
                    }
                }
            });
        },

        /**
         * Apply TV optimizations
         */
        applyTVOptimizations() {
            // Disable text selection
            document.addEventListener('selectstart', (e) => {
                e.preventDefault();
            });

            // Disable right-click context menu
            document.addEventListener('contextmenu', (e) => {
                e.preventDefault();
            });

            // Disable zoom gestures (pinch zoom)
            document.addEventListener('gesturestart', (e) => {
                e.preventDefault();
            });

            document.addEventListener('gesturechange', (e) => {
                e.preventDefault();
            });

            document.addEventListener('gestureend', (e) => {
                e.preventDefault();
            });

            // Disable double-tap zoom
            let lastTouchEnd = 0;
            document.addEventListener('touchend', (e) => {
                const now = Date.now();
                if (now - lastTouchEnd <= 300) {
                    e.preventDefault();
                }
                lastTouchEnd = now;
            }, false);

            // Prevent drag
            document.addEventListener('dragstart', (e) => {
                e.preventDefault();
            });

            // Add CSS for TV optimizations
            const style = document.createElement('style');
            style.textContent = `
                /* TV Optimizations */
                * {
                    -webkit-user-select: none !important;
                    -moz-user-select: none !important;
                    -ms-user-select: none !important;
                    user-select: none !important;
                    -webkit-touch-callout: none !important;
                    -webkit-tap-highlight-color: transparent !important;
                }

                /* Hide scrollbars */
                ::-webkit-scrollbar {
                    display: none;
                }
                * {
                    scrollbar-width: none;
                    -ms-overflow-style: none;
                }

                /* Fullscreen Overlay Styles */
                .tv-fullscreen-overlay {
                    position: fixed;
                    top: 0;
                    left: 0;
                    width: 100%;
                    height: 100%;
                    background: rgba(0, 0, 0, 0.95);
                    backdrop-filter: blur(10px);
                    z-index: 99999;
                    display: flex;
                    align-items: center;
                    justify-content: center;
                    opacity: 0;
                    visibility: hidden;
                    transition: opacity 0.3s ease, visibility 0.3s ease;
                    cursor: pointer;
                }

                .tv-fullscreen-overlay.active {
                    opacity: 1;
                    visibility: visible;
                }

                .tv-fullscreen-content {
                    text-align: center;
                    color: white;
                }

                .tv-fullscreen-icon {
                    margin: 0 auto 2rem;
                    animation: pulse 2s ease-in-out infinite;
                }

                .tv-fullscreen-title {
                    font-size: 2.5rem;
                    font-weight: 700;
                    margin-bottom: 0.5rem;
                    text-shadow: 0 2px 10px rgba(0, 0, 0, 0.5);
                }

                .tv-fullscreen-subtitle {
                    font-size: 1.25rem;
                    opacity: 0.8;
                    font-weight: 400;
                }

                @keyframes pulse {
                    0%, 100% {
                        transform: scale(1);
                        opacity: 1;
                    }
                    50% {
                        transform: scale(1.1);
                        opacity: 0.8;
                    }
                }

                /* Prevent accidental scrolling */
                body.tv-fullscreen-active {
                    overflow: hidden !important;
                    position: fixed;
                    width: 100%;
                    height: 100%;
                }
            `;
            document.head.appendChild(style);
        }
    };

    // Initialize when DOM is ready
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', () => TVFullscreen.init());
    } else {
        TVFullscreen.init();
    }

    // Export for manual control if needed
    window.TVFullscreen = TVFullscreen;
})();

