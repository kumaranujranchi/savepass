<?php
require_once "includes/auth_session.php";
require_once "config/db.php";
require_once "includes/functions.php";

$user_id = $_SESSION["id"];

// Fetch items
$sql = "SELECT * FROM vault_items WHERE user_id = :user_id ORDER BY created_at DESC";
$stmt = $pdo->prepare($sql);
$stmt->bindParam(":user_id", $user_id);
$stmt->execute();
$items = $stmt->fetchAll(PDO::FETCH_ASSOC);

require_once "includes/header.php";
?>

<h1 class="page-title">Saved Passwords</h1>
<p class="page-subtitle">Manage and access your encrypted credentials securely.</p>

<div class="section-card" style="margin-bottom: 2rem; padding: 1.5rem;">
    <div style="display: flex; flex-wrap: wrap; gap: 1.5rem; align-items: center; justify-content: space-between;">
        <div style="flex: 1; min-width: 300px; position: relative;">
            <input type="text" id="searchInput" onkeyup="filterList()" placeholder="Search passwords..."
                style="margin-bottom: 0; padding-left: 3.5rem; background: #1a1c26; border: 1px solid var(--border-color); border-radius: 12px; height: 52px; width: 100%;">
            <span
                style="position: absolute; left: 1.25rem; top: 50%; transform: translateY(-50%); color: var(--text-dim); font-size: 1.2rem;">🔍</span>
        </div>

        <div style="display: flex; gap: 12px; align-items: center;">
            <a href="import_passwords.php" class="btn-pro"
                style="padding: 12px 20px; font-size: 0.85rem; height: 52px; display: flex; align-items: center; border: 1px solid rgba(44, 15, 189, 0.3); background: rgba(44, 15, 189, 0.05);">
                <span style="margin-right: 8px;">🔄</span> Sync Browser
            </a>
            <div class="filters-container">
                <div class="filter-pill active" onclick="filterCategory('all')">All</div>
                <div class="filter-pill" onclick="filterCategory('Work')">Work</div>
                <div class="filter-pill" onclick="filterCategory('Personal')">Personal</div>
                <div class="filter-pill" onclick="filterCategory('Finance')">Finance</div>
                <div class="filter-pill" onclick="filterCategory('Social')">Social</div>
            </div>
        </div>
    </div>
</div>

<div class="section-card">
    <div style="overflow-x: auto;">
        <table class="activity-table">
            <thead>
                <tr>
                    <th>Service</th>
                    <th>Username</th>
                    <th>Strength</th>
                    <th class="desktop-only">Created At</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody id="passwordList">
                <?php foreach ($items as $item): ?>
                    <?php $decrypted_password = decryptData($item['password_enc']); ?>
                    <tr class="password-card" data-category="<?php echo htmlspecialchars($item['category']); ?>">
                        <td>
                            <div class="service-cell">
                                <div class="service-icon"><?php echo strtoupper(substr($item['app_name'], 0, 1)); ?></div>
                                <div class="app-name"><?php echo htmlspecialchars($item['app_name']); ?></div>
                            </div>
                        </td>
                        <td style="color: var(--text-secondary);"><?php echo htmlspecialchars($item['username']); ?></td>
                        <td>
                            <span class="strength-pill strong">
                                <span style="display: flex; gap: 2px;">
                                    <span
                                        style="width: 4px; height: 10px; background: currentColor; border-radius: 1px;"></span>
                                    <span
                                        style="width: 4px; height: 10px; background: currentColor; border-radius: 1px;"></span>
                                    <span
                                        style="width: 4px; height: 10px; background: currentColor; border-radius: 1px;"></span>
                                    <span
                                        style="width: 4px; height: 10px; background: currentColor; border-radius: 1px; opacity: 0.3;"></span>
                                </span>
                                Strong
                            </span>
                        </td>
                        <td class="desktop-only" style="color: var(--text-dim);">
                            <?php echo formatDate($item['created_at']); ?>
                        </td>
                        <td>
                            <div style="display: flex; gap: 12px; color: var(--text-dim); font-size: 1.1rem;">
                                <span class="icon-btn"
                                    onclick="copyToClipboard('<?php echo htmlspecialchars($item['username']); ?>')"
                                    title="Copy Username">👤</span>
                                <span class="icon-btn"
                                    onclick="copyToClipboard('<?php echo htmlspecialchars($decrypted_password); ?>')"
                                    title="Copy Password">🔑</span>
                            </div>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- Simple Toast Notification -->
<div id="toast"
    style="visibility: hidden; min-width: 250px; margin-left: -125px; background-color: #333; color: #fff; text-align: center; border-radius: 2px; padding: 16px; position: fixed; z-index: 1; left: 50%; bottom: 30px; font-size: 17px;">
    Copied to clipboard!</div>

<script>
    function copyToClipboard(text) {
        navigator.clipboard.writeText(text).then(function () {
            var x = document.getElementById("toast");
            x.style.visibility = "visible";
            setTimeout(function () { x.style.visibility = "hidden"; }, 3000);
        }, function (err) {
            console.error('Async: Could not copy text: ', err);
        });
    }

    function filterList() {
        var input, filter, list, cards, name, i, txtValue;
        input = document.getElementById('searchInput');
        filter = input.value.toUpperCase();
        list = document.getElementById("passwordList");
        cards = list.getElementsByClassName('password-card');

        for (i = 0; i < cards.length; i++) {
            name = cards[i].getElementsByClassName("app-name")[0];
            txtValue = name.textContent || name.innerText;
            if (txtValue.toUpperCase().indexOf(filter) > -1) {
                cards[i].style.display = "";
            } else {
                cards[i].style.display = "none";
            }
        }
    }

    function filterCategory(category) {
        var cards = document.getElementsByClassName('password-card');
        var pills = document.getElementsByClassName('filter-pill');

        // Update Active Pill
        for (var j = 0; j < pills.length; j++) {
            pills[j].classList.remove('active');
            if (pills[j].innerText === category || (category === 'all' && pills[j].innerText === 'All')) {
                pills[j].classList.add('active');
            }
        }

        // Filter Cards
        for (var i = 0; i < cards.length; i++) {
            var cat = cards[i].getAttribute('data-category');
            if (category === 'all' || cat === category) {
                cards[i].style.display = "";
            } else {
                cards[i].style.display = "none";
            }
        }
    }
</script>

<?php require_once "includes/footer.php"; ?>