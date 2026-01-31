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
    <link rel="icon" type="image/png" href="assets/images/favicon.png">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/style.css">
    <script src="https://unpkg.com/lucide@latest"></script>
</head>

<body>
    <!-- Sidebar -->
    <aside class="sidebar desktop-only">
        <div class="brand">
            <img src="assets/images/logo.png" alt="SecureVault Logo" class="brand-logo">
        </div>

        <nav class="sidebar-nav">
            <a href="dashboard.php" class="nav-link <?php echo ($current_page == 'dashboard.php') ? 'active' : ''; ?>">
                <i data-lucide="layout-grid" style="width: 18px; height: 18px;"></i> Dashboard
            </a>
            <a href="passwords.php"
                class="nav-link <?php echo ($current_page == 'passwords.php' || $current_page == 'add_password.php') ? 'active' : ''; ?>">
                <i data-lucide="key-round" style="width: 18px; height: 18px;"></i> Passwords
            </a>
            <a href="apikeys.php" class="nav-link <?php echo ($current_page == 'apikeys.php') ? 'active' : ''; ?>">
                <i data-lucide="terminal" style="width: 18px; height: 18px;"></i> API Keys
            </a>
            <a href="notes.php" class="nav-link <?php echo ($current_page == 'notes.php') ? 'active' : ''; ?>">
                <i data-lucide="file-text" style="width: 18px; height: 18px;"></i> Secure Notes
            </a>

            <div class="nav-section-title">Account</div>
            <a href="#" class="nav-link"><i data-lucide="trash-2" style="width: 18px; height: 18px;"></i> Trash</a>
            <a href="settings.php" class="nav-link <?php echo ($current_page == 'settings.php') ? 'active' : ''; ?>">
                <i data-lucide="settings" style="width: 18px; height: 18px;"></i> Settings
            </a>
        </nav>

        <div class="sidebar-footer">
            <a href="logout.php" class="nav-link" style="margin-bottom: 1rem;"><i data-lucide="log-out" style="width: 18px; height: 18px;"></i> Logout</a>
            <div class="btn-pro">
                <i data-lucide="award" style="width: 18px; height: 18px;"></i> Upgrade to Pro
            </div>
        </div>
    </aside>

    <!-- Main Content Area -->
    <div class="main-content">
        <!-- Top Header -->
        <header class="top-header desktop-only">
            <div class="search-container">
                <i data-lucide="search" class="search-icon" style="width: 18px; height: 18px;"></i>
                <input type="text" class="search-bar" placeholder="Search passwords, notes or API keys (Cmd + K)">
            </div>
            <div class="header-actions">
                <i data-lucide="bell" style="width: 20px; height: 20px; cursor: pointer; color: var(--text-secondary);"></i>
                <i data-lucide="shield-check" style="width: 20px; height: 20px; cursor: pointer; color: var(--text-secondary);"></i>
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
                <div class="bottom-nav-icon"><i data-lucide="layout-grid"></i></div>
                <span>Home</span>
            </a>
            <a href="passwords.php"
                class="bottom-nav-item <?php echo ($current_page == 'passwords.php' || $current_page == 'add_password.php') ? 'active' : ''; ?>">
                <div class="bottom-nav-icon"><i data-lucide="key-round"></i></div>
                <span>Vault</span>
            </a>
            <a href="notes.php" class="bottom-nav-item <?php echo ($current_page == 'notes.php') ? 'active' : ''; ?>">
                <div class="bottom-nav-icon"><i data-lucide="file-text"></i></div>
                <span>Notes</span>
            </a>
            <a href="settings.php"
                class="bottom-nav-item <?php echo ($current_page == 'settings.php') ? 'active' : ''; ?>">
                <div class="bottom-nav-icon"><i data-lucide="settings"></i></div>
                <span>Settings</span>
            </a>
        </nav>

        <div class="page-container">