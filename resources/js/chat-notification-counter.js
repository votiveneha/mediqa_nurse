/**
 * Chat Notification Counter
 * Real-time notification count for new messages
 */

(function() {
    'use strict';

    // Initialize notification counter
    window.ChatNotificationCounter = {
        userId: null,
        unreadCount: 0,
        checkInterval: null,
        echo: null,

        /**
         * Initialize the notification counter
         */
        init: function(userId, echoInstance = null) {
            this.userId = userId;
            this.echo = echoInstance;

            console.log('🔔 Initializing Chat Notification Counter for user:', userId);

            // Load initial count
            this.loadUnreadCount();

            // Setup real-time listeners
            this.setupRealTimeListeners();

            // Poll for updates every 30 seconds as backup
            this.startPolling();
        },

        /**
         * Load unread count from server
         */
        loadUnreadCount: function() {
            fetch('/nurse/chat/unread-count')
                .then(response => response.json())
                .then(data => {
                    if (data.unread_count !== undefined) {
                        this.updateCount(data.unread_count);
                        console.log('📊 Unread count loaded:', data.unread_count);
                    }
                })
                .catch(error => console.error('❌ Error loading unread count:', error));
        },

        /**
         * Setup real-time listeners via Pusher
         */
        setupRealTimeListeners: function() {
            if (!this.echo) {
                console.warn('⚠️ Echo not available, using polling only');
                return;
            }

            // Listen on user's private channel for new message notifications
            const channel = this.echo.private(`user.${this.userId}`);

            channel.bind('new.message', (data) => {
                console.log('📨 New message notification received:', data);
                this.incrementCount();
            });

            console.log('✅ Real-time notification listener setup complete');
        },

        /**
         * Increment unread count
         */
        incrementCount: function() {
            this.unreadCount++;
            this.updateDisplay();
            this.showNotification();
        },

        /**
         * Update unread count
         */
        updateCount: function(count) {
            this.unreadCount = parseInt(count) || 0;
            this.updateDisplay();
        },

        /**
         * Reset unread count to 0 (when user views messages)
         */
        resetCount: function() {
            this.unreadCount = 0;
            this.updateDisplay();
        },

        /**
         * Update the display
         */
        updateDisplay: function() {
            // Update badge on bell icon
            const bellIcon = document.querySelector('#dropdownNotify');
            if (bellIcon) {
                // Remove existing badge if any
                const existingBadge = bellIcon.querySelector('.notify-badge');
                if (existingBadge) {
                    existingBadge.remove();
                }

                // Add new badge if count > 0
                if (this.unreadCount > 0) {
                    const badge = document.createElement('span');
                    badge.className = 'notify-badge';
                    badge.textContent = this.unreadCount > 99 ? '99+' : this.unreadCount;
                    badge.style.cssText = `
                        position: absolute;
                        top: -5px;
                        right: -5px;
                        background: #dc3545;
                        color: white;
                        font-size: 10px;
                        font-weight: bold;
                        padding: 2px 6px;
                        border-radius: 10px;
                        min-width: 18px;
                        text-align: center;
                        box-shadow: 0 2px 4px rgba(0,0,0,0.2);
                        animation: notifyPulse 2s infinite;
                    `;

                    // Add pulse animation
                    if (!document.getElementById('notify-pulse-style')) {
                        const style = document.createElement('style');
                        style.id = 'notify-pulse-style';
                        style.textContent = `
                            @keyframes notifyPulse {
                                0%, 100% { transform: scale(1); opacity: 1; }
                                50% { transform: scale(1.1); opacity: 0.8; }
                            }
                        `;
                        document.head.appendChild(style);
                    }

                    bellIcon.style.position = 'relative';
                    bellIcon.appendChild(badge);
                }

                // Update dropdown text
                const messagesLink = document.querySelector('a.dropdown-item[href*="messages"]');
                if (messagesLink) {
                    messagesLink.textContent = `${this.unreadCount} message${this.unreadCount !== 1 ? 's' : ''}`;
                }
            }

            // Update page title
            if (this.unreadCount > 0) {
                const baseTitle = document.title.replace(/\(\d+\)\s*/, '');
                document.title = `(${this.unreadCount}) ${baseTitle}`;
            }
        },

        /**
         * Show browser notification
         */
        showNotification: function() {
            if (document.hidden && 'Notification' in window && Notification.permission === 'granted') {
                new Notification('New Message', {
                    body: 'You have a new message',
                    icon: '/nurse/assets/imgs/logo.png',
                    badge: '/nurse/assets/imgs/logo.png'
                });
            }
        },

        /**
         * Start polling as backup
         */
        startPolling: function() {
            // Check every 30 seconds
            this.checkInterval = setInterval(() => {
                this.loadUnreadCount();
            }, 30000);
        },

        /**
         * Stop polling
         */
        stopPolling: function() {
            if (this.checkInterval) {
                clearInterval(this.checkInterval);
                this.checkInterval = null;
            }
        }
    };

    // Auto-initialize when DOM is ready
    document.addEventListener('DOMContentLoaded', function() {
        const userIdElement = document.querySelector('[data-user-id]');
        const userId = userIdElement ? userIdElement.dataset.userId : null;

        if (userId) {
            window.ChatNotificationCounter.init(userId, window.Echo || null);
            console.log('✅ ChatNotificationCounter initialized');
        } else {
            console.warn('⚠️ No user ID found, notification counter not initialized');
        }
    });
})();
