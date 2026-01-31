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
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/style.css">
    <style>
        body {
            display: block;
        }
    </style>
</head>

<body class="login-body">
    <div class="login-container">
        <h1 class="login-title">SecureVault</h1>
        <div class="login-box">
            <h2>Welcome Back</h2>
            <p style="color: #ccc; margin-bottom: 2rem;">Unlock your encrypted vault</p>
            <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
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
                <div class="form-group">
                    <input type="submit" class="btn btn-primary" style="width: 100%;" value="Unlock Vault">
                </div>
                <div style="margin-top: 1rem; font-size: 0.9rem;">
                    <a href="register.php" style="color: #2c0fbd;">Create New Account</a>
                </div>
                <p style="margin-top: 2rem; font-size: 0.8rem; color: #666;">Security Note: Your Master Password is
                    unrecoverable. If lost, your data remains encrypted and inaccessible.</p>
            </form>
        </div>
    </div>
</body>

</html>