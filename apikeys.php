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
<div class="section-card" style="margin-bottom: 2.5rem; padding: 2rem;">
    <div class="section-header" style="margin-bottom: 2rem; padding: 0;">
        <span class="section-title">Add New API Key</span>
        <p style="color: var(--text-dim); font-size: 0.85rem; margin-top: 4px;">Store your keys securely with AES-256
            encryption.</p>
    </div>

    <form method="post" action="">
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
                <span
                    style="position: absolute; left: 1.25rem; top: 50%; transform: translateY(-50%); color: var(--text-dim); font-size: 1.2rem;">🔑</span>
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
                    <?php $decrypted_key = decryptData($key['api_key_enc']); ?>
                    <tr>
                        <td>
                            <div class="service-cell">
                                <div class="service-icon"><?php echo strtoupper(substr($key['service_name'], 0, 1)); ?></div>
                                <div class="app-name"><?php echo htmlspecialchars($key['service_name']); ?></div>
                            </div>
                        </td>
                        <td>
                            <?php 
                            $env_class = 'strong'; // default
                            $envv = strtolower($key['environment']);
                            if($envv == 'development') $env_class = 'strong'; // Purple/Primary
                            if($envv == 'staging') $env_class = 'strong'; // Green
                            if($envv == 'production') $env_class = 'strong'; // Warning/Red
                            ?>
                            <span class="strength-pill <?php echo $env_class; ?>" style="background: <?php 
                                if($envv == 'development') echo 'rgba(44, 15, 189, 0.15)';
                                elseif($envv == 'staging') echo 'rgba(0, 184, 148, 0.15)';
                                elseif($envv == 'production') echo 'rgba(255, 159, 67, 0.15)';
                            ?>; color: <?php 
                                if($envv == 'development') echo 'var(--accent-primary)';
                                elseif($envv == 'staging') echo 'var(--green-sec)';
                                elseif($envv == 'production') echo '#ff9f43';
                            ?>; border: none; font-weight: 800; text-transform: uppercase; font-size: 0.7rem; padding: 4px 10px;">
                                <?php echo htmlspecialchars($key['environment']); ?>
                            </span>
                        </td>
                        <td>
                            <div class="key-mask" style="display: flex; align-items: center; gap: 8px;">
                                <span class="masked" style="color: var(--text-dim); letter-spacing: 2px;">••••••••••••••••</span>
                                <span class="revealed" style="display: none; color: var(--text-secondary); font-family: monospace;"><?php echo htmlspecialchars($decrypted_key); ?></span>
                            </div>
                        </td>
                        <td>
                            <div style="display: flex; gap: 12px; color: var(--text-dim); font-size: 1.1rem;">
                                <span class="icon-btn" onclick="toggleReveal(this)" title="Toggle Visibility">👁️</span>
                                <span class="icon-btn" onclick="copyText('<?php echo htmlspecialchars($decrypted_key); ?>')" title="Copy Key">📋</span>
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