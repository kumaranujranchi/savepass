/**
 * Security Management Module
 * Handles Auto-Lock (idle timeout) and Clipboard Protection
 */

const SecurityManager = {
    idleTimer: null,
    clipboardTimer: null,
    
    // Default settings (will be overridden by localStorage/Settings)
    config: {
        autoLockTimeout: 5, // minutes
        clipboardClearDelay: 30, // seconds
        lockOnClose: true
    },

    /**
     * Initialize security features
     */
    init: function() {
        this.loadConfig();
        this.setupIdleTimer();
        this.setupWindowEvents();
        console.log('Security Manager initialized');
    },

    /**
     * Load settings from localStorage
     */
    loadConfig: function() {
        const saved = localStorage.getItem('vault_security_config');
        if (saved) {
            try {
                this.config = { ...this.config, ...JSON.parse(saved) };
            } catch (e) {
                console.error('Failed to parse security config', e);
            }
        }
    },

    /**
     * Save settings to localStorage
     */
    saveConfig: function(newConfig) {
        this.config = { ...this.config, ...newConfig };
        localStorage.setItem('vault_security_config', JSON.stringify(this.config));
        this.setupIdleTimer(); // Restart timer with new setting
    },

    /**
     * Setup idle detection
     */
    setupIdleTimer: function() {
        if (this.idleTimer) clearTimeout(this.idleTimer);
        
        if (this.config.autoLockTimeout === 0) return; // Disabled

        const timeoutMs = this.config.autoLockTimeout * 60 * 1000;
        
        const resetTimer = () => {
            clearTimeout(this.idleTimer);
            this.idleTimer = setTimeout(() => this.lockVault('timeout'), timeoutMs);
        };

        // Events that indicate activity
        const events = ['mousedown', 'mousemove', 'keypress', 'scroll', 'touchstart'];
        events.forEach(name => document.addEventListener(name, resetTimer, true));
        
        resetTimer();
    },

    /**
     * Setup window/close events
     */
    setupWindowEvents: function() {
        window.addEventListener('beforeunload', () => {
            if (this.config.lockOnClose) {
                // We can't always guarantee redirect on close, but we can clear sensitive storage
                // Note: sessionStorage clears on tab close anyway, but we might want to proactively clear keys
                // if we were using a different storage mechanism.
            }
        });

        // Lock when tab becomes hidden (optional, but highly secure)
        /*
        document.addEventListener('visibilitychange', () => {
            if (document.hidden && this.config.lockOnClose) {
                this.lockVault('hidden');
            }
        });
        */
    },

    /**
     * Lock the vault by clearing session keys and redirecting
     */
    lockVault: function(reason) {
        console.log('Locking vault due to:', reason);
        // Clear master key from session storage
        if (typeof CryptoHelper !== 'undefined') {
            sessionStorage.removeItem('master_key');
        }
        
        // Redirect to login with message
        window.location.href = 'logout.php?reason=' + reason;
    },

    /**
     * Schedule clipboard clearing
     */
    scheduleClipboardClear: function() {
        if (this.clipboardTimer) clearTimeout(this.clipboardTimer);
        
        if (this.config.clipboardClearDelay === 0) return; // Disabled

        const delayMs = this.config.clipboardClearDelay * 1000;
        
        this.clipboardTimer = setTimeout(() => {
            this.clearClipboard();
        }, delayMs);
    },

    /**
     * Clear the clipboard
     */
    clearClipboard: function() {
        if (navigator.clipboard && navigator.clipboard.writeText) {
            navigator.clipboard.writeText('').then(() => {
                if (typeof showToast === 'function') {
                    showToast('Clipboard cleared for security');
                }
            }).catch(err => {
                console.warn('Could not clear clipboard:', err);
            });
        }
    }
};

// Auto-initialize when script loads
document.addEventListener('DOMContentLoaded', () => {
    SecurityManager.init();
});
