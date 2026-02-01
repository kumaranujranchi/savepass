<?php
session_start();
require_once "config/db.php";
require_once "includes/functions.php";

// If user is already logged in OR not in OTP state, redirect
if (!isset($_SESSION["pending_otp_userid"])) {
    header("location: login.php");
    exit;
}

$error = "";
$success_msg = "";
$user_id = $_SESSION["pending_otp_userid"];

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST["resend"])) {
        // Generate NEW OTP
        $otp = sprintf("%06d", mt_rand(100000, 999999));
        $expires = date("Y-m-d H:i:s", strtotime("+10 minutes"));

        $stmt_otp = $pdo->prepare("INSERT INTO user_otps (user_id, otp_code, expires_at) VALUES (:uid, :otp, :expires)");
        $stmt_otp->execute([':uid' => $user_id, ':otp' => $otp, ':expires' => $expires]);

        require_once "config/db.php";
        require_once "includes/mailer.php";
        if (Mailer::sendOTP($_SESSION["pending_otp_email"], $otp, $_SERVER['REMOTE_ADDR'], $_SERVER['HTTP_USER_AGENT'])) {
            $success_msg = "A new verification code has been sent.";
        } else {
            $error = "Failed to resend code.";
        }
    } elseif (isset($_POST["cancel"])) {
        unset($_SESSION["pending_otp_userid"]);
        unset($_SESSION["pending_otp_email"]);
        header("location: login.php");
        exit;
    } else {
        $entered_otp = trim($_POST["otp"]);

        // Validate OTP
        $stmt = $pdo->prepare("SELECT id FROM user_otps WHERE user_id = :user_id AND otp_code = :otp AND expires_at > NOW() AND is_used = 0 ORDER BY created_at DESC LIMIT 1");
        $stmt->execute([':user_id' => $user_id, ':otp' => $entered_otp]);
        $otp_record = $stmt->fetch();

        if ($otp_record) {
            // Mark OTP as used
            $pdo->prepare("UPDATE user_otps SET is_used = 1 WHERE id = :id")->execute([':id' => $otp_record['id']]);

            // Trust this device
            $device_token = bin2hex(random_bytes(32));
            $browser_info = $_SERVER['HTTP_USER_AGENT'];
            $last_ip = $_SERVER['REMOTE_ADDR'];

            $stmt = $pdo->prepare("INSERT INTO user_trusted_devices (user_id, device_token, browser_info, last_ip) VALUES (:uid, :token, :browser, :ip)");
            $stmt->execute([':uid' => $user_id, ':token' => $device_token, ':browser' => $browser_info, ':ip' => $last_ip]);

            // Set trusted device cookie (valid for 30 days)
            setcookie('securevault_device', $device_token, time() + (86400 * 30), "/", "", true, true);

            // Complete the login
            $_SESSION["loggedin"] = true;
            $_SESSION["id"] = $user_id;
            $_SESSION["email"] = $_SESSION["pending_otp_email"];

            // Clean up session
            unset($_SESSION["pending_otp_userid"]);
            unset($_SESSION["pending_otp_email"]);

            header("location: dashboard.php");
            exit;
        } else {
            $error = "Invalid or expired verification code.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verify Login - SecureVault</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <script src="https://unpkg.com/lucide@latest"></script>
    <style>
        .otp-input-group {
            display: flex;
            gap: 10px;
            justify-content: center;
            margin: 2rem 0;
        }

        .otp-box {
            width: 45px;
            height: 55px;
            text-align: center;
            font-size: 1.5rem;
            font-weight: 800;
            border-radius: 12px;
            background: rgba(255, 255, 255, 0.05);
            border: 1px solid var(--border-color);
            color: white;
        }
    </style>
</head>

<body class="login-body">
    <div class="auth-card">
        <div style="margin-bottom: 2rem;">
            <a href="login.php"
                style="display: inline-flex; align-items: center; gap: 8px; font-size: 0.8rem; color: var(--text-dim); text-decoration: none;">
                ← Back to Login
            </a>
        </div>
        <div class="auth-logo">SecureVault</div>
        <h2>Verification Required</h2>
        <p class="subtitle">We've sent a 6-digit code to your email.<br>It's required to recognize this browser.</p>

        <?php if (!empty($success_msg)): ?>
            <div
                style="background: rgba(0, 230, 118, 0.1); border: 1px solid var(--green-sec); color: var(--green-sec); padding: 10px; border-radius: 8px; margin-bottom: 20px; font-size: 0.9rem;">
                <?php echo $success_msg; ?>
            </div>
        <?php endif; ?>

        <form method="post" class="auth-form" style="margin-top: 2rem;">
            <div class="form-group" style="text-align: center;">
                <label style="text-align: center; display: block;">Enter Code</label>
                <input type="text" name="otp" maxlength="6" placeholder="000000" required autocomplete="one-time-code"
                    style="text-align: center; font-size: 1.5rem; letter-spacing: 12px; font-weight: 800; height: 60px;">
                <?php if (!empty($error)): ?>
                    <span class="text-danger" style="margin-top: 1rem; display: block;">
                        <?php echo $error; ?>
                    </span>
                <?php endif; ?>
            </div>

            <button type="submit" class="btn-nav"
                style="width: 100%; border: none; padding: 1rem; cursor: pointer; margin-top: 1rem;">
                Verify & Continue
            </button>

            <button type="submit" name="cancel" formaction=""
                style="width: 100%; border: 1px solid var(--border-color); background: transparent; color: var(--text-dim); padding: 0.8rem; border-radius: 12px; cursor: pointer; margin-top: 1rem; font-weight: 600;">
                Cancel Login
            </button>
        </form>

        <p style="margin-top: 2rem; font-size: 0.8rem; color: var(--text-dim);">
            Didn't receive code?
        <form method="post" style="display: inline;">
            <button type="submit" name="resend"
                style="background: none; border: none; color: var(--accent-primary); cursor: pointer; font-weight: 700; font-size: 0.8rem; padding: 0;">
                Resend Code
            </button>
        </form>
        </p>
    </div>
    <script>lucide.createIcons();</script>
</body>

</html>