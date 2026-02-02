<?php
session_start();

if (isset($_SESSION["loggedin"]) && $_SESSION["loggedin"] === true) {
    header("location: dashboard.php");
    exit;
}

require_once "config/db.php";
require_once "includes/functions.php";

$email = $password = "";
$email_err = $password_err = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (empty(trim($_POST["email"]))) {
        $email_err = "Please enter email.";
    } else {
        $email = trim($_POST["email"]);
    }

    if (empty(trim($_POST["password"]))) {
        $password_err = "Please enter your password.";
    } else {
        $password = trim($_POST["password"]);
    }

    if (empty($email_err) && empty($password_err)) {
        $sql = "SELECT id, email, password_hash FROM users WHERE email = :email";

        if ($stmt = $pdo->prepare($sql)) {
            $stmt->bindParam(":email", $param_email, PDO::PARAM_STR);
            $param_email = $email;

            if ($stmt->execute()) {
                if ($stmt->rowCount() == 1) {
                    if ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                        $id = $row["id"];
                        $email = $row["email"];
                        $hashed_password = $row["password_hash"];

                        // Zero-Knowledge: Both stored and submitted are authHash
                        // Direct comparison instead of password_verify()
                        if ($password === $hashed_password) {
                            // --- NEW BROWSER CHECK ---
                            $is_trusted = false;

                            if (!empty($_COOKIE['securevault_device'])) {
                                $device_token = $_COOKIE['securevault_device'];
                                $stmt_device = $pdo->prepare("SELECT id FROM user_trusted_devices WHERE user_id = :uid AND device_token = :token");
                                $stmt_device->execute([':uid' => $id, ':token' => $device_token]);
                                if ($stmt_device->fetch()) {
                                    $is_trusted = true;
                                }
                            }

                            if (!$is_trusted) {
                                // Generate OTP
                                $otp = sprintf("%06d", mt_rand(100000, 999999));
                                $expires = date("Y-m-d H:i:s", strtotime("+10 minutes"));

                                // RESTORED: Save OTP to DB
                                $stmt_otp = $pdo->prepare("INSERT INTO user_otps (user_id, otp_code, expires_at) VALUES (:uid, :otp, :expires)");
                                $stmt_otp->execute([':uid' => $id, ':otp' => $otp, ':expires' => $expires]);

                                // Send Email
                                require_once "config/db.php";
                                require_once "includes/mailer.php";
                                $ip = $_SERVER['REMOTE_ADDR'];
                                $browser = $_SERVER['HTTP_USER_AGENT'];

                                if (Mailer::sendOTP($email, $otp, $ip, $browser)) {
                                    $_SESSION["pending_otp_userid"] = $id;
                                    $_SESSION["pending_otp_email"] = $email;
                                    header("location: verify_otp.php");
                                    exit;
                                } else {
                                    $email_err = "Failed to send verification code. Please contact support.";
                                    error_log("OTP mail failed for $email. IP: $ip");
                                }
                            } else {
                                // Device is trusted, proceed to login
                                $_SESSION["loggedin"] = true;
                                $_SESSION["id"] = $id;
                                $_SESSION["email"] = $email;
                                header("location: dashboard.php");
                                exit;
                            }
                        } else {
                            $password_err = "The password you entered was not valid.";
                        }
                    }
                } else {
                    $email_err = "No account found with that email.";
                }
            } else {
                echo "Oops! Something went wrong. Please try again later.";
            }
            unset($stmt);
        }
    }
    unset($pdo);
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login | SecureVault</title>
    <link rel="icon" type="image/png" href="assets/images/favicon.png">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700;800;900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/style.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/crypto-js/4.1.1/crypto-js.min.js"></script>
    <script src="assets/js/crypto-helper.js"></script>

<body class="login-body">
    <div class="auth-card">
        <div style="margin-bottom: 2rem;">
            <a href="index.php"
                style="display: inline-flex; align-items: center; gap: 8px; font-size: 0.8rem; color: var(--text-dim); text-decoration: none; transition: 0.3s;"
                onmouseover="this.style.color='#fff'" onmouseout="this.style.color='var(--text-dim)'">
                ← Back to Home
            </a>
        </div>
        <div class="auth-logo">SecureVault</div>
        <h2>Welcome Back</h2>
        <p class="subtitle">Unlock your encrypted vault</p>

        <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post"
            onsubmit="return handleLogin(event)" class="auth-form">
            <div class="form-group">
                <label>Email Address</label>
                <input type="email" name="email" placeholder="e.g. name@example.com" value="<?php echo $email; ?>"
                    required>
                <?php if (!empty($email_err)): ?>
                    <span class="text-danger"
                        style="font-size: 0.8rem; margin-top: 5px; display: block;"><?php echo $email_err; ?></span>
                <?php endif; ?>
            </div>

            <div class="form-group">
                <label>Master Password</label>
                <input type="password" name="password" placeholder="••••••••••••" required>
                <?php if (!empty($password_err)): ?>
                    <span class="text-danger"
                        style="font-size: 0.8rem; margin-top: 5px; display: block;"><?php echo $password_err; ?></span>
                <?php endif; ?>
            </div>

            <button type="submit" class="btn-auth">Unlock Vault</button>

            <div class="auth-footer">
                Don't have an account? <a href="register.php">Create New Account</a>
            </div>

            <p class="auth-note">
                <strong>Security Note:</strong> Your Master Password is unrecoverable.
                If lost, your data remains encrypted and inaccessible.
            </p>
        </form>
    </div>
    <script>
        function handleLogin(e) {
            const form = e.target;
            const email = form.email.value;
            const password = form.password.value;

            if (!email || !password) return true;

            // Derive keys locally
            const masterKey = CryptoHelper.deriveMasterKey(password, email);
            const authHash = CryptoHelper.deriveAuthHash(masterKey);

            // Store Master Key in session storage (only for this tab session)
            CryptoHelper.setSessionKey(masterKey);

            // Replace password with authHash before sending to server
            form.password.value = authHash;

            return true;
        }
    </script>
</body>

</html>