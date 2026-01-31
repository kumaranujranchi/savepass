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
        $api_key_enc = encryptData($api_key_raw);
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
<div class="dashboard-card" style="margin-bottom: 2rem;">
    <div class="card-header">
        <span class="card-title">Add New API Key</span>
    </div>
    <form method="post" action="">
        <div style="display: flex; gap: 1rem; margin-bottom: 1rem;">
            <div style="flex: 1;">
                <label
                    style="display: block; font-size: 0.75rem; color: var(--text-dim); text-transform: uppercase; font-weight: 800; margin-bottom: 8px; letter-spacing: 0.5px;">Service
                    Name</label>
                <input type="text" name="service_name" placeholder="e.g. AWS, Stripe, Google Cloud" required
                    style="margin-bottom: 0;">
            </div>
            <div style="flex: 1;">
                <label
                    style="display: block; font-size: 0.75rem; color: var(--text-dim); text-transform: uppercase; font-weight: 800; margin-bottom: 8px; letter-spacing: 0.5px;">Environment</label>
                <select name="environment" style="margin-bottom: 0;">
                    <option value="Development">Development</option>
                    <option value="Staging">Staging</option>
                    <option value="Production">Production</option>
                </select>
            </div>
        </div>
        <div style="margin-bottom: 2rem;">
            <label
                style="display: block; font-size: 0.75rem; color: var(--text-dim); text-transform: uppercase; font-weight: 800; margin-bottom: 8px; letter-spacing: 0.5px;">API
                Key Secret</label>
            <input type="text" name="api_key" placeholder="Paste your API key here" required style="margin-bottom: 0;">
        </div>
        <button type="submit" name="add_key" class="btn btn-primary"
            style="width: auto; padding: 0.9rem 3rem; background: var(--accent-primary); color: white; border: none; font-weight: 700; border-radius: 8px; font-size: 0.9rem;">Save
            Key</button>
    </form>
</div>

<div class="key-list">
    <?php foreach ($keys as $key): ?>
        <?php $decrypted_key = decryptData($key['api_key_enc']); ?>
        <div class="dashboard-card"
            style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1rem;">
            <div>
                <div style="font-weight: 700; font-size: 1.1rem; color: var(--text-primary);">
                    <?php echo htmlspecialchars($key['service_name']); ?>
                </div>
                <div
                    style="font-size: 0.75rem; color: var(--text-dim); margin-top: 4px; text-transform: uppercase; font-weight: 800;">
                    Environment: <span
                        style="color: var(--accent-secondary);"><?php echo htmlspecialchars($key['environment']); ?></span>
                </div>
            </div>
            <div style="display: flex; align-items: center; gap: 12px;">
                <div class="key-mask"
                    style="font-family: monospace; background: #1a1c26; padding: 10px 16px; border-radius: 8px; font-size: 0.9rem; border: 1px solid var(--border-color); color: var(--text-secondary);">
                    <span class="masked">••••••••••••••••••••••••</span>
                    <span class="revealed"
                        style="display: none; color: var(--text-primary);"><?php echo htmlspecialchars($decrypted_key); ?></span>
                </div>
                <div style="display: flex; gap: 8px;">
                    <button class="btn btn-primary" style="width: auto; margin: 0; padding: 10px 16px;"
                        onclick="toggleReveal(this)">Show</button>
                    <button class="btn btn-cancel" style="width: auto; margin: 0; padding: 10px 16px;"
                        onclick="copyText('<?php echo htmlspecialchars($decrypted_key); ?>')">Copy</button>
                </div>
            </div>
        </div>
    <?php endforeach; ?>
</div>

<script>
    function toggleReveal(btn) {
        var container = btn.previousElementSibling;
        var masked = container.querySelector('.masked');
        var revealed = container.querySelector('.revealed');

        if (masked.style.display !== 'none') {
            masked.style.display = 'none';
            revealed.style.display = 'inline';
            btn.innerText = 'Hide';
        } else {
            masked.style.display = 'inline';
            revealed.style.display = 'none';
            btn.innerText = 'Show';
        }
    }

    function copyText(text) {
        navigator.clipboard.writeText(text).then(function () {
            alert("Copied!");
        });
    }
</script>

<?php require_once "includes/footer.php"; ?>