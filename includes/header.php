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
    <!-- Sidebar -->
    <aside class="sidebar desktop-only">
        <div class="brand">
            <img src="assets/images/logo.png" alt="SecureVault Logo" class="brand-logo">
        </div>

        <nav class="sidebar-nav">
            <a href="dashboard.php" class="nav-link <?php echo ($current_page == 'dashboard.php') ? 'active' : ''; ?>">
                <span>🏠</span> Dashboard
            </a>
            <a href="passwords.php"
                class="nav-link <?php echo ($current_page == 'passwords.php' || $current_page == 'add_password.php') ? 'active' : ''; ?>">
                <span>🔑</span> Passwords
            </a>
            <a href="apikeys.php" class="nav-link <?php echo ($current_page == 'apikeys.php') ? 'active' : ''; ?>">
                <span>🛠️</span> API Keys
            </a>
            <a href="notes.php" class="nav-link <?php echo ($current_page == 'notes.php') ? 'active' : ''; ?>">
                <span>📝</span> Secure Notes
            </a>

            <div class="nav-section-title">Account</div>
            <a href="#" class="nav-link"><span>🗑️</span> Trash</a>
            <a href="settings.php" class="nav-link <?php echo ($current_page == 'settings.php') ? 'active' : ''; ?>">
                <span>⚙️</span> Settings
            </a>
        </nav>

        <div class="sidebar-footer">
            <a href="logout.php" class="nav-link" style="margin-bottom: 1rem;"><span>🚪</span> Logout</a>
            <div class="btn-pro">
                <span>🏆</span> Upgrade to Pro
            </div>
        </div>
    </aside>

    <!-- Main Content Area -->
    <div class="main-content">
        <!-- Top Header -->
        <header class="top-header desktop-only">
            <div class="search-container">
                <span class="search-icon">🔍</span>
                <input type="text" class="search-bar" placeholder="Search passwords, notes or API keys (Cmd + K)">
            </div>
            <div class="header-actions">
                <span style="font-size: 1.2rem; cursor: pointer; color: var(--text-secondary);">🔔</span>
                <span style="font-size: 1.2rem; cursor: pointer; color: var(--text-secondary);">🛡️</span>
                <div class="profile-card">
                    <div class="profile-info">
                        <span class="profile-name"><?php echo htmlspecialchars($_SESSION['email'] ?? 'User'); ?></span>
                        <span class="security-badge">Security Grade: A+</span>
                    </div>
                    <div class="avatar"></div>
                </div>
            </div>
        </header>

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
            <a href="settings.php"
                class="bottom-nav-item <?php echo ($current_page == 'settings.php') ? 'active' : ''; ?>">
                <div class="bottom-nav-icon">⚙️</div>
                <span>Settings</span>
            </a>
        </nav>

        <div class="page-container">