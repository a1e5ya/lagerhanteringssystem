/**
 * Centralized Message System 
 * 
 * - Overlay positioning (doesn't change page layout)
 * - Auto-dismiss after 5 seconds
 * - Security: HTML escaping by default
 * - Multiple message types with proper styling
 * - Accessibility compliant
 * - Mobile responsive
 * - Stack multiple messages

 * 
 * @version 1.0
 * @author Axxell
 */



class MessageSystem {
    constructor() {
        this.container = null;
        this.messageCounter = 0;
        this.maxMessages = 5; // Maximum simultaneous messages
        this.defaultDuration = 5000; // 5 seconds
        this.init();
    }

    /**
     * Initialize the message system
     */
    init() {
        // Create the overlay container if it doesn't exist
        this.createContainer();
        
        // Add required CSS if not already present
        this.addStyles();
        
        // Expose global function for backward compatibility
        window.showMessage = (message, type = 'info', options = {}) => {
            this.show(message, type, options);
        };
        
        console.log('MessageSystem initialized');
    }

    /**
     * Create the message container overlay
     */
    createContainer() {
        if (document.getElementById('ka-message-overlay')) {
            this.container = document.getElementById('ka-message-overlay');
            return;
        }

        this.container = document.createElement('div');
        this.container.id = 'ka-message-overlay';
        this.container.className = 'ka-message-overlay';
        this.container.setAttribute('aria-live', 'polite');
        this.container.setAttribute('aria-atomic', 'false');
        
        document.body.appendChild(this.container);
    }

    /**
     * Add required CSS styles
     */
    addStyles() {
        if (document.getElementById('ka-message-styles')) {
            return; // Styles already added
        }

        const styles = document.createElement('style');
        styles.id = 'ka-message-styles';
        styles.textContent = `
            .ka-message-overlay {
                position: fixed;
                top: 100px;
                right: 20px;
                z-index: 10000;
                max-width: 400px;
                pointer-events: none;
            }

            .ka-message {
                margin-bottom: 10px;
                padding: 22px 16px;
                border-radius: 6px;
                box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
                font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
                font-size: 14px;
                line-height: 1.4;
                pointer-events: auto;
                position: relative;
                overflow: hidden;
                animation: ka-message-slide-in 0.3s cubic-bezier(0.16, 1, 0.3, 1);
                border-left: 4px solid transparent;
                display: flex;
                align-items: flex-start;
                gap: 8px;
            }

            .ka-message.ka-removing {
                animation: ka-message-slide-out 0.3s cubic-bezier(0.4, 0, 1, 1);
            }

            .ka-message-icon {
                flex-shrink: 0;
                margin-top: 1px;
                font-size: 16px;
            }

            .ka-message-content {
                flex: 1;
                word-wrap: break-word;
            }

            .ka-message-close {
                flex-shrink: 0;
                background: none;
                border: none;
                font-size: 18px;
                cursor: pointer;
                opacity: 0.6;
                padding: 0;
                margin-left: 8px;
                margin-top: -2px;
                line-height: 1;
                color: inherit;
            }

            .ka-message-close:hover {
                opacity: 1;
            }

            .ka-message-progress {
                position: absolute;
                bottom: 0;
                left: 0;
                height: 3px;
                background: rgba(255, 255, 255, 0.3);
                border-radius: 0 0 6px 6px;
                animation: ka-message-progress 5s linear;
            }

            /* Message Types */
            .ka-message.success {
                background: #10b981;
                color: white;
                border-left-color: #059669;
            }

            .ka-message.error,
            .ka-message.danger {
                background: #ef4444;
                color: white;
                border-left-color: #dc2626;
            }

            .ka-message.warning {
                background: #f59e0b;
                color: white;
                border-left-color: #d97706;
            }

            .ka-message.info {
                background: #3b82f6;
                color: white;
                border-left-color: #2563eb;
            }

            /* Animations */
            @keyframes ka-message-slide-in {
                from {
                    opacity: 0;
                    transform: translateX(100%);
                }
                to {
                    opacity: 1;
                    transform: translateX(0);
                }
            }

            @keyframes ka-message-slide-out {
                from {
                    opacity: 1;
                    transform: translateX(0);
                    max-height: 100px;
                    margin-bottom: 10px;
                }
                to {
                    opacity: 0;
                    transform: translateX(100%);
                    max-height: 0;
                    margin-bottom: 0;
                }
            }

            @keyframes ka-message-progress {
                from { width: 100%; }
                to { width: 0%; }
            }

            /* Mobile responsiveness */
            @media (max-width: 480px) {
                .ka-message-overlay {
                    left: 10px;
                    right: 10px;
                    max-width: none;
                }
                
                .ka-message {
                    font-size: 13px;
                }
            }

            /* High contrast mode support */
            @media (prefers-contrast: high) {
                .ka-message {
                    border-width: 2px;
                    border-style: solid;
                }
            }

            /* Reduced motion support */
            @media (prefers-reduced-motion: reduce) {
                .ka-message {
                    animation: none;
                }
                
                .ka-message-progress {
                    animation: none;
                }
            }
        `;
        
        document.head.appendChild(styles);
    }

    /**
     * Show a message
     * 
     * @param {string} message - The message text
     * @param {string} type - Message type: success, error, danger, warning, info
     * @param {object} options - Additional options
     */
    show(message, type = 'info', options = {}) {
        const config = {
            duration: options.duration || this.defaultDuration,
            closable: options.closable !== false,
            html: options.html === true,
            sound: options.sound === true,
            ...options
        };

        // Validate and sanitize input
        if (!message || typeof message !== 'string') {
            console.warn('MessageSystem: Invalid message provided');
            return;
        }

        // Normalize type
        type = this.normalizeType(type);

        // Limit number of simultaneous messages
        this.enforceMessageLimit();

        // Create message element
        const messageEl = this.createMessageElement(message, type, config);
        
        // Add to container
        this.container.appendChild(messageEl);

        // Play sound if requested
        if (config.sound) {
            this.playNotificationSound(type);
        }

        // Auto-dismiss if duration > 0
        if (config.duration > 0) {
            setTimeout(() => {
                this.dismiss(messageEl);
            }, config.duration);
        }

        // Return message element for manual control
        return messageEl;
    }

    /**
     * Create message element
     */
    createMessageElement(message, type, config) {
        const messageEl = document.createElement('div');
        messageEl.className = `ka-message ${type}`;
        messageEl.setAttribute('role', 'alert');
        messageEl.setAttribute('aria-atomic', 'true');
        
        // Generate unique ID
        const messageId = `ka-message-${++this.messageCounter}`;
        messageEl.id = messageId;

        // Get icon for message type
        const icon = this.getIcon(type);
        
        // Sanitize message content
        const content = config.html ? message : this.escapeHtml(message);

        // Build HTML
        messageEl.innerHTML = `
            <div class="ka-message-icon">${icon}</div>
            <div class="ka-message-content">${content}</div>
            ${config.closable ? '<button class="ka-message-close" aria-label="Stäng meddelande">&times;</button>' : ''}
            ${config.duration > 0 ? '<div class="ka-message-progress" style="animation-duration: ' + config.duration + 'ms;"></div>' : ''}
        `;

        // Add close button functionality
        if (config.closable) {
            const closeBtn = messageEl.querySelector('.ka-message-close');
            closeBtn.addEventListener('click', () => {
                this.dismiss(messageEl);
            });
        }

        return messageEl;
    }

    /**
     * Get icon for message type
     */
    getIcon(type) {
        const icons = {
            success: '✓',
            error: '✕',
            danger: '✕',
            warning: '⚠',
            info: 'ℹ'
        };
        
        return icons[type] || icons.info;
    }

    /**
     * Normalize message type
     */
    normalizeType(type) {
        const typeMap = {
            'success': 'success',
            'error': 'error',
            'danger': 'error',
            'fail': 'error',
            'failure': 'error',
            'warning': 'warning',
            'warn': 'warning',
            'info': 'info',
            'information': 'info'
        };

        return typeMap[type.toLowerCase()] || 'info';
    }

    /**
     * Enforce maximum message limit
     */
    enforceMessageLimit() {
        const messages = this.container.querySelectorAll('.ka-message:not(.ka-removing)');
        
        if (messages.length >= this.maxMessages) {
            // Remove oldest message
            const oldestMessage = messages[0];
            this.dismiss(oldestMessage);
        }
    }

    /**
     * Dismiss a message
     */
    dismiss(messageEl) {
        if (!messageEl || messageEl.classList.contains('ka-removing')) {
            return;
        }

        messageEl.classList.add('ka-removing');
        
        // Remove after animation
        setTimeout(() => {
            if (messageEl.parentNode) {
                messageEl.parentNode.removeChild(messageEl);
            }
        }, 300);
    }

    /**
     * Dismiss all messages
     */
    dismissAll() {
        const messages = this.container.querySelectorAll('.ka-message:not(.ka-removing)');
        messages.forEach(message => this.dismiss(message));
    }

    /**
     * Escape HTML to prevent XSS
     */
    escapeHtml(text) {
        const div = document.createElement('div');
        div.textContent = text;
        return div.innerHTML;
    }

    /**
     * Play notification sound (optional)
     */
    playNotificationSound(type) {
        try {
            // Create audio context if supported
            if (typeof AudioContext !== 'undefined' || typeof webkitAudioContext !== 'undefined') {
                const audioContext = new (AudioContext || webkitAudioContext)();
                const oscillator = audioContext.createOscillator();
                const gainNode = audioContext.createGain();
                
                oscillator.connect(gainNode);
                gainNode.connect(audioContext.destination);
                
                // Different frequencies for different message types
                const frequencies = {
                    success: 800,
                    error: 400,
                    warning: 600,
                    info: 500
                };
                
                oscillator.frequency.setValueAtTime(frequencies[type] || 500, audioContext.currentTime);
                gainNode.gain.setValueAtTime(0.1, audioContext.currentTime);
                gainNode.gain.exponentialRampToValueAtTime(0.01, audioContext.currentTime + 0.1);
                
                oscillator.start(audioContext.currentTime);
                oscillator.stop(audioContext.currentTime + 0.1);
            }
        } catch (error) {
            // Silently fail if audio is not supported
        }
    }

    /**
     * Convenience methods for different message types
     */
    success(message, options = {}) {
        return this.show(message, 'success', options);
    }

    error(message, options = {}) {
        return this.show(message, 'error', options);
    }

    warning(message, options = {}) {
        return this.show(message, 'warning', options);
    }

    info(message, options = {}) {
        return this.show(message, 'info', options);
    }
}

// Initialize the message system when DOM is ready
document.addEventListener('DOMContentLoaded', function() {
    window.messageSystem = new MessageSystem();
});

// If DOM is already loaded, initialize immediately
if (document.readyState === 'loading') {
    // Already set up the DOMContentLoaded listener above
} else {
    window.messageSystem = new MessageSystem();
}