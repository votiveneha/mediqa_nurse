/**
 * Simple In-Page Notification Counter
 * Direct implementation without Vite dependencies
 */

(function() {
    'use strict';
    
    // Get user ID from data attribute
    const headerElement = document.querySelector('[data-user-id]');
    const userId = headerElement ? headerElement.dataset.userId : null;
    
    if (!userId) {
        console.warn('⚠️ No user ID found for notification counter');
        return;
    }
    
    console.log('🔔 Initializing notification counter for user:', userId);
    
    // Counter object
    window.SimpleNotificationCounter = {
        userId: userId,
        unreadCount: 0,
        
        // Initialize
        init: function() {
            this.loadUnreadCount();
            this.setupRealTimeListener();
            // Poll every 30 seconds
            setInterval(() => this.loadUnreadCount(), 30000);
        },
        
        // Load count from server
        loadUnreadCount: function() {
            fetch('/nurse/chat/unread-count')
                .then(response => response.json())
                .then(data => {
                    const count = data.unread_count || 0;
                    this.updateCount(count);
                    console.log('📊 Unread count loaded:', count);
                })
                .catch(error => console.error('❌ Error loading unread count:', error));
        },
        
        // Setup Pusher listener
        setupRealTimeListener: function() {
            if (!window.Echo) {
                console.warn('⚠️ Echo not available, using polling only');
                return;
            }
            
            const channel = window.Echo.private('user.' + this.userId);
            
            channel.bind('new.message', (data) => {
                console.log('📨 New message notification:', data);
                this.incrementCount();
            });
            
            console.log('✅ Real-time listener setup for user.' + this.userId);
        },
        
        // Update count
        updateCount: function(count) {
            this.unreadCount = parseInt(count) || 0;
            this.updateDisplay();
        },
        
        // Increment count
        incrementCount: function() {
            this.unreadCount++;
            this.updateDisplay();
        },
        
        // Update UI
        updateDisplay: function() {
            // Update dropdown text
            const messagesLink = document.querySelector('a.dropdown-item[href*="messages"]');
            if (messagesLink) {
                const count = this.unreadCount;
                messagesLink.textContent = count + ' message' + (count !== 1 ? 's' : '');
            }
            
            // Update/add badge
            const bellIcon = document.querySelector('#dropdownNotify');
            if (bellIcon) {
                // Remove existing badge
                let badge = bellIcon.querySelector('.notify-badge');
                if (badge) {
                    badge.remove();
                }
                
                // Add new badge if count > 0
                if (this.unreadCount > 0) {
                    badge = document.createElement('span');
                    badge.className = 'notify-badge';
                    badge.textContent = this.unreadCount > 99 ? '99+' : this.unreadCount;
                    badge.style.cssText = 'position:absolute;top:-5px;right:-5px;background:#dc3545;color:white;font-size:10px;font-weight:bold;padding:2px 6px;border-radius:10px;min-width:18px;text-align:center;box-shadow:0 2px 4px rgba(0,0,0,0.2);animation:notifyPulse 2s infinite;';
                    bellIcon.style.position = 'relative';
                    bellIcon.appendChild(badge);
                    
                    // Add animation if not exists
                    if (!document.getElementById('notify-pulse-style')) {
                        const style = document.createElement('style');
                        style.id = 'notify-pulse-style';
                        style.textContent = '@keyframes notifyPulse{0%,100%{transform:scale(1);opacity:1;}50%{transform:scale(1.1);opacity:0.8;}}';
                        document.head.appendChild(style);
                    }
                }
            }
            
            // Update page title
            if (this.unreadCount > 0) {
                const baseTitle = document.title.replace(/\(\d+\)\s*/, '');
                document.title = '(' + this.unreadCount + ') ' + baseTitle;
            }
        },
        
        // Reset count
        resetCount: function() {
            this.unreadCount = 0;
            this.updateDisplay();
        }
    };
    
    // Auto-initialize when page loads
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', () => {
            setTimeout(() => window.SimpleNotificationCounter.init(), 500);
        });
    } else {
        setTimeout(() => window.SimpleNotificationCounter.init(), 500);
    }
    
    console.log('✅ SimpleNotificationCounter loaded');
})();
