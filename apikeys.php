<?php
require_once "includes/auth_session.php";
require_once "config/db.php";
require_once "includes/functions.php";

$user_id = $_SESSION["id"];
$service_name = $api_key = $env = "";

// Handle Add
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['add_key'])) {
    $service_name = cleanInput($_POST["service_name"]);
    $api_key_raw = $_POST["api_key"];
    $env = cleanInput($_POST["environment"]);

    if (!empty($service_name) && !empty($api_key_raw)) {
        $api_key_enc = $api_key_raw; // Already encrypted on client
        $sql = "INSERT INTO api_keys (user_id, service_name, api_key_enc, environment) VALUES (:user_id, :service_name, :api_key_enc, :env)";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([':user_id' => $user_id, ':service_name' => $service_name, ':api_key_enc' => $api_key_enc, ':env' => $env]);
        header("location: apikeys.php"); // Refresh
        exit;
    }
}

// Fetch Keys
$stmt = $pdo->prepare("SELECT * FROM api_keys WHERE user_id = :id ORDER BY created_at DESC");
$stmt->execute([':id' => $user_id]);
$keys = $stmt->fetchAll(PDO::FETCH_ASSOC);

require_once "includes/header.php";
?>

<h1 class="page-title">API Key Manager</h1>
<p class="page-subtitle">Securely store and manage your service API keys and secrets.</p>

<!-- Premium Add Form -->
<div class="section-card" style="margin-bottom: 2.5rem; padding: 2rem;">
    <div class="section-header"
        style="margin-bottom: 2rem; padding: 0; display: flex; justify-content: space-between; align-items: flex-start;">
        <div>
            <span class="section-title">Add New API Key</span>
            <p style="color: var(--text-dim); font-size: 0.85rem; margin-top: 4px;">Store your keys securely with
                AES-256 encryption.</p>
        </div>
        <button type="button" onclick="scanAllKeys()" class="btn btn-pro"
            style="padding: 0.5rem 1rem; font-size: 0.8rem; background: rgba(255, 69, 58, 0.1); color: #ff453a; border: 1px solid #ff453a;">
            <i data-lucide="shield-alert" style="width: 14px; height: 14px; margin-right: 6px;"></i>
            Scan for Leaks
        </button>
    </div>

    <form id="keyForm" method="post" action="" onsubmit="return handleKeySubmit(event)">
        <div
            style="display: grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap: 1.5rem; margin-bottom: 1.5rem;">
            <div>
                <label
                    style="display: block; font-size: 0.75rem; color: var(--text-dim); text-transform: uppercase; font-weight: 800; margin-bottom: 10px; letter-spacing: 0.5px;">Service
                    Name</label>
                <input type="text" name="service_name" placeholder="e.g. AWS, Stripe, Google" required
                    style="margin-bottom: 0; background: #1a1c26; border: 1px solid var(--border-color); border-radius: 12px; height: 52px; width: 100%; padding: 0 1.25rem;">
            </div>
            <div>
                <label
                    style="display: block; font-size: 0.75rem; color: var(--text-dim); text-transform: uppercase; font-weight: 800; margin-bottom: 10px; letter-spacing: 0.5px;">Environment</label>
                <select name="environment"
                    style="margin-bottom: 0; background: #1a1c26; border: 1px solid var(--border-color); border-radius: 12px; height: 52px; width: 100%; padding: 0 1.25rem; color: var(--text-primary);">
                    <option value="Development">Development</option>
                    <option value="Staging">Staging</option>
                    <option value="Production">Production</option>
                </select>
            </div>
        </div>

        <div style="margin-bottom: 2rem;">
            <label
                style="display: block; font-size: 0.75rem; color: var(--text-dim); text-transform: uppercase; font-weight: 800; margin-bottom: 10px; letter-spacing: 0.5px;">API
                Key Secret</label>
            <div style="position: relative;">
                <input type="text" name="api_key" placeholder="Paste your secret key here" required
                    style="margin-bottom: 0; background: #1a1c26; border: 1px solid var(--border-color); border-radius: 12px; height: 52px; width: 100%; padding: 0 1.25rem; padding-left: 3.5rem;">
                <i data-lucide="key"
                    style="position: absolute; left: 1.25rem; top: 50%; transform: translateY(-50%); color: var(--text-dim); width: 20px; height: 20px;"></i>
            </div>
        </div>

        <button type="submit" name="add_key" class="btn btn-primary"
            style="width: auto; min-width: 180px; padding: 1rem 3rem; background: var(--accent-primary); color: white; border: none; font-weight: 800; border-radius: 12px; font-size: 0.95rem; cursor: pointer; transition: 0.3s; box-shadow: 0 4px 15px rgba(44, 15, 189, 0.3);">
            Save Connection
        </button>
    </form>
</div>

<div class="section-card">
    <div style="overflow-x: auto;">
        <table class="activity-table">
            <thead>
                <tr>
                    <th>Service</th>
                    <th>Environment</th>
                    <th>API Key Secret</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($keys as $key): ?>
                    <tr>
                        <td>
                            <div class="service-cell">
                                <div class="service-icon"><?php echo strtoupper(substr($key['service_name'], 0, 1)); ?>
                                </div>
                                <div class="app-name"><?php echo htmlspecialchars($key['service_name']); ?></div>
                            </div>
                        </td>
                        <td>
                            <?php
                            $env_class = 'strong'; // default
                            $envv = strtolower($key['environment']);
                            if ($envv == 'development')
                                $env_class = 'strong'; // Purple/Primary
                            if ($envv == 'staging')
                                $env_class = 'strong'; // Green
                            if ($envv == 'production')
                                $env_class = 'strong'; // Warning/Red
                            ?>
                            <span class="strength-pill <?php echo $env_class; ?>"
                                style="background: <?php
                                if ($envv == 'development')
                                    echo 'rgba(44, 15, 189, 0.15)';
                                elseif ($envv == 'staging')
                                    echo 'rgba(0, 184, 148, 0.15)';
                                elseif ($envv == 'production')
                                    echo 'rgba(255, 159, 67, 0.15)';
                                ?>; color: <?php
                                if ($envv == 'development')
                                    echo 'var(--accent-primary)';
                                elseif ($envv == 'staging')
                                    echo 'var(--green-sec)';
                                elseif ($envv == 'production')
                                    echo '#ff9f43';
                                ?>; border: none; font-weight: 800; text-transform: uppercase; font-size: 0.7rem; padding: 4px 10px;">
                                <?php echo htmlspecialchars($key['environment']); ?>
                            </span>
                        </td>
                        <td>
                            <div class="key-mask" style="display: flex; align-items: center; gap: 8px;">
                                <span class="masked"
                                    style="color: var(--text-dim); letter-spacing: 2px;">••••••••••••••••</span>
                                <span class="revealed" data-encrypted="<?php echo $key['api_key_enc']; ?>"
                                    style="display: none; color: var(--text-secondary); font-family: monospace;">••••••••</span>
                                <div class="leak-status" style="display: none;">
                                    <span class="strength-pill"
                                        style="background: rgba(255, 69, 58, 0.15); color: #ff453a; border: none; font-weight: 800; font-size: 0.6rem; padding: 2px 6px;">⚠️
                                        EXPOSED</span>
                                </div>
                            </div>
                        </td>
                        <td>
                            <div style="display: flex; gap: 12px; color: var(--text-dim);">
                                <i data-lucide="eye" class="icon-btn" style="width: 18px; height: 18px;"
                                    onclick="toggleReveal(this)" title="Toggle Visibility"></i>
                                <i data-lucide="shield-check" class="icon-btn scan-btn" style="width: 18px; height: 18px;"
                                    onclick="scanIndividualKey(this)" title="Scan for Exposure"></i>
                                <i data-lucide="copy" class="icon-btn" style="width: 18px; height: 18px;"
                                    onclick="copyKey('<?php echo $key['api_key_enc']; ?>', this)" title="Copy Key"></i>
                                <i data-lucide="trash-2" class="icon-btn" style="width: 18px; height: 18px;"
                                    title="Delete"></i>
                            </div>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<script>
    function toggleReveal(btn) {
        var row = btn.closest('tr');
        var masked = row.querySelector('.masked');
        var revealed = row.querySelector('.revealed');

        if (masked.style.display !== 'none') {
            const encrypted = revealed.getAttribute('data-encrypted');
            const key = CryptoHelper.getSessionKey();
            if (key) {
                const plaintext = CryptoHelper.decrypt(encrypted, key);
                revealed.innerText = plaintext;
                masked.style.display = 'none';
                revealed.style.display = 'inline';
            } else {
                alert("Master Key not found. Please re-login.");
            }
        } else {
            masked.style.display = 'inline';
            revealed.style.display = 'none';
            revealed.innerText = '••••••••';
        }
    }

    function copyWithFeedback(text, buttonElement) {
        if (!text) return;

        navigator.clipboard.writeText(text).then(() => {
            // Change icon temporarily
            const originalIcon = buttonElement.getAttribute('data-lucide');
            buttonElement.setAttribute('data-lucide', 'check');
            buttonElement.style.color = 'var(--green-sec)';
            lucide.createIcons();

            showToast("Copied to clipboard!");

            // Trigger automatic clipboard clear
            if (typeof SecurityManager !== 'undefined') {
                SecurityManager.scheduleClipboardClear();
            }

            // Restore icon after 2 seconds
            setTimeout(() => {
                buttonElement.setAttribute('data-lucide', originalIcon);
                buttonElement.style.color = '';
                lucide.createIcons();
            }, 2000);
        }).catch(err => {
            showToast('Failed to copy');
            console.error('Copy failed:', err);
        });
    }

    function copyKey(ciphertext, btn) {
        const key = CryptoHelper.getSessionKey();
        if (!key) {
            showToast("Master Key not found.");
            return;
        }

        const plaintext = CryptoHelper.decrypt(ciphertext, key);
        if (plaintext !== "[Decryption Error]") {
            copyWithFeedback(plaintext, btn);
        } else {
            showToast("Decryption failed.");
        }
    }

    function showToast(msg) {
        let toast = document.getElementById("toast");
        if (!toast) {
            toast = document.createElement("div");
            toast.id = "toast";
            toast.style = "visibility: hidden; min-width: 250px; margin-left: -125px; background-color: #333; color: #fff; text-align: center; border-radius: 2px; padding: 16px; position: fixed; z-index: 1; left: 50%; bottom: 30px; font-size: 17px;";
            document.body.appendChild(toast);
        }
        toast.innerText = msg;
        toast.style.visibility = "visible";
        setTimeout(function () { toast.style.visibility = "hidden"; }, 3000);
    }

    async function scanIndividualKey(btn) {
        const row = btn.closest('tr');
        const encrypted = row.querySelector('.revealed').getAttribute('data-encrypted');
        const leakStatusDiv = row.querySelector('.leak-status');

        btn.classList.add('rotating'); // Add a CSS class for animation if needed
        btn.style.color = 'var(--accent-primary)';

        const key = CryptoHelper.getSessionKey();
        if (!key) {
            showToast("Master Key not found. Please re-login.");
            btn.classList.remove('rotating');
            return;
        }

        const plaintext = CryptoHelper.decrypt(encrypted, key);
        if (plaintext === "[Decryption Error]") {
            showToast("Decryption failed.");
            btn.classList.remove('rotating');
            return;
        }

        const leakCount = await SecurityManager.checkExposed(plaintext);

        btn.classList.remove('rotating');

        if (leakCount > 0) {
            leakStatusDiv.style.display = 'block';
            showToast(`⚠️ Warning: This key has been found in ${leakCount} breaches!`);
            btn.style.color = '#ff453a';
            btn.setAttribute('data-lucide', 'shield-alert');
        } else {
            leakStatusDiv.style.display = 'none';
            showToast("✅ Solid! This key is not found in known breaches.");
            btn.style.color = '#00e676';
            btn.setAttribute('data-lucide', 'shield-check');
        }
        lucide.createIcons();
    }

    async function scanAllKeys() {
        showToast("🔍 Scanning all keys for exposure...");
        const scanButtons = document.querySelectorAll('.scan-btn');
        for (let btn of scanButtons) {
            await scanIndividualKey(btn);
        }
    }

    function handleKeySubmit(e) {
        const form = e.target;
        const key = CryptoHelper.getSessionKey();
        if (!key) {
            alert("Security Error: Master Key missing.");
            return false;
        }
        form.api_key.value = CryptoHelper.encrypt(form.api_key.value, key);
        return true;
    }
</script>

<?php require_once "includes/footer.php"; ?>