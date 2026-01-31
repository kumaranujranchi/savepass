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

<h1>Security Settings</h1>

<?php if ($msg): ?>
    <div style="background: #2c0fbd; color: white; padding: 1rem; border-radius: 6px; margin-bottom: 1rem;">
        <?php echo $msg; ?>
    </div>
<?php endif; ?>

<div class="card">
    <div
        style="display: flex; justify-content: space-between; align-items: center; padding-bottom: 1rem; border-bottom: 1px solid #333;">
        <div>
            <div style="font-size: 1rem;">Two-Factor Authentication</div>
        </div>
        <div
            style="width: 44px; height: 24px; background: #333; border-radius: 12px; position: relative; cursor: not-allowed;">
            <div
                style="width: 20px; height: 20px; background: white; border-radius: 50%; position: absolute; top: 2px; left: 2px;">
            </div>
        </div>
    </div>
    <div style="display: flex; justify-content: space-between; align-items: center; padding-top: 1rem;">
        <div>
            <div style="font-size: 1rem;">Auto-Lock Timeout</div>
        </div>
        <div style="color: #888;">5 Minutes ›</div>
    </div>
</div>

<h3>Login Credentials</h3>
<div class="card">
    <div style="margin-bottom: 1rem;">
        <div style="font-size: 1rem;">Master Password</div>
        <div style="font-size: 0.8rem; color: #888;">Last changed recently</div>
    </div>

    <form method="post">
        <input type="password" name="new_password" placeholder="New Master Password" required>
        <input type="password" name="confirm_password" placeholder="Confirm New Password" required>
        <button type="submit" name="change_password" class="btn btn-primary">Update Password</button>
    </form>
</div>

<h3>Connected Devices</h3>
<div class="card">
    <div style="display: flex; align-items: center; padding-bottom: 1rem; border-bottom: 1px solid #333;">
        <div
            style="width: 32px; height: 32px; background: #333; border-radius: 50%; margin-right: 1rem; display: flex; justify-content: center; align-items: center;">
            💻</div>
        <div>
            <div style="font-weight: bold;">Current Session</div>
            <div style="font-size: 0.8rem; color: #888;">IP:
                <?php echo $_SERVER['REMOTE_ADDR']; ?>
            </div>
        </div>
    </div>
</div>

<?php require_once "includes/footer.php"; ?>