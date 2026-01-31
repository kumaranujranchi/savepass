<?php
require_once "config/db.php";
require_once "includes/functions.php";

$email = $password = $confirm_password = "";
$email_err = $password_err = $confirm_password_err = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
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
            $param_password = password_hash($password, PASSWORD_DEFAULT); // Hash the password
            if ($stmt->execute()) {
                header("location: login.php");
            } else {
                echo "Something went wrong. Please try again later.";
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

        <form id="regForm" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post"
            onsubmit="return handleRegister(event)" class="auth-form">
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
                <input type="password" name="password" placeholder="Min. 6 characters" required>
                <?php if (!empty($password_err)): ?>
                    <span class="text-danger"
                        style="font-size: 0.8rem; margin-top: 5px; display: block;"><?php echo $password_err; ?></span>
                <?php endif; ?>
            </div>

            <div class="form-group">
                <label>Confirm Password</label>
                <input type="password" name="confirm_password" placeholder="Re-enter password" required>
                <?php if (!empty($confirm_password_err)): ?>
                    <span class="text-danger"
                        style="font-size: 0.8rem; margin-top: 5px; display: block;"><?php echo $confirm_password_err; ?></span>
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
            const form = e.target;
            const email = form.email.value;
            const password = form.password.value;
            const confirmPassword = form.confirm_password.value;

            if (password !== confirmPassword) {
                return true; // Let PHP handle validation error display for consistency
            }

            if (password.length < 6) {
                return true;
            }

            // Client-side hashing: Never send the actual Master Password
            const masterKey = CryptoHelper.deriveMasterKey(password, email);
            const authHash = CryptoHelper.deriveAuthHash(masterKey);

            // Replace values before submitting
            form.password.value = authHash;
            form.confirm_password.value = authHash;

            return true;
        }
    </script>
</body>

</html>