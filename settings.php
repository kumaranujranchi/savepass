<?php
require_once "includes/auth_session.php";
require_once "config/db.php";
require_once "includes/functions.php";

$user_id = $_SESSION["id"];
$msg = "";

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['change_password'])) {
    $new_password = $_POST['new_password'];
    $confirm_password = $_POST['confirm_password'];

    if ($new_password === $confirm_password) {
        $param_password = password_hash($new_password, PASSWORD_DEFAULT);
        $sql = "UPDATE users SET password_hash = :password WHERE id = :id";

        if ($stmt = $pdo->prepare($sql)) {
            $stmt->bindParam(":password", $param_password, PDO::PARAM_STR);
            $stmt->bindParam(":id", $user_id, PDO::PARAM_INT);
            if ($stmt->execute()) {
                $msg = "Password updated successfully.";
            } else {
                $msg = "Something went wrong.";
            }
        }
    } else {
        $msg = "Passwords do not match.";
    }
}

require_once "includes/header.php";
?>

<h1 class="page-title">Account Settings</h1>
<p class="page-subtitle">Manage your security preferences and connected devices.</p>

<?php if ($msg): ?>
    <div
        style="background: rgba(44, 15, 189, 0.1); border: 1px solid var(--accent-primary); color: white; padding: 1rem; border-radius: 12px; margin-bottom: 2rem; font-weight: 600;">
        <?php echo $msg; ?>
    </div>
<?php endif; ?>

<div class="section-card" style="margin-bottom: 2rem;">
    <div class="section-header">
        <span class="section-title">Security Settings</span>
    </div>
    <div
        style="padding: 1.5rem; border-bottom: 1px solid var(--border-color); display: flex; justify-content: space-between; align-items: center;">
        <div>
            <div style="font-weight: 700; margin-bottom: 4px; color: var(--text-primary);">Two-Factor Authentication
            </div>
            <div style="font-size: 0.8rem; color: var(--text-dim);">Add an extra layer of security to your account.
            </div>
        </div>
        <div
            style="width: 44px; height: 24px; background: #333; border-radius: 12px; position: relative; cursor: not-allowed; opacity: 0.5;">
            <div
                style="width: 20px; height: 20px; background: #888; border-radius: 50%; position: absolute; top: 2px; left: 2px;">
            </div>
        </div>
    </div>
    <div style="padding: 1.5rem; display: flex; justify-content: space-between; align-items: center;">
        <div>
            <div style="font-weight: 700; margin-bottom: 4px; color: var(--text-primary);">Auto-Lock Timeout</div>
            <div style="font-size: 0.8rem; color: var(--text-dim);">Automatically lock your vault after inactivity.
            </div>
        </div>
        <div style="font-weight: 700; color: var(--accent-secondary); cursor: pointer;">5 Minutes ›</div>
    </div>
</div>

<div class="section-card" style="margin-bottom: 2rem;">
    <div class="section-header">
        <span class="section-title">Update Master Password</span>
    </div>
    <form method="post" style="padding: 1.5rem;">
        <div style="display: flex; gap: 1.5rem; margin-bottom: 1.5rem; flex-wrap: wrap;">
            <div style="flex: 1; min-width: 250px;">
                <label
                    style="display: block; font-size: 0.75rem; color: var(--text-dim); text-transform: uppercase; font-weight: 800; margin-bottom: 8px; letter-spacing: 0.5px;">New
                    Master Password</label>
                <input type="password" name="new_password" placeholder="••••••••" required
                    style="margin-bottom: 0; background: #1a1c26; border: 1px solid var(--border-color); border-radius: 8px; height: 45px; width: 100%;">
            </div>
            <div style="flex: 1; min-width: 250px;">
                <label
                    style="display: block; font-size: 0.75rem; color: var(--text-dim); text-transform: uppercase; font-weight: 800; margin-bottom: 8px; letter-spacing: 0.5px;">Confirm
                    New Password</label>
                <input type="password" name="confirm_password" placeholder="••••••••" required
                    style="margin-bottom: 0; background: #1a1c26; border: 1px solid var(--border-color); border-radius: 8px; height: 45px; width: 100%;">
            </div>
        </div>
        <button type="submit" name="change_password" class="btn btn-primary"
            style="width: auto; padding: 0.8rem 2.5rem; background: var(--accent-primary);">Update Password</button>
    </form>
</div>

<div class="section-card">
    <div class="section-header">
        <span class="section-title">Connected Devices</span>
    </div>
    <div style="padding: 1.5rem;">
        <div <div
            style="display: flex; align-items: center; gap: 1.25rem; padding: 1.25rem; background: var(--bg-card); border-radius: 12px; border: 1px solid var(--border-color);">
            <div
                style="font-size: 1.5rem; width: 48px; height: 48px; background: rgba(255,255,255,0.03); border-radius: 50%; display: flex; align-items: center; justify-content: center;">
                💻</div>
            <div style="flex: 1;">
                <div style="font-weight: 700; color: var(--text-primary);">Current Session <span
                        class="strength-pill strong"
                        style="padding: 2px 8px; font-size: 0.6rem; margin-left: 8px;">Active</span></div>
                <div style="font-size: 0.75rem; color: var(--text-dim);">IP: <?php echo $_SERVER['REMOTE_ADDR']; ?> •
                    Ranchi, India</div>
            </div>
            <div style="color: var(--text-dim); font-size: 0.8rem; font-weight: 600;">This Device</div>
        </div>
    </div>
</div>

<?php require_once "includes/footer.php"; ?>