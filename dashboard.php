<?php
require_once "includes/auth_session.php";
require_once "config/db.php";
require_once "includes/functions.php";

$user_id = $_SESSION["id"];

// Get Counts
$stmt = $pdo->prepare("SELECT COUNT(*) FROM vault_items WHERE user_id = :id");
$stmt->execute([':id' => $user_id]);
$password_count = $stmt->fetchColumn();

$stmt = $pdo->prepare("SELECT COUNT(*) FROM api_keys WHERE user_id = :id");
$stmt->execute([':id' => $user_id]);
$apikey_count = $stmt->fetchColumn();

$stmt = $pdo->prepare("SELECT COUNT(*) FROM secure_notes WHERE user_id = :id");
$stmt->execute([':id' => $user_id]);
$note_count = $stmt->fetchColumn();

// Get Recent Activity (Latest 3 Passwords)
$stmt = $pdo->prepare("SELECT app_name, created_at FROM vault_items WHERE user_id = :id ORDER BY created_at DESC LIMIT 3");
$stmt->execute([':id' => $user_id]);
$recent_items = $stmt->fetchAll(PDO::FETCH_ASSOC);

require_once "includes/header.php";
?>

<h1>Dashboard Overview</h1>
<p>Welcome back! Your vault is encrypted and secure.</p>

<div class="stat-grid">
    <div class="card">
        <h3>Total Passwords</h3>
        <div class="stat-value">
            <?php echo $password_count; ?>
        </div>
    </div>
    <div class="card">
        <h3>API Keys</h3>
        <div class="stat-value">
            <?php echo $apikey_count; ?>
        </div>
    </div>
    <div class="card">
        <h3>Secure Notes</h3>
        <div class="stat-value">
            <?php echo $note_count; ?>
        </div>
    </div>
</div>

<div class="card">
    <h3>Recent Activity</h3>
    <?php if (count($recent_items) > 0): ?>
        <?php foreach ($recent_items as $item): ?>
            <div
                style="display: flex; align-items: center; justify-content: space-between; padding: 0.8rem 0; border-bottom: 1px solid #333;">
                <div>Added <strong>
                        <?php echo htmlspecialchars($item['app_name']); ?>
                    </strong></div>
                <div style="color: #888; font-size: 0.9rem;">
                    <?php echo formatDate($item['created_at']); ?>
                </div>
            </div>
        <?php endforeach; ?>
    <?php else: ?>
        <p style="color: #888; font-style: italic;">No activity yet.</p>
    <?php endif; ?>
</div>

<?php require_once "includes/footer.php"; ?>