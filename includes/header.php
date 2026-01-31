<?php
// Active page check
$current_page = basename($_SERVER['PHP_SELF']);
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SecureVault</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/style.css">
</head>

<body>
    <div class="sidebar desktop-only">
        <h2>SecureVault</h2>
        <p style="font-size: 0.8rem; color: #888; margin-top: -10px; margin-bottom: 2rem;">Privacy First</p>
        <nav>
            <a href="dashboard.php"
                class="nav-item <?php echo ($current_page == 'dashboard.php') ? 'active' : ''; ?>">Dashboard</a>
            <a href="passwords.php"
                class="nav-item <?php echo ($current_page == 'passwords.php' || $current_page == 'add_password.php') ? 'active' : ''; ?>">Passwords</a>
            <a href="apikeys.php" class="nav-item <?php echo ($current_page == 'apikeys.php') ? 'active' : ''; ?>">API
                Keys</a>
            <a href="notes.php" class="nav-item <?php echo ($current_page == 'notes.php') ? 'active' : ''; ?>">Secure
                Notes</a>
            <a href="settings.php"
                class="nav-item <?php echo ($current_page == 'settings.php') ? 'active' : ''; ?>">Settings</a>
        </nav>

        <div style="margin-top: auto; padding-top: 2rem;">
            <div style="display: flex; align-items: center; margin-bottom: 1rem;">
                <div style="width: 32px; height: 32px; background: #555; border-radius: 50%; margin-right: 0.5rem;">
                </div>
                <div>
                    <div>
                        <?php echo htmlspecialchars($_SESSION['email'] ?? 'User'); ?>
                    </div>
                    <div style="font-size: 0.8rem; color: #4CAF50;">Online</div>
                </div>
            </div>
            <a href="logout.php" style="color: #888; font-size: 0.9rem;">Logout</a>
        </div>
    </div>

    <!-- Mobile Bottom Navigation -->
    <nav class="bottom-nav mobile-only">
        <a href="dashboard.php"
            class="bottom-nav-item <?php echo ($current_page == 'dashboard.php') ? 'active' : ''; ?>">
            <div class="bottom-nav-icon">🏠</div>
            <span>Home</span>
        </a>
        <a href="passwords.php"
            class="bottom-nav-item <?php echo ($current_page == 'passwords.php' || $current_page == 'add_password.php') ? 'active' : ''; ?>">
            <div class="bottom-nav-icon">🔑</div>
            <span>Vault</span>
        </a>
        <a href="notes.php" class="bottom-nav-item <?php echo ($current_page == 'notes.php') ? 'active' : ''; ?>">
            <div class="bottom-nav-icon">📝</div>
            <span>Notes</span>
        </a>
        <a href="settings.php" class="bottom-nav-item <?php echo ($current_page == 'settings.php') ? 'active' : ''; ?>">
            <div class="bottom-nav-icon">⚙️</div>
            <span>Settings</span>
        </a>
    </nav>
    <div class="main-content">