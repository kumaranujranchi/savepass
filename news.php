<?php
session_start();
$isLoggedIn = isset($_SESSION["id"]);
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>News & Updates | SecureVault</title>
    <!-- Favicon -->
    <link rel="icon" type="image/png" href="assets/images/favicon.png">
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700;800;900&display=swap" rel="stylesheet">
    <!-- Lucide Icons -->
    <script src="https://unpkg.com/lucide@latest"></script>
    <link rel="stylesheet" href="assets/css/style.css?v=<?php echo time(); ?>">

    <style>
        :root {
            --bg-main: #0a0b10;
            --card-bg: rgba(22, 23, 30, 0.6);
            --accent-primary: #2c0fbd;
            --accent-glow: rgba(44, 15, 189, 0.4);
            --text-primary: #ffffff;
            --text-secondary: #8e92a4;
            --text-dim: #5c5f73;
            --border-color: rgba(255, 255, 255, 0.1);
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Inter', sans-serif;
        }

        body {
            background-color: var(--bg-main);
            color: var(--text-primary);
            line-height: 1.6;
        }

        .ambient-bg {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            z-index: -1;
            background: radial-gradient(circle at 50% 10%, rgba(44, 15, 189, 0.1) 0%, transparent 50%);
        }

        nav {
            padding: 1.5rem 5%;
            display: flex;
            justify-content: space-between;
            align-items: center;
            position: sticky;
            top: 0;
            z-index: 100;
            background: rgba(10, 11, 16, 0.8);
            backdrop-filter: blur(10px);
            border-bottom: 1px solid var(--border-color);
        }

        .logo {
            font-size: 1.5rem;
            font-weight: 900;
            display: flex;
            align-items: center;
            gap: 10px;
            background: linear-gradient(135deg, #fff 0%, #8e92a4 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            text-decoration: none;
        }

        .nav-links {
            display: flex;
            gap: 2rem;
            align-items: center;
        }

        .nav-links a {
            color: var(--text-secondary);
            font-weight: 600;
            font-size: 0.9rem;
            transition: 0.3s;
            text-decoration: none;
        }

        .nav-links a:hover {
            color: var(--text-primary);
        }

        .container {
            max-width: 900px;
            margin: 4rem auto;
            padding: 0 5%;
        }

        .header-section {
            text-align: center;
            margin-bottom: 5rem;
        }

        .header-section h1 {
            font-size: 3rem;
            font-weight: 800;
            margin-bottom: 1rem;
        }

        .header-section p {
            color: var(--text-secondary);
            font-size: 1.1rem;
        }

        .news-grid {
            display: grid;
            gap: 2rem;
        }

        .news-card {
            background: var(--card-bg);
            border: 1px solid var(--border-color);
            border-radius: 20px;
            padding: 2.5rem;
            transition: 0.3s;
        }

        .news-card:hover {
            transform: translateY(-5px);
            border-color: rgba(44, 15, 189, 0.4);
            background: rgba(27, 24, 43, 0.8);
        }

        .meta {
            display: flex;
            align-items: center;
            gap: 1rem;
            margin-bottom: 1rem;
            font-size: 0.85rem;
        }

        .tag {
            background: rgba(44, 15, 189, 0.2);
            color: #7c4dff;
            padding: 4px 12px;
            border-radius: 100px;
            font-weight: 700;
            text-transform: uppercase;
            font-size: 0.7rem;
            letter-spacing: 0.5px;
        }

        .date {
            color: var(--text-dim);
        }

        .news-card h2 {
            font-size: 1.8rem;
            margin-bottom: 1rem;
            font-weight: 700;
        }

        .news-card p {
            color: var(--text-secondary);
            margin-bottom: 1.5rem;
        }

        .read-more {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            color: var(--accent-primary);
            font-weight: 700;
            text-decoration: none;
            font-size: 0.9rem;
        }

        .read-more:hover {
            text-decoration: underline;
        }

        footer {
            padding: 4rem 5%;
            border-top: 1px solid var(--border-color);
            text-align: center;
            margin-top: 5rem;
        }
    </style>
</head>

<body>
    <div class="ambient-bg"></div>

    <nav>
        <a href="index.php" class="logo">
            <img src="assets/images/logo.png" alt="SecureVault" style="height: 32px;">
        </a>
        <div class="nav-links">
            <a href="index.php">Home</a>
            <a href="index.php#features">Features</a>
            <a href="index.php#security">Security</a>
            <a href="pricing.php">Pricing</a>
            <a href="news.php" style="color: white;">News</a>
            <?php if ($isLoggedIn): ?>
                <a href="dashboard.php" style="color: var(--accent-primary);">Dashboard</a>
            <?php else: ?>
                <a href="login.php">Login</a>
            <?php endif; ?>
        </div>
        <div class="mobile-menu-btn" onclick="toggleMobileMenu()">
            <i data-lucide="menu"></i>
        </div>
    </nav>

    <div class="container">
        <div class="header-section">
            <h1>Latest Updates</h1>
            <p>Stay informed about product features, security audits, and company news.</p>
        </div>

        <div class="news-grid">
            <!-- News Item 1 -->
            <article class="news-card">
                <div class="meta">
                    <span class="tag">Product Update</span>
                    <span class="date">February 2, 2026</span>
                </div>
                <h2>Launching Mobile OTP & Browser Guard</h2>
                <p>We've rolled out a major security update. Now, any login attempt from an unrecognized browser will
                    require email verification. This adds a critical layer of defense against credential stuffing
                    attacks.</p>
            </article>

            <!-- News Item 2 -->
            <article class="news-card">
                <div class="meta">
                    <span class="tag">Announcement</span>
                    <span class="date">January 20, 2026</span>
                </div>
                <h2>SecureVault is Now Free for Everyone</h2>
                <p>We believe privacy is a fundamental right. That's why we are making the core features of
                    SecureVault—unlimited passwords, cross-device sync, and secure notes—completely free to use for
                    individual users.</p>
            </article>

            <!-- News Item 3 -->
            <article class="news-card">
                <div class="meta">
                    <span class="tag">Feature</span>
                    <span class="date">January 15, 2026</span>
                </div>
                <h2>Introducing Dark Web Monitoring</h2>
                <p>Our new leak detection engine now scans known dark web databases for your exposed credentials. If we
                    find a match, we'll alert you instantly so you can change your passwords before hackers act.</p>
            </article>
        </div>
    </div>

    <footer>
        <p style="color: var(--text-dim);">&copy;
            <?php echo date('Y'); ?> SecureVault. All rights reserved.
        </p>
    </footer>

    <script>
        lucide.createIcons();

        function toggleMobileMenu() {
            const navLinks = document.querySelector('.nav-links');
            const menuBtnIcon = document.querySelector('.mobile-menu-btn i');

            navLinks.classList.toggle('mobile-active');

            // Toggle icon between menu and x
            if (navLinks.classList.contains('mobile-active')) {
                menuBtnIcon.setAttribute('data-lucide', 'x');
            } else {
                menuBtnIcon.setAttribute('data-lucide', 'menu');
            }
            lucide.createIcons();
        }
    </script>
</body>

</html>