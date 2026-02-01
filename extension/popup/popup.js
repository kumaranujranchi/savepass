document.addEventListener('DOMContentLoaded', () => {
    const apiUrlInput = document.getElementById('apiUrl');
    const apiKeyInput = document.getElementById('apiKey');
    const saveBtn = document.getElementById('saveBtn');
    const status = document.getElementById('status');

    // Load existing settings
    chrome.storage.local.get(['apiUrl', 'apiKey'], (result) => {
        if (result.apiUrl) apiUrlInput.value = result.apiUrl;
        if (result.apiKey) apiKeyInput.value = result.apiKey;
    });

    // Save settings
    saveBtn.onclick = () => {
        const apiUrl = apiUrlInput.value.trim();
        const apiKey = apiKeyInput.value.trim();

        if (!apiUrl || !apiKey) {
            alert('Please fill in both fields');
            return;
        }

        chrome.storage.local.set({ apiUrl, apiKey }, () => {
            status.style.display = 'block';
            setTimeout(() => {
                status.style.display = 'none';
            }, 2000);
        });
    };
});
