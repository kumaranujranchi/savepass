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
        // Zero-Knowledge: Data is ALREADY encrypted on the client side.
        // We'll store it as is.
        $password_enc = $password_raw;
        $notes_enc = !empty($notes) ? $notes : null;

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

            <form id="vaultForm" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post"
                onsubmit="return handleVaultSubmit(event)">
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
                        <input type="password" name="password" id="password_field" placeholder="Password" required
                            style="padding-right: 80px;">
                        <i data-lucide="eye" onclick="togglePasswordVisibility('password_field', this)"
                            style="position: absolute; right: 50px; top: 50%; transform: translateY(-50%); cursor: pointer; color: var(--text-dim); width: 18px; height: 18px;"
                            title="Toggle visibility"></i>
                        <button type="button" onclick="openGenerator()"
                            style="position: absolute; right: 8px; top: 50%; transform: translateY(-50%); background: transparent; color: #2c0fbd; border: none; font-weight: bold; cursor: pointer; font-size: 0.8rem;">GENERATE</button>
                    </div>
                    <div
                        style="height: 4px; background: #333; margin-top: 0.5rem; border-radius: 2px; overflow: hidden; display: flex;">
                        <div style="flex: 1; height: 100%; background: #4caf50; margin-right: 2px;"></div>
                        <div style="flex: 1; height: 100%; background: #4caf50; margin-right: 2px;"></div>
                        <div style="flex: 1; height: 100%; background: #333; margin-right: 2px;"></div>
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


<!-- Password Generator Modal -->
<div id="generatorModal"
    style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.7); z-index: 2000; align-items: center; justify-content: center;">
    <div
        style="background: var(--bg-card); border-radius: 16px; padding: 2rem; max-width: 500px; width: 90%; border: 1px solid var(--border-color);">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem;">
            <h3 style="margin: 0; font-size: 1.2rem;">Password Generator</h3>
            <i data-lucide="x" onclick="closeGenerator()"
                style="cursor: pointer; width: 24px; height: 24px; color: var(--text-dim);"></i>
        </div>

        <!-- Generated Password Display -->
        <div
            style="background: rgba(255,255,255,0.03); border: 1px solid var(--border-color); border-radius: 10px; padding: 16px; margin-bottom: 1.5rem; display: flex; align-items: center; gap: 12px;">
            <input type="text" id="generatedPassword" readonly
                style="flex: 1; background: transparent; border: none; color: var(--text-primary); font-size: 1.1rem; font-family: monospace;"
                value="GeneratedPass123!">
            <i data-lucide="copy" onclick="copyWithFeedback(document.getElementById('generatedPassword').value, this)"
                style="cursor: pointer; width: 20px; height: 20px; color: var(--accent-primary);" title="Copy"></i>
        </div>

        <!-- Strength Indicator -->
        <div style="margin-bottom: 1.5rem;">
            <div style="display: flex; justify-content: space-between; margin-bottom: 8px;">
                <span style="font-size: 0.85rem; color: var(--text-secondary);">Strength</span>
                <span id="strengthLabel" style="font-size: 0.85rem; font-weight: 700; color: #00e676;">Strong</span>
            </div>
            <div style="display: flex; gap: 4px; height: 6px;">
                <div id="bar1" style="flex: 1; background: #00e676; border-radius: 3px;"></div>
                <div id="bar2" style="flex: 1; background: #00e676; border-radius: 3px;"></div>
                <div id="bar3" style="flex: 1; background: #00e676; border-radius: 3px;"></div>
                <div id="bar4" style="flex: 1; background: #333; border-radius: 3px;"></div>
            </div>
        </div>

        <!-- Length Slider -->
        <div style="margin-bottom: 1.5rem;">
            <div style="display: flex; justify-content: space-between; margin-bottom: 8px;">
                <label style="font-size: 0.85rem; color: var(--text-secondary);">Length</label>
                <span id="lengthValue"
                    style="font-size: 0.85rem; font-weight: 700; color: var(--text-primary);">16</span>
            </div>
            <input type="range" id="lengthSlider" min="8" max="32" value="16"
                oninput="updateLength(this.value); generateNewPassword();"
                style="width: 100%; accent-color: var(--accent-primary);">
        </div>

        <!-- Character Options -->
        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 12px; margin-bottom: 1.5rem;">
            <label
                style="display: flex; align-items: center; gap: 8px; cursor: pointer; padding: 10px; background: rgba(255,255,255,0.03); border-radius: 8px;">
                <input type="checkbox" id="optUppercase" checked onchange="generateNewPassword()"
                    style="accent-color: var(--accent-primary);">
                <span style="font-size: 0.9rem;">Uppercase (A-Z)</span>
            </label>
            <label
                style="display: flex; align-items: center; gap: 8px; cursor: pointer; padding: 10px; background: rgba(255,255,255,0.03); border-radius: 8px;">
                <input type="checkbox" id="optLowercase" checked onchange="generateNewPassword()"
                    style="accent-color: var(--accent-primary);">
                <span style="font-size: 0.9rem;">Lowercase (a-z)</span>
            </label>
            <label
                style="display: flex; align-items: center; gap: 8px; cursor: pointer; padding: 10px; background: rgba(255,255,255,0.03); border-radius: 8px;">
                <input type="checkbox" id="optNumbers" checked onchange="generateNewPassword()"
                    style="accent-color: var(--accent-primary);">
                <span style="font-size: 0.9rem;">Numbers (0-9)</span>
            </label>
            <label
                style="display: flex; align-items: center; gap: 8px; cursor: pointer; padding: 10px; background: rgba(255,255,255,0.03); border-radius: 8px;">
                <input type="checkbox" id="optSymbols" checked onchange="generateNewPassword()"
                    style="accent-color: var(--accent-primary);">
                <span style="font-size: 0.9rem;">Symbols (!@#$)</span>
            </label>
        </div>

        <!-- Actions -->
        <div style="display: flex; gap: 12px;">
            <button onclick="generateNewPassword()" class="btn-cancel" style="flex: 1;">
                <i data-lucide="refresh-cw" style="width: 16px; height: 16px; margin-right: 6px;"></i>
                Regenerate
            </button>
            <button onclick="useGeneratedPassword()" class="btn-primary" style="flex: 1;">Use Password</button>
        </div>
    </div>
</div>

<script src="assets/js/password-generator.js"></script>
<script>
    function openGenerator() {
        document.getElementById('generatorModal').style.display = 'flex';
        generateNewPassword();
        lucide.createIcons();
    }

    function closeGenerator() {
        document.getElementById('generatorModal').style.display = 'none';
    }

    function updateLength(value) {
        document.getElementById('lengthValue').textContent = value;
    }

    function generateNewPassword() {
        const options = {
            length: parseInt(document.getElementById('lengthSlider').value),
            uppercase: document.getElementById('optUppercase').checked,
            lowercase: document.getElementById('optLowercase').checked,
            numbers: document.getElementById('optNumbers').checked,
            symbols: document.getElementById('optSymbols').checked
        };

        const password = PasswordGenerator.generate(options);
        document.getElementById('generatedPassword').value = password;

        // Update strength indicator
        const strength = PasswordGenerator.calculateStrength(password);
        const info = PasswordGenerator.getStrengthInfo(strength);

        document.getElementById('strengthLabel').textContent = info.label;
        document.getElementById('strengthLabel').style.color = info.color;

        // Update bars
        for (let i = 1; i <= 4; i++) {
            const bar = document.getElementById('bar' + i);
            bar.style.background = i <= info.bars ? info.color : '#333';
        }
    }

    function useGeneratedPassword() {
        const password = document.getElementById('generatedPassword').value;
        document.getElementById('password_field').value = password;
        closeGenerator();
        updatePasswordStrength(password);
    }

    function updatePasswordStrength(password) {
        const strength = PasswordGenerator.calculateStrength(password);
        const info = PasswordGenerator.getStrengthInfo(strength);

        // Update the strength indicator in the form
        const bars = document.querySelectorAll('#password_field + div > div > div');
        bars.forEach((bar, index) => {
            bar.style.background = index < info.bars ? info.color : '#333';
        });

        const strengthText = document.querySelector('#password_field + div + div');
        if (strengthText) {
            strengthText.textContent = 'Strength: ' + info.label;
            strengthText.style.color = info.color;
        }
    }

    // Update strength on password input
    document.addEventListener('DOMContentLoaded', function () {
        const passwordField = document.getElementById('password_field');
        if (passwordField) {
            passwordField.addEventListener('input', function () {
                updatePasswordStrength(this.value);
            });
        }
    });

    function handleVaultSubmit(e) {

        const form = e.target;
        const key = CryptoHelper.getSessionKey();

        if (!key) {
            alert("Security Error: Master Key not found. Please log in again.");
            window.location.href = 'login.php';
            return false;
        }

        // Encrypt sensitive fields locally
        form.password.value = CryptoHelper.encrypt(form.password.value, key);
        if (form.notes.value) {
            form.notes.value = CryptoHelper.encrypt(form.notes.value, key);
        }

        return true;
    }

    function copyWithFeedback(text, buttonElement) {
        if (!text) return;

        navigator.clipboard.writeText(text).then(() => {
            // Change icon temporarily
            const originalIcon = buttonElement.getAttribute('data-lucide');
            buttonElement.setAttribute('data-lucide', 'check');
            buttonElement.style.color = 'var(--green-sec)';
            lucide.createIcons();

            showToast("Copied to clipboard!");

            // Trigger automatic clipboard clear
            if (typeof SecurityManager !== 'undefined') {
                SecurityManager.scheduleClipboardClear();
            }

            // Restore icon after 2 seconds
            setTimeout(() => {
                buttonElement.setAttribute('data-lucide', originalIcon);
                buttonElement.style.color = '';
                lucide.createIcons();
            }, 2000);
        }).catch(err => {
            showToast('Failed to copy');
            console.error('Copy failed:', err);
        });
    }

    function showToast(msg) {
        let toast = document.getElementById("toast");
        if (!toast) {
            toast = document.createElement("div");
            toast.id = "toast";
            toast.style = "visibility: hidden; min-width: 250px; margin-left: -125px; background-color: #333; color: #fff; text-align: center; border-radius: 2px; padding: 16px; position: fixed; z-index: 1; left: 50%; bottom: 30px; font-size: 17px;";
            document.body.appendChild(toast);
        }
        toast.innerText = msg;
        toast.style.visibility = "visible";
        setTimeout(function () { toast.style.visibility = "hidden"; }, 3000);
    }
</script>

<?php require_once "includes/footer.php"; ?>