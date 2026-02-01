<?php
require_once "config/db.php";
require_once "includes/functions.php";

$email = $password = $confirm_password = "";
$email_err = $password_err = $confirm_password_err = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    error_log("Registration attempt started");
    error_log("POST data: " . print_r($_POST, true));

    // Validate email
    if (empty(trim($_POST["email"]))) {
        $email_err = "Please enter an email.";
    } else {
        $sql = "SELECT id FROM users WHERE email = :email";
        if ($stmt = $pdo->prepare($sql)) {
            $stmt->bindParam(":email", $param_email, PDO::PARAM_STR);
            $param_email = trim($_POST["email"]);
            if ($stmt->execute()) {
                if ($stmt->rowCount() == 1) {
                    $email_err = "This email is already taken.";
                } else {
                    $email = trim($_POST["email"]);
                }
            } else {
                echo "Oops! Something went wrong. Please try again later.";
            }
            unset($stmt);
        }
    }

    // Validate password
    if (empty(trim($_POST["password"]))) {
        $password_err = "Please enter a password.";
    } elseif (strlen(trim($_POST["password"])) < 6) {
        $password_err = "Password must have at least 6 characters.";
    } else {
        $password = trim($_POST["password"]);
    }

    // Validate confirm password
    if (empty(trim($_POST["confirm_password"]))) {
        $confirm_password_err = "Please confirm password.";
    } else {
        $confirm_password = trim($_POST["confirm_password"]);
        if (empty($password_err) && ($password != $confirm_password)) {
            $confirm_password_err = "Password did not match.";
        }
    }

    // Check input errors before inserting in database
    if (empty($email_err) && empty($password_err) && empty($confirm_password_err)) {
        $sql = "INSERT INTO users (email, password_hash) VALUES (:email, :password)";
        if ($stmt = $pdo->prepare($sql)) {
            $stmt->bindParam(":email", $param_email, PDO::PARAM_STR);
            $stmt->bindParam(":password", $param_password, PDO::PARAM_STR);
            $param_email = $email;
            // Zero-Knowledge: Client sends authHash, not plain password
            // We store it directly without re-hashing
            $param_password = $password;
            if ($stmt->execute()) {
                error_log("User registered successfully: " . $email);
                header("location: login.php");
                exit();
            } else {
                $error_info = $stmt->errorInfo();
                error_log("Registration failed: " . print_r($error_info, true));
                echo "<div style='color: red; padding: 20px; background: rgba(255,0,0,0.1); border-radius: 8px; margin: 20px;'>";
                echo "<strong>Database Error:</strong><br>";
                echo "SQLSTATE: " . $error_info[0] . "<br>";
                echo "Error Code: " . $error_info[1] . "<br>";
                echo "Message: " . $error_info[2];
                echo "</div>";
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
    <title>Register - SecureVault</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/crypto-js/4.1.1/crypto-js.min.js"></script>
    <script src="assets/js/crypto-helper.js"></script>

<body class="login-body">
    <div class="auth-card">
        <div class="auth-logo">SecureVault</div>
        <h2>Create Account</h2>
        <p class="subtitle">Setup your master password</p>

        <?php
        // Display all errors in a prominent box at the top
        if (!empty($email_err) || !empty($password_err) || !empty($confirm_password_err)):
            ?>
            <div
                style="background: rgba(244, 67, 54, 0.1); border-left: 4px solid #f44336; padding: 15px; margin-bottom: 20px; border-radius: 8px;">
                <strong style="color: #f44336; display: block; margin-bottom: 8px;">⚠️ Registration Error</strong>
                <?php if (!empty($email_err)): ?>
                    <p style="color: #d32f2f; margin: 5px 0; font-size: 0.9rem;">• <?php echo $email_err; ?></p>
                <?php endif; ?>
                <?php if (!empty($password_err)): ?>
                    <p style="color: #d32f2f; margin: 5px 0; font-size: 0.9rem;">• <?php echo $password_err; ?></p>
                <?php endif; ?>
                <?php if (!empty($confirm_password_err)): ?>
                    <p style="color: #d32f2f; margin: 5px 0; font-size: 0.9rem;">• <?php echo $confirm_password_err; ?></p>
                <?php endif; ?>
            </div>
        <?php endif; ?>

        <form id="regForm" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post"
            onsubmit="return handleRegister(event)" class="auth-form">
            <div class="form-group">
                <label>Email Address</label>
                <input type="email" name="email" placeholder="e.g. name@example.com" value="<?php echo $email; ?>"
                    required>
                <?php if (!empty($email_err)): ?>
                    <span class="text-danger"
                        style="color: #f44336; font-size: 0.85rem; margin-top: 5px; display: block; font-weight: 500;"><?php echo $email_err; ?></span>
                <?php endif; ?>
            </div>

            <div class="form-group">
                <label>Master Password</label>
                <input type="password" name="password" placeholder="Min. 6 characters" required>
                <?php if (!empty($password_err)): ?>
                    <span class="text-danger"
                        style="color: #f44336; font-size: 0.85rem; margin-top: 5px; display: block; font-weight: 500;"><?php echo $password_err; ?></span>
                <?php endif; ?>
            </div>

            <div class="form-group">
                <label>Confirm Password</label>
                <input type="password" name="confirm_password" placeholder="Re-enter password" required>
                <?php if (!empty($confirm_password_err)): ?>
                    <span class="text-danger"
                        style="color: #f44336; font-size: 0.85rem; margin-top: 5px; display: block; font-weight: 500;"><?php echo $confirm_password_err; ?></span>
                <?php endif; ?>
            </div>

            <button type="submit" class="btn-auth">Sign Up</button>

            <div class="auth-footer">
                Already have an account? <a href="login.php">Login here</a>
            </div>

            <p class="auth-note">
                <strong>Important:</strong> Your Master Password is your ONLY key.
                We cannot reset it for you. Please store it safely.
            </p>
        </form>
    </div>

    <script>
        function handleRegister(e) {
            console.log('Registration form submitted');
            const form = e.target;
            const email = form.email.value;
            const password = form.password.value;
            const confirmPassword = form.confirm_password.value;

            console.log('Email:', email);
            console.log('Password length:', password.length);

            if (password !== confirmPassword) {
                console.warn('Passwords do not match');
                return true; // Let PHP handle validation error display for consistency
            }

            if (password.length < 6) {
                console.warn('Password too short');
                return true;
            }

            try {
                console.log('Starting key derivation...');
                // Client-side hashing: Never send the actual Master Password
                const masterKey = CryptoHelper.deriveMasterKey(password, email);
                console.log('Master Key derived:', masterKey.substring(0, 20) + '...');

                const authHash = CryptoHelper.deriveAuthHash(masterKey);
                console.log('Auth Hash derived:', authHash.substring(0, 20) + '...');

                // Replace values before submitting
                form.password.value = authHash;
                form.confirm_password.value = authHash;

                console.log('Form values replaced with authHash, submitting...');
                return true;
            } catch (error) {
                console.error('Crypto error:', error);
                alert('Encryption error: ' + error.message);
                return false;
            }
        }
    </script>
</body>

</html>