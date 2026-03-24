/**
 * Chat System - Real-time Messaging
 * MediQa Nurse-Healthcare Chat Application
 */

import Echo from 'laravel-echo';
import Pusher from 'pusher-js';

// Setup Pusher and Echo
window.Pusher = Pusher;

window.Echo = new Echo({
    broadcaster: 'pusher',
    key: process.env.MIX_PUSHER_APP_KEY || 'your-pusher-key',
    cluster: process.env.MIX_PUSHER_APP_CLUSTER || 'mt1',
    forceTLS: true,
    encrypted: true,
    authEndpoint: '/broadcasting/auth',
});

/**
 * Chat Manager Class
 * Handles all real-time chat functionality
 */
class ChatManager {
    constructor(conversationId) {
        this.conversationId = conversationId;
        this.typingTimeout = null;
        this.isTyping = false;
        this.lastScrollTop = 0;
        
        this.init();
    }

    /**
     * Initialize chat manager
     */
    init() {
        this.setupElements();
        this.listenForMessages();
        this.listenForTyping();
        this.listenForPresence();
        this.setupMessageForm();
        this.setupTypingDetection();
        this.setupFileUpload();
        this.setupEmojiPicker();
        this.autoResizeTextarea();
        this.scrollToBottom();
        this.startHeartbeat();
    }

    /**
     * Setup DOM element references
     */
    setupElements() {
        this.chatMessages = document.getElementById('chatMessages');
        this.messageForm = document.getElementById('messageForm');
        this.messageInput = document.getElementById('messageInput');
        this.sendBtn = document.getElementById('sendBtn');
        this.fileInput = document.getElementById('fileInput');
        this.attachFileBtn = document.getElementById('attachFileBtn');
        this.typingIndicator = document.getElementById('typingIndicator');
    }

    /**
     * Listen for incoming messages
     */
    listenForMessages() {
        Echo.private(`conversation.${this.conversationId}`)
            .listen('.message.sent', (event) => {
                console.log('Message received:', event);
                this.appendMessage(event);
                this.playNotificationSound();
                this.updateTitleNotification();
            });
    }

    /**
     * Listen for typing events
     */
    listenForTyping() {
        Echo.join(`conversation.${this.conversationId}.presence`)
            .here((users) => {
                console.log('Users in conversation:', users);
                this.updateOnlineStatus(users);
            })
            .joining((user) => {
                console.log('User joined:', user);
                this.showUserOnline(user);
            })
            .leaving((user) => {
                console.log('User left:', user);
                this.showUserOffline(user);
            })
            .listen('.user.typing', (event) => {
                this.toggleTypingIndicator(event);
            });
    }

    /**
     * Listen for presence events
     */
    listenForPresence() {
        // Handle presence channel events
    }

    /**
     * Setup message form submission
     */
    setupMessageForm() {
        if (!this.messageForm) return;

        this.messageForm.addEventListener('submit', async (e) => {
            e.preventDefault();
            
            const message = this.messageInput.value.trim();
            if (!message && !this.fileInput.files[0]) return;

            if (this.fileInput.files[0]) {
                this.uploadFile();
            } else {
                this.sendMessage(message);
            }
        });

        // Send on Ctrl+Enter
        this.messageInput.addEventListener('keydown', (e) => {
            if (e.key === 'Enter' && e.ctrlKey) {
                e.preventDefault();
                this.messageForm.dispatchEvent(new Event('submit'));
            }
        });
    }

    /**
     * Send message via AJAX
     */
    async sendMessage(message) {
        const formData = new FormData();
        formData.append('conversation_id', this.conversationId);
        formData.append('message', message);
        formData.append('_token', window.Laravel.csrfToken);

        try {
            const response = await fetch(this.getSendUrl(), {
                method: 'POST',
                body: formData,
                headers: {
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest',
                },
            });

            const data = await response.json();
            
            if (data.success) {
                this.appendMessage({
                    ...data.message,
                    sender_id: data.message.sender.id,
                    sender_name: data.message.sender.name,
                    sender_avatar: data.message.sender.profile_img,
                });
                this.messageInput.value = '';
                this.stopTyping();
                this.scrollToBottom();
            }
        } catch (error) {
            console.error('Error sending message:', error);
            this.showError('Failed to send message');
        }
    }

    /**
     * Upload file attachment
     */
    async uploadFile() {
        const file = this.fileInput.files[0];
        if (!file) return;

        // Validate file size (10MB max)
        if (file.size > 10 * 1024 * 1024) {
            this.showError('File size must be less than 10MB');
            return;
        }

        const formData = new FormData();
        formData.append('conversation_id', this.conversationId);
        formData.append('file', file);
        formData.append('_token', window.Laravel.csrfToken);

        try {
            const response = await fetch(this.getUploadUrl(), {
                method: 'POST',
                body: formData,
                headers: {
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest',
                },
            });

            const data = await response.json();
            
            if (data.success) {
                this.appendMessage({
                    ...data.message,
                    sender_id: data.message.sender.id,
                    sender_name: data.message.sender.name,
                    sender_avatar: data.message.sender.profile_img,
                });
                this.fileInput.value = '';
                this.scrollToBottom();
            }
        } catch (error) {
            console.error('Error uploading file:', error);
            this.showError('Failed to upload file');
        }
    }

    /**
     * Setup typing detection
     */
    setupTypingDetection() {
        if (!this.messageInput) return;

        this.messageInput.addEventListener('input', () => {
            this.startTyping();
        });

        this.messageInput.addEventListener('blur', () => {
            this.stopTyping();
        });

        this.messageInput.addEventListener('focus', () => {
            // Mark messages as read when focusing on chat
            this.markAsRead();
        });
    }

    /**
     * Start typing indicator
     */
    startTyping() {
        if (this.typingTimeout) {
            clearTimeout(this.typingTimeout);
        }

        if (!this.isTyping) {
            this.isTyping = true;
            this.broadcastTyping(true);
        }

        this.typingTimeout = setTimeout(() => {
            this.stopTyping();
        }, 2000);
    }

    /**
     * Stop typing indicator
     */
    stopTyping() {
        if (this.typingTimeout) {
            clearTimeout(this.typingTimeout);
            this.typingTimeout = null;
        }

        if (this.isTyping) {
            this.isTyping = false;
            this.broadcastTyping(false);
        }
    }

    /**
     * Broadcast typing status
     */
    broadcastTyping(isTyping) {
        Echo.private(`conversation.${this.conversationId}`)
            .whisper('typing', {
                conversationId: this.conversationId,
                userId: window.Laravel.userId,
                userName: window.Laravel.userName,
                userAvatar: window.Laravel.userAvatar,
                isTyping: isTyping,
            });
    }

    /**
     * Toggle typing indicator UI
     */
    toggleTypingIndicator(event) {
        if (!this.typingIndicator) return;

        if (event.is_typing && event.user_id !== window.Laravel.userId) {
            this.typingIndicator.style.display = 'flex';
            const typingText = this.typingIndicator.querySelector('.typing-text');
            if (typingText) {
                typingText.textContent = `${event.user_name} is typing...`;
            }
            this.scrollToBottom();
        } else {
            this.typingIndicator.style.display = 'none';
        }
    }

    /**
     * Append message to chat
     */
    appendMessage(event) {
        const isSent = event.sender_id === window.Laravel.userId;
        const messageClass = isSent ? 'sent' : 'received';
        const time = new Date(event.created_at).toLocaleTimeString([], { 
            hour: '2-digit', 
            minute: '2-digit' 
        });

        let messageContent = '';
        
        if (event.message_type === 'file') {
            messageContent = `
                <div class="message-file">
                    <i class="fas fa-file"></i>
                    <a href="${event.file_url}" download target="_blank">
                        ${event.file_name}
                    </a>
                    ${event.file_size ? `<span class="file-size">(${this.formatFileSize(event.file_size)})</span>` : ''}
                </div>
            `;
        } else if (event.message_type === 'image') {
            messageContent = `
                <div class="message-image">
                    <img src="${event.file_url}" alt="Image" class="img-fluid">
                </div>
            `;
        } else {
            messageContent = `<p class="message-text">${this.escapeHtml(event.message)}</p>`;
        }

        const messageHtml = `
            <div class="message ${messageClass}" data-message-id="${event.id}">
                ${!isSent ? `
                <div class="message-avatar">
                    <img src="${event.sender_avatar}" alt="${event.sender_name}">
                </div>
                ` : ''}
                <div class="message-content">
                    <div class="message-header">
                        ${!isSent ? `<span class="sender-name">${event.sender_name}</span>` : ''}
                        <span class="message-time">${time}</span>
                    </div>
                    ${messageContent}
                    ${isSent ? `
                    <div class="message-status">
                        <i class="fas fa-check"></i>
                    </div>
                    ` : ''}
                    <div class="message-actions">
                        <button class="btn-action" onclick="window.chatManager.replyToMessage(${event.id})" title="Reply">
                            <i class="fas fa-reply"></i>
                        </button>
                        ${isSent ? `
                        <button class="btn-action" onclick="window.chatManager.deleteMessage(${event.id})" title="Delete">
                            <i class="fas fa-trash"></i>
                        </button>
                        ` : ''}
                    </div>
                </div>
            </div>
        `;

        this.chatMessages.insertAdjacentHTML('beforeend', messageHtml);
    }

    /**
     * Setup file upload button
     */
    setupFileUpload() {
        if (!this.attachFileBtn || !this.fileInput) return;

        this.attachFileBtn.addEventListener('click', () => {
            this.fileInput.click();
        });

        this.fileInput.addEventListener('change', () => {
            if (this.fileInput.files[0]) {
                this.messageForm.dispatchEvent(new Event('submit'));
            }
        });
    }

    /**
     * Setup emoji picker
     */
    setupEmojiPicker() {
        const emojiBtn = document.querySelector('.btn-emoji');
        if (!emojiBtn) return;

        emojiBtn.addEventListener('click', () => {
            // Simple emoji insertion - can be enhanced with a full emoji picker
            const emojis = ['😀', '😂', '😍', '👍', '❤️', '🎉', '👏', '🙏'];
            const randomEmoji = emojis[Math.floor(Math.random() * emojis.length)];
            this.messageInput.value += randomEmoji;
            this.messageInput.focus();
        });
    }

    /**
     * Auto-resize textarea
     */
    autoResizeTextarea() {
        if (!this.messageInput) return;

        this.messageInput.addEventListener('input', function() {
            this.style.height = 'auto';
            this.style.height = Math.min(this.scrollHeight, 150) + 'px';
        });
    }

    /**
     * Scroll to bottom of chat
     */
    scrollToBottom() {
        if (this.chatMessages) {
            this.chatMessages.scrollTop = this.chatMessages.scrollHeight;
        }
    }

    /**
     * Mark messages as read
     */
    async markAsRead() {
        try {
            await fetch(this.getMarkAsReadUrl(), {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest',
                },
                body: JSON.stringify({
                    conversation_id: this.conversationId,
                    _token: window.Laravel.csrfToken,
                }),
            });
        } catch (error) {
            console.error('Error marking messages as read:', error);
        }
    }

    /**
     * Delete message
     */
    async deleteMessage(messageId) {
        if (!confirm('Are you sure you want to delete this message?')) return;

        try {
            const response = await fetch(this.getDeleteMessageUrl(), {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest',
                },
                body: JSON.stringify({
                    message_id: messageId,
                    _token: window.Laravel.csrfToken,
                }),
            });

            const data = await response.json();
            
            if (data.success) {
                const messageElement = document.querySelector(`[data-message-id="${messageId}"]`);
                if (messageElement) {
                    messageElement.style.opacity = '0';
                    setTimeout(() => messageElement.remove(), 300);
                }
            }
        } catch (error) {
            console.error('Error deleting message:', error);
            this.showError('Failed to delete message');
        }
    }

    /**
     * Reply to message
     */
    replyToMessage(messageId) {
        const messageElement = document.querySelector(`[data-message-id="${messageId}"]`);
        const messageText = messageElement?.querySelector('.message-text')?.textContent || '';
        
        const replyPreview = document.getElementById('replyPreview');
        const replyToText = document.getElementById('replyToText');
        
        if (replyPreview && replyToText) {
            window.replyToMessageId = messageId;
            replyToText.textContent = messageText.substring(0, 50) + (messageText.length > 50 ? '...' : '');
            replyPreview.style.display = 'flex';
            this.messageInput.focus();
        }
    }

    /**
     * Play notification sound
     */
    playNotificationSound() {
        // Only play if not focused on the window
        if (document.hidden) {
            const audio = new Audio('/sounds/message.mp3');
            audio.play().catch(() => {}); // Silent fail if audio not available
        }
    }

    /**
     * Update title with unread count
     */
    updateTitleNotification() {
        if (document.hidden) {
            let count = parseInt(document.title.match(/\((\d+)\)/)?.[1] || 0) + 1;
            document.title = `(${count}) New Message`;
        }
    }

    /**
     * Show error message
     */
    showError(message) {
        alert(message);
    }

    /**
     * Escape HTML
     */
    escapeHtml(text) {
        const div = document.createElement('div');
        div.textContent = text;
        return div.innerHTML;
    }

    /**
     * Format file size
     */
    formatFileSize(bytes) {
        if (!bytes) return '';
        const units = ['B', 'KB', 'MB', 'GB'];
        let i = 0;
        while (bytes > 1024) {
            bytes /= 1024;
            i++;
        }
        return `${bytes.toFixed(1)} ${units[i]}`;
    }

    /**
     * Get send URL based on user role
     */
    getSendUrl() {
        return window.Laravel.userRole === 1
            ? '/nurse/chat/send'
            : '/healthcare-facilities/chat/send';
    }

    /**
     * Get upload URL
     */
    getUploadUrl() {
        return window.Laravel.userRole === 1
            ? '/nurse/chat/upload'
            : '/healthcare-facilities/chat/upload';
    }

    /**
     * Get mark as read URL
     */
    getMarkAsReadUrl() {
        return window.Laravel.userRole === 1
            ? '/nurse/chat/mark-as-read'
            : '/healthcare-facilities/chat/mark-as-read';
    }

    /**
     * Get delete message URL
     */
    getDeleteMessageUrl() {
        return window.Laravel.userRole === 1
            ? '/nurse/chat/delete'
            : '/healthcare-facilities/chat/delete';
    }

    /**
     * Update online status
     */
    updateOnlineStatus(users) {
        // Update online indicators for users in the conversation
    }

    /**
     * Show user online
     */
    showUserOnline(user) {
        const statusElement = document.querySelector('.online-status');
        if (statusElement) {
            statusElement.classList.add('online');
            statusElement.innerHTML = '<i class="fas fa-circle"></i> Online';
        }
    }

    /**
     * Show user offline
     */
    showUserOffline(user) {
        const statusElement = document.querySelector('.online-status');
        if (statusElement) {
            statusElement.classList.remove('online');
            statusElement.innerHTML = '<i class="fas fa-circle"></i> Offline';
        }
    }

    /**
     * Start heartbeat to keep connection alive
     */
    startHeartbeat() {
        setInterval(() => {
            // Send heartbeat to keep connection alive
            this.markAsRead();
        }, 60000); // Every minute
    }
}

// Export for use in views
window.ChatManager = ChatManager;

// Auto-initialize if chat container exists
document.addEventListener('DOMContentLoaded', () => {
    const chatContainer = document.querySelector('.chat-container');
    if (chatContainer && window.Laravel?.conversationId) {
        window.chatManager = new ChatManager(window.Laravel.conversationId);
    }
});
