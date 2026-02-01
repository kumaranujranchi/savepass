/**
 * Advanced Password Generator Module
 * Provides customizable password generation with strength calculation
 */

const PasswordGenerator = {
    // Character sets
    UPPERCASE: 'ABCDEFGHIJKLMNOPQRSTUVWXYZ',
    LOWERCASE: 'abcdefghijklmnopqrstuvwxyz',
    NUMBERS: '0123456789',
    SYMBOLS: '!@#$%^&*()_+-=[]{}|;:,.<>?',

    /**
     * Generate password with custom options
     * @param {Object} options - Generation options
     * @returns {string} Generated password
     */
    generate: function(options = {}) {
        const defaults = {
            length: 16,
            uppercase: true,
            lowercase: true,
            numbers: true,
            symbols: true
        };

        const opts = { ...defaults, ...options };
        
        // Build character pool
        let chars = '';
        let requiredChars = [];

        if (opts.uppercase) {
            chars += this.UPPERCASE;
            requiredChars.push(this.getRandomChar(this.UPPERCASE));
        }
        if (opts.lowercase) {
            chars += this.LOWERCASE;
            requiredChars.push(this.getRandomChar(this.LOWERCASE));
        }
        if (opts.numbers) {
            chars += this.NUMBERS;
            requiredChars.push(this.getRandomChar(this.NUMBERS));
        }
        if (opts.symbols) {
            chars += this.SYMBOLS;
            requiredChars.push(this.getRandomChar(this.SYMBOLS));
        }

        if (chars.length === 0) {
            chars = this.LOWERCASE; // Fallback
        }

        // Generate password ensuring at least one char from each selected type
        let password = requiredChars.join('');
        const remainingLength = opts.length - requiredChars.length;

        for (let i = 0; i < remainingLength; i++) {
            password += this.getRandomChar(chars);
        }

        // Shuffle the password
        return this.shuffle(password);
    },

    /**
     * Get random character from string using crypto API
     */
    getRandomChar: function(str) {
        const array = new Uint32Array(1);
        crypto.getRandomValues(array);
        return str[array[0] % str.length];
    },

    /**
     * Shuffle string using Fisher-Yates algorithm
     */
    shuffle: function(str) {
        const arr = str.split('');
        for (let i = arr.length - 1; i > 0; i--) {
            const array = new Uint32Array(1);
            crypto.getRandomValues(array);
            const j = array[0] % (i + 1);
            [arr[i], arr[j]] = [arr[j], arr[i]];
        }
        return arr.join('');
    },

    /**
     * Calculate password strength (0-100)
     */
    calculateStrength: function(password) {
        if (!password) return 0;

        let score = 0;
        const length = password.length;

        // Length score (max 40 points)
        score += Math.min(length * 2, 40);

        // Character variety (max 60 points)
        const hasUpper = /[A-Z]/.test(password);
        const hasLower = /[a-z]/.test(password);
        const hasNumber = /[0-9]/.test(password);
        const hasSymbol = /[^A-Za-z0-9]/.test(password);

        if (hasUpper) score += 15;
        if (hasLower) score += 15;
        if (hasNumber) score += 15;
        if (hasSymbol) score += 15;

        return Math.min(score, 100);
    },

    /**
     * Get strength label and color
     */
    getStrengthInfo: function(score) {
        if (score >= 80) {
            return { label: 'Strong', color: '#00e676', bars: 4 };
        } else if (score >= 60) {
            return { label: 'Good', color: '#00b0ff', bars: 3 };
        } else if (score >= 40) {
            return { label: 'Fair', color: '#ff9100', bars: 2 };
        } else {
            return { label: 'Weak', color: '#f44336', bars: 1 };
        }
    }
};

/**
 * Password Visibility Toggle
 */
function togglePasswordVisibility(inputId, iconElement) {
    const input = document.getElementById(inputId);
    const isPassword = input.type === 'password';
    
    input.type = isPassword ? 'text' : 'password';
    
    // Update icon
    if (iconElement) {
        iconElement.setAttribute('data-lucide', isPassword ? 'eye-off' : 'eye');
        lucide.createIcons(); // Refresh icons
    }
}

/**
 * Copy to clipboard with visual feedback
 */
function copyWithFeedback(text, buttonElement) {
    if (!text) return;
    
    navigator.clipboard.writeText(text).then(() => {
        // Change icon temporarily
        const originalIcon = buttonElement.getAttribute('data-lucide');
        buttonElement.setAttribute('data-lucide', 'check');
        lucide.createIcons();
        
        // Show toast
        showToast('Copied to clipboard!');
        
        // Restore icon after 2 seconds
        setTimeout(() => {
            buttonElement.setAttribute('data-lucide', originalIcon);
            lucide.createIcons();
        }, 2000);
    }).catch(err => {
        showToast('Failed to copy');
        console.error('Copy failed:', err);
    });
}
