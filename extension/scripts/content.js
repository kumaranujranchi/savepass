/**
 * SecureVault Content Script
 * Detects password forms and triggers "Save to Vault" prompt
 */

let lastPasswordData = null;

// Listen for form submissions
document.addEventListener('submit', (e) => {
    const form = e.target;
    const passwordFields = form.querySelectorAll('input[type="password"]');
    const userFields = form.querySelectorAll('input[type="text"], input[type="email"]');
    
    if (passwordFields.length > 0) {
        const username = userFields.length > 0 ? userFields[0].value : '';
        const password = passwordFields[0].value;
        const service = window.location.hostname;
        
        lastPasswordData = {
            service_name: service,
            username: username,
            password: password
        };
        
        // Notify background script about potential password capture
        chrome.runtime.sendMessage({
            type: 'PASSWORD_CAPTURED',
            data: lastPasswordData
        });
        
        console.log('SecureVault: Password capture triggered');
    }
}, true);

// Listen for messages from background script
chrome.runtime.onMessage.addListener((message, sender, sendResponse) => {
    if (message.type === 'SHOW_SAVE_PROMPT') {
        showSavePrompt(message.data);
    }
});

/**
 * Display a premium overlay prompt to save the password
 */
function showSavePrompt(data) {
    if (document.getElementById('sv-save-prompt')) return;

    const overlay = document.createElement('div');
    overlay.id = 'sv-save-prompt';
    overlay.innerHTML = `
        <div class="sv-prompt-container">
            <div class="sv-prompt-logo">🔒</div>
            <div class="sv-prompt-content">
                <div class="sv-prompt-title">Save to SecureVault?</div>
                <div class="sv-prompt-subtitle">Would you like to save credentials for <b>${data.service_name}</b>?</div>
            </div>
            <div class="sv-prompt-actions">
                <button id="sv-btn-ignore" class="sv-btn-secondary">Ignore</button>
                <button id="sv-btn-save" class="sv-btn-primary">Save Now</button>
            </div>
        </div>
    `;

    document.body.appendChild(overlay);

    // Button actions
    document.getElementById('sv-btn-ignore').onclick = () => {
        overlay.remove();
    };

    document.getElementById('sv-btn-save').onclick = () => {
        chrome.runtime.sendMessage({
            type: 'CONFIRM_SAVE',
            data: data
        }, (response) => {
            if (response && response.success) {
                overlay.innerHTML = `<div class="sv-prompt-container"><div class="sv-prompt-title">✅ Saved to SecureVault!</div></div>`;
                setTimeout(() => overlay.remove(), 2000);
            } else {
                alert('SecureVault: ' + (response ? response.message : 'Connection failed'));
            }
        });
    };
}
