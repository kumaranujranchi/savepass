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

<h1 class="page-title">Dashboard Overview</h1>
<p class="page-subtitle">Welcome back! Your vault is encrypted and secure.</p>

<div class="dashboard-grid">
    <!-- Total Passwords -->
    <div class="dashboard-card">
        <div class="card-header">
            <span class="card-title">Total Passwords</span>
            <span style="color: var(--text-dim);">•••</span>
        </div>
        <div class="card-body">
            <span class="card-value"><?php echo $password_count; ?></span>
            <span class="card-trend trend-up">+5%</span>
        </div>
        <div class="progress-bar">
            <div class="progress-fill" style="width: 65%; background: var(--accent-primary);"></div>
        </div>
        <a href="import_passwords.php"
            style="display: block; margin-top: 15px; font-size: 0.75rem; color: var(--accent-secondary); font-weight: 700;">+
            Sync Browser Passwords</a>
    </div>

    <!-- API Keys -->
    <div class="dashboard-card">
        <div class="card-header">
            <span class="card-title">API Keys</span>
            <span style="color: var(--text-dim);">🛡️</span>
        </div>
        <div class="card-body">
            <span class="card-value"><?php echo $apikey_count; ?></span>
            <span class="card-trend trend-up">+2%</span>
        </div>
        <div class="progress-bar">
            <div class="progress-fill" style="width: 40%; background: var(--green-sec);"></div>
        </div>
    </div>

    <!-- Secure Notes -->
    <div class="dashboard-card">
        <div class="card-header">
            <span class="card-title">Secure Notes</span>
            <span style="color: var(--text-dim);">📝</span>
        </div>
        <div class="card-body">
            <span class="card-value"><?php echo $note_count; ?></span>
            <span class="card-trend trend-down">-1%</span>
        </div>
        <div class="progress-bar">
            <div class="progress-fill" style="width: 30%; background: var(--purple-sec);"></div>
        </div>
    </div>

    <!-- Last Accessed -->
    <div class="dashboard-card">
        <div class="card-header">
            <span class="card-title">Last Accessed</span>
            <span style="color: var(--text-dim);">🕒</span>
        </div>
        <div>
            <div style="font-weight: 800; font-size: 1.1rem; margin-bottom: 4px;">
                <?php echo !empty($recent_items) ? htmlspecialchars($recent_items[0]['app_name']) : 'N/A'; ?>
            </div>
            <div style="font-size: 0.8rem; color: var(--text-secondary); margin-bottom: 12px;">Just now</div>
            <div
                style="display: flex; align-items: center; gap: 6px; font-size: 0.7rem; font-weight: 700; color: var(--green-sec); text-transform: uppercase;">
                <span style="width: 6px; height: 6px; background: var(--green-sec); border-radius: 50%;"></span>
                Encrypted Link Active
            </div>
        </div>
    </div>
</div>

<div class="section-card">
    <div class="section-header">
        <span class="section-title">Recent Activity</span>
        <a href="passwords.php" class="view-all">View All</a>
    </div>
    <div style="overflow-x: auto;">
        <table class="activity-table">
            <thead>
                <tr>
                    <th>Service</th>
                    <th>Username</th>
                    <th>Strength</th>
                    <th>Last Modified</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php if (count($recent_items) > 0): ?>
                    <?php foreach ($recent_items as $item): ?>
                        <tr>
                            <td>
                                <div class="service-cell">
                                    <div class="service-icon"><?php echo strtoupper(substr($item['app_name'], 0, 1)); ?></div>
                                    <?php echo htmlspecialchars($item['app_name']); ?>
                                </div>
                            </td>
                            <td style="color: var(--text-secondary);">
                                <?php echo htmlspecialchars($item['username'] ?? 'user_name'); ?>
                            </td>
                            <td>
                                <span class="strength-pill strong">
                                    <span style="display: flex; gap: 2px;">
                                        <span
                                            style="width: 4px; height: 10px; background: currentColor; border-radius: 1px;"></span>
                                        <span
                                            style="width: 4px; height: 10px; background: currentColor; border-radius: 1px;"></span>
                                        <span
                                            style="width: 4px; height: 10px; background: currentColor; border-radius: 1px;"></span>
                                        <span
                                            style="width: 4px; height: 10px; background: currentColor; border-radius: 1px; opacity: 0.3;"></span>
                                    </span>
                                    Strong
                                </span>
                            </td>
                            <td style="color: var(--text-dim);"><?php echo formatDate($item['created_at']); ?></td>
                            <td>
                                <div style="display: flex; gap: 12px; color: var(--text-dim); font-size: 1.1rem;">
                                    <span style="cursor: pointer;">📋</span>
                                    <span style="cursor: pointer;">👁️</span>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="5" style="text-align: center; padding: 3rem; color: var(--text-dim);">
                            No recent activity found in your vault.
                        </td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<a href="add_password.php" class="fab">+</a>

<?php require_once "includes/footer.php"; ?>