<?php
require_once "includes/auth_session.php";
require_once "config/db.php";
require_once "includes/functions.php";

$user_id = $_SESSION["id"];
$import_status = "";
$import_count = 0;

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["encrypted_data"])) {
    $data_list = json_decode($_POST["encrypted_data"], true);
    if ($data_list) {
        foreach ($data_list as $item) {
            $sql = "INSERT INTO vault_items (user_id, app_name, website_url, username, password_enc, category) 
                    VALUES (:user_id, :app_name, :website_url, :username, :password_enc, 'Other')";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([
                ':user_id' => $user_id,
                ':app_name' => cleanInput($item['name']),
                ':website_url' => cleanInput($item['url']),
                ':username' => cleanInput($item['user']),
                ':password_enc' => $item['pass'] // Already encrypted on client
            ]);
            $import_count++;
        }
        $import_status = "Successfully imported $import_count passwords!";
    }
}

require_once "includes/header.php";
?>

<div class="page-container">
    <h1 class="page-title">Sync Browser Passwords</h1>
    <p class="page-subtitle">Upload the CSV file exported from your browser (Chrome, Safari, Edge).</p>

    <?php if ($import_status): ?>
        <div class="section-card"
            style="background: rgba(44, 15, 189, 0.1); border: 1px solid var(--accent-primary); border-radius: 12px; padding: 1.5rem; margin-bottom: 2rem;">
            <div style="display: flex; align-items: center; gap: 1rem;">
                <i data-lucide="check-circle" style="width: 24px; height: 24px; color: #4caf50;"></i>
                <span style="font-weight: 700; color: white;">
                    <?php echo $import_status; ?>
                </span>
            </div>
            <a href="passwords.php" class="btn btn-primary"
                style="margin-top: 1.5rem; width: auto; padding: 0.8rem 2rem;">View Passwords</a>
        </div>
    <?php endif; ?>

    <div class="section-card" style="padding: 2.5rem; max-width: 600px;">
        <form id="importForm" method="post" onsubmit="return handleImport(event)">
            <input type="hidden" name="encrypted_data" id="encrypted_data">
            <div style="border: 2px dashed var(--border-color); border-radius: 16px; padding: 3rem 2rem; text-align: center; margin-bottom: 2rem; background: rgba(255,255,255,0.02); transition: 0.3s;"
                id="dropZone" onmouseover="this.style.background='rgba(255,255,255,0.04)'"
                onmouseout="this.style.background='rgba(255,255,255,0.02)'">
                <i data-lucide="upload-cloud"
                    style="width: 48px; height: 48px; margin-bottom: 1.5rem; opacity: 0.5;"></i>
                <h3 style="margin-bottom: 10px;">Select CSV File</h3>
                <p style="color: var(--text-dim); font-size: 0.9rem; margin-bottom: 2rem;">Only .csv files are
                    supported. Export from Chrome Settings > Passwords.</p>

                <input type="file" id="csv_file" accept=".csv" required style="display: none;"
                    onchange="updateFileName(this)">
                <label for="csv_file" class="btn-pro"
                    style="cursor: pointer; display: inline-flex; width: auto; padding: 12px 24px;">
                    Choose File
                </label>
                <div id="file-name"
                    style="margin-top: 1rem; color: var(--accent-secondary); font-weight: 800; font-size: 0.85rem;">
                </div>
            </div>

            <button type="submit" id="submitBtn" class="btn btn-primary"
                style="width: 100%; padding: 1rem; font-weight: 800; font-size: 1rem; border-radius: 12px; box-shadow: 0 4px 15px rgba(44, 15, 189, 0.3);">
                Start Global Encrypted Import
            </button>
        </form>
    </div>

    <div class="section-card" style="margin-top: 3rem; padding: 2rem;">
        <h3 style="margin-bottom: 1.5rem; display: flex; align-items: center; gap: 10px;">
            <i data-lucide="info" style="width: 20px; height: 20px; color: var(--accent-secondary);"></i> Help: How to
            export?
        </h3>
        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 1.5rem;">
            <div>
                <div style="font-weight: 800; color: var(--text-primary); margin-bottom: 8px; font-size: 0.9rem;">Google
                    Chrome</div>
                <p style="font-size: 0.8rem; color: var(--text-dim); line-height: 1.5;">Settings > Passwords > Three
                    dots (⋮) > Export Passwords...</p>
            </div>
            <div>
                <div style="font-weight: 800; color: var(--text-primary); margin-bottom: 8px; font-size: 0.9rem;">Safari
                    (Mac)</div>
                <p style="font-size: 0.8rem; color: var(--text-dim); line-height: 1.5;">Settings > Passwords > Share
                    button > Export All Passwords...</p>
            </div>
            <div>
                <div style="font-weight: 800; color: var(--text-primary); margin-bottom: 8px; font-size: 0.9rem;">MSEdge
                </div>
                <p style="font-size: 0.8rem; color: var(--text-dim); line-height: 1.5;">Settings > Profiles > Passwords
                    > More actions > Export Passwords...</p>
            </div>
        </div>
    </div>
</div>

<script>
    function updateFileName(input) {
        const fileName = input.files[0] ? input.files[0].name : '';
        document.getElementById('file-name').textContent = fileName ? 'Selected: ' + fileName : '';
    }

    async function handleImport(e) {
        e.preventDefault();
        const form = e.target;
        const fileInput = document.getElementById('csv_file');
        const key = CryptoHelper.getSessionKey();

        if (!key) {
            alert("Master Key missing. Log in again.");
            return false;
        }

        if (!fileInput.files.length) return false;

        const file = fileInput.files[0];
        const text = await file.text();
        const rows = text.split('\n').map(row => row.split(','));
        const header = rows[0].map(h => h.toLowerCase().trim().replace(/"/g, ''));

        // Basic CSV mapping
        const nameIdx = header.indexOf('name') !== -1 ? header.indexOf('name') : header.indexOf('title');
        const urlIdx = header.indexOf('url') !== -1 ? header.indexOf('url') : header.indexOf('website');
        const userIdx = header.indexOf('username') !== -1 ? header.indexOf('username') : header.indexOf('user');
        const passIdx = header.indexOf('password') !== -1 ? header.indexOf('password') : header.indexOf('pass');

        const encryptedData = [];
        const submitBtn = document.getElementById('submitBtn');
        submitBtn.innerText = "Encrypting locally...";
        submitBtn.disabled = true;

        for (let i = 1; i < rows.length; i++) {
            const row = rows[i];
            if (row.length < 2) continue;

            const name = row[nameIdx] ? row[nameIdx].trim().replace(/"/g, '') : "Imported Item";
            const url = row[urlIdx] ? row[urlIdx].trim().replace(/"/g, '') : "";
            const user = row[userIdx] ? row[userIdx].trim().replace(/"/g, '') : "";
            const pass = row[passIdx] ? row[passIdx].trim().replace(/"/g, '') : "";

            if (pass) {
                encryptedData.push({
                    name: name,
                    url: url,
                    user: user,
                    pass: CryptoHelper.encrypt(pass, key)
                });
            }
        }

        document.getElementById('encrypted_data').value = JSON.stringify(encryptedData);
        submitBtn.innerText = "Saving to vault...";
        form.submit();
    }
</script>

<?php require_once "includes/footer.php"; ?>