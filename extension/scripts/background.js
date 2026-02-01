/**
 * SecureVault Background Service Worker
 */

chrome.runtime.onMessage.addListener((message, sender, sendResponse) => {
    if (message.type === 'PASSWORD_CAPTURED') {
        // Automatically trigger the prompt in the active tab
        chrome.tabs.sendMessage(sender.tab.id, {
            type: 'SHOW_SAVE_PROMPT',
            data: message.data
        });
    }
    
    if (message.type === 'CONFIRM_SAVE') {
        handleSave(message.data).then(sendResponse);
        return true; // async response
    }
});

async function handleSave(data) {
    try {
        const settings = await chrome.storage.local.get(['apiUrl', 'apiKey']);
        const apiUrl = settings.apiUrl || 'http://localhost/api/save_password.php';
        const apiKey = settings.apiKey;

        if (!apiKey) {
            return { success: false, message: 'API Key not set. Please open extension settings.' };
        }

        const response = await fetch(apiUrl, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-API-Key': apiKey
            },
            body: JSON.stringify({
                service_name: data.service_name,
                username: data.username,
                password_enc: data.password, // Ideally encrypt with master key here if possible
                category: 'Extension'
            })
        });

        const result = await response.json();
        return result;
    } catch (error) {
        console.error('Save failed:', error);
        return { success: false, message: 'Server unreachable' };
    }
}
