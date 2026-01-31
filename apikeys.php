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

<h1>API Key Manager</h1>

<!-- Simple Add Form -->
<div class="card">
    <h3>Add New API Key</h3>
    <form method="post" action="">
        <div style="display: flex; gap: 1rem;">
            <input type="text" name="service_name" placeholder="Service Name (e.g. AWS)" required style="flex: 1;">
            <select name="environment" style="flex: 1;">
                <option value="Development">Development</option>
                <option value="Staging">Staging</option>
                <option value="Production">Production</option>
            </select>
        </div>
        <input type="text" name="api_key" placeholder="API Key Secret" required>
        <button type="submit" name="add_key" class="btn btn-primary">Save Key</button>
    </form>
</div>

<div class="key-list">
    <?php foreach ($keys as $key): ?>
        <?php $decrypted_key = decryptData($key['api_key_enc']); ?>
        <div class="card" style="display: flex; justify-content: space-between; align-items: center; padding: 1rem;">
            <div>
                <div style="font-weight: bold; font-size: 1.1rem;">
                    <?php echo htmlspecialchars($key['service_name']); ?>
                </div>
                <div style="font-size: 0.8rem; color: #888; margin-top: 5px;">Environment: <span style="color: #ccc;">
                        <?php echo htmlspecialchars($key['environment']); ?>
                    </span></div>
            </div>
            <div style="display: flex; align-items: center; gap: 1rem;">
                <div class="key-mask"
                    style="font-family: monospace; background: #333; padding: 0.4rem; border-radius: 4px; font-size: 0.9rem;">
                    <span class="masked">••••••••••••••••••••••••</span>
                    <span class="revealed" style="display: none;">
                        <?php echo htmlspecialchars($decrypted_key); ?>
                    </span>
                </div>
                <button class="btn btn-cancel" onclick="toggleReveal(this)">Show</button>
                <button class="btn btn-primary"
                    onclick="copyText('<?php echo htmlspecialchars($decrypted_key); ?>')">Copy</button>
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