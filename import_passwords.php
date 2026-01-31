<?php
require_once "includes/auth_session.php";
require_once "config/db.php";
require_once "includes/functions.php";

$user_id = $_SESSION["id"];
$import_status = "";
$import_count = 0;

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_FILES["csv_file"])) {
    $file = $_FILES["csv_file"]["tmp_name"];

    if (($handle = fopen($file, "r")) !== FALSE) {
        $header = fgetcsv($handle, 1000, ",");

        // Normalize header to lowercase
        $header = array_map('strtolower', $header);

        // Map headers to indices
        $name_idx = array_search('name', $header);
        $url_idx = array_search('url', $header);
        $user_idx = array_search('username', $header);
        $pass_idx = array_search('password', $header);

        // If chrome export, headers might be 'name','url','username','password'
        // If some headers not found, try common alternatives
        if ($name_idx === false)
            $name_idx = array_search('title', $header);
        if ($url_idx === false)
            $url_idx = array_search('website', $header);
        if ($user_idx === false)
            $user_idx = array_search('user', $header);
        if ($pass_idx === false)
            $pass_idx = array_search('pass', $header);

        while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
            $app_name = ($name_idx !== false) ? $data[$name_idx] : "Imported Item";
            $website_url = ($url_idx !== false) ? $data[$url_idx] : "";
            $username = ($user_idx !== false) ? $data[$user_idx] : "";
            $password_raw = ($pass_idx !== false) ? $data[$pass_idx] : "";

            if (!empty($password_raw)) {
                $password_enc = encryptData($password_raw);
                $sql = "INSERT INTO vault_items (user_id, app_name, website_url, username, password_enc, category) 
                        VALUES (:user_id, :app_name, :website_url, :username, :password_enc, 'Other')";
                $stmt = $pdo->prepare($sql);
                $stmt->execute([
                    ':user_id' => $user_id,
                    ':app_name' => $app_name,
                    ':website_url' => $website_url,
                    ':username' => $username,
                    ':password_enc' => $password_enc
                ]);
                $import_count++;
            }
        }
        fclose($handle);
        $import_status = "Successfully imported $import_count passwords!";
    } else {
        $import_status = "Error opening file.";
    }
}

require_once "includes/header.php";
?>

<div class="page-container">
    <h1 class="page-title">Sync Browser Passwords</h1>
    <p class="page-subtitle">Upload the CSV file exported from your browser (Chrome, Safari, Edge).</p>

    <?php if ($import_status): ?>
        <div class="section-card"
            style="background: rgba(44, 15, 189, 0.1); border: 1px solid var(--accent-primary); border-radius: 12px; padding: 1.5rem; margin-bottom: 2rem;">
            <div style="display: flex; align-items: center; gap: 1rem;">
                <span style="font-size: 1.5rem;">✅</span>
                <span style="font-weight: 700; color: white;">
                    <?php echo $import_status; ?>
                </span>
            </div>
            <a href="passwords.php" class="btn btn-primary"
                style="margin-top: 1.5rem; width: auto; padding: 0.8rem 2rem;">View Passwords</a>
        </div>
    <?php endif; ?>

    <div class="section-card" style="padding: 2.5rem; max-width: 600px;">
        <form method="post" enctype="multipart/form-data">
            <div style="border: 2px dashed var(--border-color); border-radius: 16px; padding: 3rem 2rem; text-align: center; margin-bottom: 2rem; background: rgba(255,255,255,0.02); transition: 0.3s;"
                onmouseover="this.style.background='rgba(255,255,255,0.04)'"
                onmouseout="this.style.background='rgba(255,255,255,0.02)'">
                <div style="font-size: 3rem; margin-bottom: 1.5rem; opacity: 0.5;">📁</div>
                <h3 style="margin-bottom: 10px;">Select CSV File</h3>
                <p style="color: var(--text-dim); font-size: 0.9rem; margin-bottom: 2rem;">Only .csv files are
                    supported. Export from Chrome Settings > Passwords.</p>

                <input type="file" name="csv_file" id="csv_file" accept=".csv" required style="display: none;"
                    onchange="updateFileName(this)">
                <label for="csv_file" class="btn-pro"
                    style="cursor: pointer; display: inline-flex; width: auto; padding: 12px 24px;">
                    Choose File
                </label>
                <div id="file-name"
                    style="margin-top: 1rem; color: var(--accent-secondary); font-weight: 800; font-size: 0.85rem;">
                </div>
            </div>

            <button type="submit" class="btn btn-primary"
                style="width: 100%; padding: 1rem; font-weight: 800; font-size: 1rem; border-radius: 12px; box-shadow: 0 4px 15px rgba(44, 15, 189, 0.3);">
                Start Import
            </button>
        </form>
    </div>

    <div class="section-card" style="margin-top: 3rem; padding: 2rem;">
        <h3 style="margin-bottom: 1.5rem; display: flex; align-items: center; gap: 10px;">
            <span style="font-size: 1.2rem;">💡</span> Help: How to export?
        </h3>
        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 1.5rem;">
            <div>
                <div style="font-weight: 800; color: var(--text-primary); margin-bottom: 8px; font-size: 0.9rem;">Google
                    Chrome</div>
                <p style="font-size: 0.8rem; color: var(--text-dim); line-height: 1.5;">Settings > Passwords > Three
                    dots (⋮) > Export Passwords...</p>
            </div>
            <div>
                <div style="font-weight: 800; color: var(--text-primary); margin-bottom: 8px; font-size: 0.9rem;">Safari
                    (Mac)</div>
                <p style="font-size: 0.8rem; color: var(--text-dim); line-height: 1.5;">Settings > Passwords > Share
                    button > Export All Passwords...</p>
            </div>
            <div>
                <div style="font-weight: 800; color: var(--text-primary); margin-bottom: 8px; font-size: 0.9rem;">MSEdge
                </div>
                <p style="font-size: 0.8rem; color: var(--text-dim); line-height: 1.5;">Settings > Profiles > Passwords
                    > More actions > Export Passwords...</p>
            </div>
        </div>
    </div>
</div>

<script>
    function updateFileName(input) {
        const fileName = input.files[0] ? input.files[0].name : '';
        document.getElementById('file-name').textContent = fileName ? 'Selected: ' + fileName : '';
    }
</script>

<?php require_once "includes/footer.php"; ?>