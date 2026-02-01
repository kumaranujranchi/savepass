<?php
require_once "includes/auth_session.php";
require_once "config/db.php";
require_once "includes/functions.php";

$user_id = $_SESSION["id"];
$msg = "";
$error = "";

// Fetch current user data
$sql = "SELECT email, created_at FROM users WHERE id = :id";
$stmt = $pdo->prepare($sql);
$stmt->bindParam(":id", $user_id, PDO::PARAM_INT);
$stmt->execute();
$user_data = $stmt->fetch(PDO::FETCH_ASSOC);
$current_email = $user_data['email'];
$member_since = $user_data['created_at'];

// Handle profile update
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['update_profile'])) {
    $new_email = trim($_POST['email']);

    // Validate email
    if (empty($new_email)) {
        $error = "Email cannot be empty.";
    } elseif (!filter_var($new_email, FILTER_VALIDATE_EMAIL)) {
        $error = "Invalid email format.";
    } elseif ($new_email === $current_email) {
        $error = "New email is the same as current email.";
    } else {
        // Check if email already exists
        $check_sql = "SELECT id FROM users WHERE email = :email AND id != :id";
        $check_stmt = $pdo->prepare($check_sql);
        $check_stmt->bindParam(":email", $new_email, PDO::PARAM_STR);
        $check_stmt->bindParam(":id", $user_id, PDO::PARAM_INT);
        $check_stmt->execute();

        if ($check_stmt->rowCount() > 0) {
            $error = "This email is already registered.";
        } else {
            // Update email
            $update_sql = "UPDATE users SET email = :email WHERE id = :id";
            $update_stmt = $pdo->prepare($update_sql);
            $update_stmt->bindParam(":email", $new_email, PDO::PARAM_STR);
            $update_stmt->bindParam(":id", $user_id, PDO::PARAM_INT);

            if ($update_stmt->execute()) {
                $_SESSION['email'] = $new_email;
                $current_email = $new_email;
                $msg = "Profile updated successfully!";
            } else {
                $error = "Failed to update profile. Please try again.";
            }
        }
    }
}

// Handle password change
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
<p class="page-subtitle">Manage your profile, security preferences and connected devices.</p>

<?php if ($msg): ?>
    <div
        style="background: rgba(0, 230, 118, 0.1); border: 1px solid var(--green-sec); color: var(--green-sec); padding: 1rem; border-radius: 12px; margin-bottom: 2rem; font-weight: 600;">
        ✓ <?php echo $msg; ?>
    </div>
<?php endif; ?>

<?php if ($error): ?>
    <div
        style="background: rgba(244, 67, 54, 0.1); border: 1px solid #f44336; color: #f44336; padding: 1rem; border-radius: 12px; margin-bottom: 2rem; font-weight: 600;">
        ⚠ <?php echo $error; ?>
    </div>
<?php endif; ?>

<!-- Profile Information Section -->
<div class="section-card" style="margin-bottom: 2rem;">
    <div class="section-header">
        <span class="section-title">Profile Information</span>
    </div>
    <form method="post" style="padding: 1.5rem;">
        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 2rem; margin-bottom: 2rem;">
            <!-- Email Field -->
            <div>
                <label
                    style="display: block; font-size: 0.75rem; color: var(--text-dim); text-transform: uppercase; font-weight: 800; margin-bottom: 8px; letter-spacing: 0.5px;">Email
                    Address</label>
                <input type="email" name="email" value="<?php echo htmlspecialchars($current_email); ?>" required
                    style="margin-bottom: 0; background: #1a1c26; border: 1px solid var(--border-color); border-radius: 8px; height: 45px; width: 100%; padding: 0 1rem; color: var(--text-primary);">
            </div>

            <!-- Member Since (Read-only) -->
            <div>
                <label
                    style="display: block; font-size: 0.75rem; color: var(--text-dim); text-transform: uppercase; font-weight: 800; margin-bottom: 8px; letter-spacing: 0.5px;">Member
                    Since</label>
                <input type="text" value="<?php echo date('F j, Y', strtotime($member_since)); ?>" readonly
                    style="margin-bottom: 0; background: rgba(255,255,255,0.03); border: 1px solid var(--border-color); border-radius: 8px; height: 45px; width: 100%; padding: 0 1rem; color: var(--text-secondary); cursor: not-allowed;">
            </div>
        </div>

        <!-- Account Stats -->
        <div
            style="display: grid; grid-template-columns: repeat(3, 1fr); gap: 1rem; margin-bottom: 1.5rem; padding: 1.5rem; background: rgba(255,255,255,0.02); border-radius: 10px; border: 1px solid var(--border-color);">
            <div style="text-align: center;">
                <div style="font-size: 1.8rem; font-weight: 800; color: var(--accent-primary);">
                    <?php
                    $count_sql = "SELECT COUNT(*) as count FROM vault_items WHERE user_id = :id";
                    $count_stmt = $pdo->prepare($count_sql);
                    $count_stmt->bindParam(":id", $user_id, PDO::PARAM_INT);
                    $count_stmt->execute();
                    echo $count_stmt->fetch(PDO::FETCH_ASSOC)['count'];
                    ?>
                </div>
                <div style="font-size: 0.75rem; color: var(--text-dim); text-transform: uppercase; margin-top: 4px;">
                    Passwords</div>
            </div>
            <div style="text-align: center;">
                <div style="font-size: 1.8rem; font-weight: 800; color: var(--green-sec);">
                    <?php
                    $notes_sql = "SELECT COUNT(*) as count FROM secure_notes WHERE user_id = :id";
                    $notes_stmt = $pdo->prepare($notes_sql);
                    $notes_stmt->bindParam(":id", $user_id, PDO::PARAM_INT);
                    $notes_stmt->execute();
                    echo $notes_stmt->fetch(PDO::FETCH_ASSOC)['count'];
                    ?>
                </div>
                <div style="font-size: 0.75rem; color: var(--text-dim); text-transform: uppercase; margin-top: 4px;">
                    Notes</div>
            </div>
            <div style="text-align: center;">
                <div style="font-size: 1.8rem; font-weight: 800; color: var(--blue-sec);">
                    <?php
                    $api_sql = "SELECT COUNT(*) as count FROM api_keys WHERE user_id = :id";
                    $api_stmt = $pdo->prepare($api_sql);
                    $api_stmt->bindParam(":id", $user_id, PDO::PARAM_INT);
                    $api_stmt->execute();
                    echo $api_stmt->fetch(PDO::FETCH_ASSOC)['count'];
                    ?>
                </div>
                <div style="font-size: 0.75rem; color: var(--text-dim); text-transform: uppercase; margin-top: 4px;">API
                    Keys</div>
            </div>
        </div>

        <button type="submit" name="update_profile" class="btn btn-primary"
            style="width: auto; padding: 0.8rem 2.5rem; background: var(--accent-primary);">
            <i data-lucide="save" style="width: 16px; height: 16px; margin-right: 6px;"></i>
            Save Changes
        </button>
    </form>
</div>

<div class="section-card" style="margin-bottom: 2rem;">
    <div class="section-header">
        <span class="section-title">Security Settings</span>
    </div>

    <!-- Two Factor Placeholder (existing) -->
    <div
        style="padding: 1.5rem; border-bottom: 1px solid var(--border-color); display: flex; justify-content: space-between; align-items: center;">
        <div>
            <div class="card-header">
                <h3 style="margin: 0; font-size: 1.1rem; display: flex; align-items: center; gap: 10px;">
                    <i data-lucide="shield-check" style="width: 20px; height: 20px; color: var(--accent-primary);"></i>
                    Two-Factor Authentication
                </h3>
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

    <!-- Auto-Lock Timeout -->
    <div
        style="padding: 1.5rem; border-bottom: 1px solid var(--border-color); display: flex; justify-content: space-between; align-items: center;">
        <div>
            <div style="font-weight: 700; margin-bottom: 4px; color: var(--text-primary);">Auto-Lock Vault</div>
            <div style="font-size: 0.8rem; color: var(--text-dim);">Automatically lock your vault after inactivity.
            </div>
        </div>
        <select id="autoLockSettings" onchange="updateSecuritySetting('autoLockTimeout', this.value)"
            style="background: #1a1c26; border: 1px solid var(--border-color); color: var(--text-primary); padding: 8px; border-radius: 8px; font-weight: 600;">
            <option value="1">1 Minute</option>
            <option value="5">5 Minutes</option>
            <option value="15">15 Minutes</option>
            <option value="30">30 Minutes</option>
            <option value="60">1 Hour</option>
            <option value="0">Never</option>
        </select>
    </div>

    <!-- Lock on Close -->
    <div
        style="padding: 1.5rem; border-bottom: 1px solid var(--border-color); display: flex; justify-content: space-between; align-items: center;">
        <div>
            <div style="font-weight: 700; margin-bottom: 4px; color: var(--text-primary);">Lock on Browser Close</div>
            <div style="font-size: 0.8rem; color: var(--text-dim);">Lock vault immediately when browser is closed.</div>
        </div>
        <label class="switch" style="position: relative; display: inline-block; width: 44px; height: 24px;">
            <input type="checkbox" id="lockOnCloseSettings"
                onchange="updateSecuritySetting('lockOnClose', this.checked)" style="opacity: 0; width: 0; height: 0;">
            <span class="slider"
                style="position: absolute; cursor: pointer; top: 0; left: 0; right: 0; bottom: 0; background-color: #333; transition: .4s; border-radius: 24px;"></span>
        </label>
    </div>

    <!-- Clipboard Clear delay -->
    <div style="padding: 1.5rem; display: flex; justify-content: space-between; align-items: center;">
        <div>
            <div style="font-weight: 700; margin-bottom: 4px; color: var(--text-primary);">Clipboard Protection</div>
            <div style="font-size: 0.8rem; color: var(--text-dim);">Automatically clear copied data from clipboard.
            </div>
        </div>
        <select id="clipboardSettings" onchange="updateSecuritySetting('clipboardClearDelay', this.value)"
            style="background: #1a1c26; border: 1px solid var(--border-color); color: var(--text-primary); padding: 8px; border-radius: 8px; font-weight: 600;">
            <option value="10">10 Seconds</option>
            <option value="30">30 Seconds</option>
            <option value="60">1 Minute</option>
            <option value="0">Never</option>
        </select>
    </div>
</div>

<style>
    .switch input:checked+.slider {
        background-color: var(--accent-primary);
    }

    .slider:before {
        position: absolute;
        content: "";
        height: 18px;
        width: 18px;
        left: 3px;
        bottom: 3px;
        background-color: white;
        transition: .4s;
        border-radius: 50%;
    }

    .switch input:checked+.slider:before {
        transform: translateX(20px);
    }
</style>

<script>
    function updateSecuritySetting(key, value) {
        if (typeof SecurityManager !== 'undefined') {
            const updates = {};
            updates[key] = (typeof value === 'string' && !isNaN(value)) ? parseInt(value) : value;
            SecurityManager.saveConfig(updates);
            showToast('Security settings updated');
        }
    }

    document.addEventListener('DOMContentLoaded', () => {
        if (typeof SecurityManager !== 'undefined') {
            const config = SecurityManager.config;
            document.getElementById('autoLockSettings').value = config.autoLockTimeout;
            document.getElementById('lockOnCloseSettings').checked = config.lockOnClose;
            document.getElementById('clipboardSettings').value = config.clipboardClearDelay;
        }
    });
</script>

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