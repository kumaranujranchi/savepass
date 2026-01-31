<?php
require_once "includes/auth_session.php";
require_once "config/db.php";
require_once "includes/functions.php";

$user_id = $_SESSION["id"];
$app_name = $url = $username = $password = $category = $notes = "";
$error = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Collect and clean inputs
    $app_name = cleanInput($_POST["app_name"]);
    $url = cleanInput($_POST["url"]);
    $username = cleanInput($_POST["username"]);
    $password_raw = $_POST["password"]; // Don't clean password special chars
    $category = cleanInput($_POST["category"]);
    $notes = cleanInput($_POST["notes"]);

    if (empty($app_name) || empty($username) || empty($password_raw)) {
        $error = "App Name, Username and Password are required.";
    } else {
        // Encrypt sensitive data
        $password_enc = encryptData($password_raw);
        $notes_enc = !empty($notes) ? encryptData($notes) : null;

        $sql = "INSERT INTO vault_items (user_id, app_name, website_url, username, password_enc, category, notes_enc) VALUES (:user_id, :app_name, :url, :username, :password_enc, :category, :notes_enc)";

        if ($stmt = $pdo->prepare($sql)) {
            $stmt->bindParam(":user_id", $user_id);
            $stmt->bindParam(":app_name", $app_name);
            $stmt->bindParam(":url", $url);
            $stmt->bindParam(":username", $username);
            $stmt->bindParam(":password_enc", $password_enc);
            $stmt->bindParam(":category", $category);
            $stmt->bindParam(":notes_enc", $notes_enc);

            if ($stmt->execute()) {
                header("location: passwords.php");
                exit();
            } else {
                $error = "Error saving data.";
            }
        }
    }
}

require_once "includes/header.php";
?>

<div style="display: flex; justify-content: center;">
    <div style="width: 100%; max-width: 600px;">
        <h1 style="text-align: center;">Add New Entry</h1>
        <div class="card">
            <?php if (!empty($error)): ?>
                <div class="text-danger mb-2">
                    <?php echo $error; ?>
                </div>
            <?php endif; ?>

            <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
                <div class="form-group">
                    <label>App Name</label>
                    <input type="text" name="app_name" placeholder="e.g. Netflix" value="<?php echo $app_name; ?>"
                        required>
                </div>

                <div class="form-group">
                    <label>Website URL</label>
                    <input type="url" name="url" placeholder="https://" value="<?php echo $url; ?>">
                </div>

                <div class="form-group">
                    <label>Username / Email</label>
                    <input type="text" name="username" placeholder="user@example.com" value="<?php echo $username; ?>"
                        required>
                </div>

                <div class="form-group">
                    <label>Password</label>
                    <div style="position: relative;">
                        <input type="text" name="password" id="password_field" placeholder="Password" required>
                        <button type="button" onclick="generatePassword()"
                            style="position: absolute; right: 8px; top: 50%; transform: translateY(-50%); background: transparent; color: #2c0fbd; border: none; font-weight: bold; cursor: pointer; font-size: 0.8rem;">GENERATE</button>
                    </div>
                    <div
                        style="height: 4px; background: #333; margin-top: 0.5rem; border-radius: 2px; overflow: hidden; display: flex;">
                        <div style="flex: 1; height: 100%; background: #4caf50; margin-right: 2px;"></div>
                        <div style="flex: 1; height: 100%; background: #4caf50; margin-right: 2px;"></div>
                        <div style="flex: 1; height: 100%; background: #333; margin-right: 2px;"></div>
                    </div>
                    <div style="font-size: 0.7rem; color: #4caf50; margin-top: 0.2rem; text-align: right;">Strength
                        Indicator</div>
                </div>

                <div class="form-group">
                    <label>Category</label>
                    <select name="category">
                        <option value="Personal">Personal</option>
                        <option value="Work">Work</option>
                        <option value="Finance">Finance</option>
                        <option value="Social">Social</option>
                        <option value="Other">Other</option>
                    </select>
                </div>

                <div class="form-group">
                    <label>Notes</label>
                    <textarea name="notes" rows="3" placeholder="Additional secure notes..."></textarea>
                </div>

                <div style="display: flex; gap: 1rem; margin-top: 2rem;">
                    <a href="passwords.php" class="btn btn-cancel" style="flex: 1; text-align: center;">Cancel</a>
                    <button type="submit" class="btn btn-primary" style="flex: 1;">Save Entry</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    function generatePassword() {
        const chars = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789!@#$%^&*()_+";
        let password = "";
        for (let i = 0; i < 16; i++) {
            password += chars.charAt(Math.floor(Math.random() * chars.length));
        }
        document.getElementById("password_field").value = password;
    }
</script>

<?php require_once "includes/footer.php"; ?>