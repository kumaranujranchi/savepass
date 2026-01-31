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

<div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem;">
    <h1>Passwords</h1>
    <a href="add_password.php"
        style="background: #2c0fbd; color: white; padding: 0.6rem 1rem; border-radius: 6px; font-weight: bold; font-size: 0.9rem;">+
        Add New</a>
</div>

<input type="text" id="searchInput" onkeyup="filterList()" placeholder="Search passwords..." style="max-width: 400px;">

<div class="filters">
    <div class="filter-pill active" onclick="filterCategory('all')">All</div>
    <div class="filter-pill" onclick="filterCategory('Work')">Work</div>
    <div class="filter-pill" onclick="filterCategory('Personal')">Personal</div>
    <div class="filter-pill" onclick="filterCategory('Finance')">Finance</div>
    <div class="filter-pill" onclick="filterCategory('Social')">Social</div>
</div>

<div class="card-list" id="passwordList">
    <?php foreach ($items as $item): ?>
        <?php
        $decrypted_password = decryptData($item['password_enc']);
        ?>
        <div class="password-card" data-category="<?php echo htmlspecialchars($item['category']); ?>">
            <div class="app-icon">
                <?php echo strtoupper(substr($item['app_name'], 0, 1)); ?>
            </div>
            <div class="card-details">
                <div class="app-name">
                    <?php echo htmlspecialchars($item['app_name']); ?>
                </div>
                <div class="username">
                    <?php echo htmlspecialchars($item['username']); ?>
                </div>
                <div class="password-row">
                    <span class="dots">••••••••</span>
                    <div class="action-icons">
                        <span class="icon-btn"
                            onclick="copyToClipboard('<?php echo htmlspecialchars($item['username']); ?>')"
                            title="Copy Username">👤</span>
                        <span class="icon-btn"
                            onclick="copyToClipboard('<?php echo htmlspecialchars($decrypted_password); ?>')"
                            title="Copy Password">🔑</span>
                    </div>
                </div>
            </div>
        </div>
    <?php endforeach; ?>
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