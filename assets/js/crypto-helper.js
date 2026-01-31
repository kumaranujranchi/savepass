/**
 * SecureVault Crypto Helper
 * Handles Zero-Knowledge Client-Side Encryption
 */

const CryptoHelper = {
    // PBKDF2 Configuration
    ITERATIONS: 100000,
    KEY_SIZE: 256 / 32, // 256 bits

    /**
     * Derives a Master Key from a password and salt using PBKDF2.
     * @param {string} password 
     * @param {string} email (used as salt)
     */
    deriveMasterKey: function(password, email) {
        const salt = CryptoJS.enc.Utf8.parse(email.toLowerCase());
        const key = CryptoJS.PBKDF2(password, salt, {
            keySize: this.KEY_SIZE,
            iterations: this.ITERATIONS,
            hasher: CryptoJS.algo.SHA256
        });
        return key.toString();
    },

    /**
     * Derives an Auth Hash from the Master Key for server-side verification.
     * The server never sees the actual password or Master Key.
     * @param {string} masterKey 
     */
    deriveAuthHash: function(masterKey) {
        return CryptoJS.SHA256(masterKey).toString();
    },

    /**
     * Encrypts plaintext using AES-256.
     * @param {string} plaintext 
     * @param {string} key 
     */
    encrypt: function(plaintext, key) {
        if (!plaintext) return "";
        try {
            return CryptoJS.AES.encrypt(plaintext, key).toString();
        } catch (e) {
            console.error("Encryption failed", e);
            return "";
        }
    },

    /**
     * Decrypts ciphertext using AES-256.
     * @param {string} ciphertext 
     * @param {string} key 
     */
    decrypt: function(ciphertext, key) {
        if (!ciphertext) return "";
        try {
            const bytes = CryptoJS.AES.decrypt(ciphertext, key);
            return bytes.toString(CryptoJS.enc.Utf8);
        } catch (e) {
            // console.error("Decryption failed", e);
            return "[Decryption Error]";
        }
    },

    /**
     * Session Storage for Master Key
     */
    setSessionKey: function(key) {
        sessionStorage.setItem('vault_master_key', key);
    },

    getSessionKey: function() {
        return sessionStorage.getItem('vault_master_key');
    },

    clearSessionKey: function() {
        sessionStorage.removeItem('vault_master_key');
    }
};
