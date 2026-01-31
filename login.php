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

                        // Verify password
                        if (password_verify($password, $hashed_password)) {
                            // Password is correct, start a new session
                            session_start();

                            $_SESSION["loggedin"] = true;
                            $_SESSION["id"] = $id;
                            $_SESSION["email"] = $email;

                            header("location: dashboard.php");
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
    <title>Login - SecureVault</title>
    <link rel="icon" type="image/png" href="assets/images/favicon.png">
    <link rel="stylesheet" href="assets/css/style.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/crypto-js/4.1.1/crypto-js.min.js"></script>
    <script src="assets/js/crypto-helper.js"></script>

<body class="login-body">
    <div class="auth-card">
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