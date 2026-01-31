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
    <style>
        body {
            display: block;
        }
    </style> <!-- Reset body flex for this layout if needed, though style.css handles it via class -->
</head>

<body class="login-body">
    <div class="login-container">
        <h1 class="login-title">SecureVault</h1>
        <div class="login-box">
            <h2>Create Account</h2>
            <p style="color: #ccc; margin-bottom: 2rem;">Setup your master password</p>
            <form id="regForm" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post"
                onsubmit="return handleRegister(event)">
                <div class="form-group <?php echo (!empty($email_err)) ? 'has-error' : ''; ?>">
                    <input type="email" name="email" placeholder="Email Address" value="<?php echo $email; ?>">
                    <span class="text-danger">
                        <?php echo $email_err; ?>
                    </span>
                </div>
                <div class="form-group <?php echo (!empty($password_err)) ? 'has-error' : ''; ?>">
                    <input type="password" name="password" placeholder="Master Password">
                    <span class="text-danger">
                        <?php echo $password_err; ?>
                    </span>
                </div>
                <div class="form-group <?php echo (!empty($confirm_password_err)) ? 'has-error' : ''; ?>">
                    <input type="password" name="confirm_password" placeholder="Confirm Master Password">
                    <span class="text-danger">
                        <?php echo $confirm_password_err; ?>
                    </span>
                </div>
                <div class="form-group">
                    <input type="submit" class="btn btn-primary" style="width: 100%;" value="Sign Up">
                </div>
                <p>Already have an account? <a href="login.php" style="color: #2c0fbd;">Login here</a>.</p>
            </form>
        </div>
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