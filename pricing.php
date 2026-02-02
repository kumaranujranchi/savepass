<?php
session_start();
$isLoggedIn = isset($_SESSION["id"]);
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pricing | SecureVault</title>
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
            background: linear-gradient(180deg, rgba(44, 15, 189, 0.1) 0%, rgba(10, 11, 16, 0) 100%);
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
            max-width: 1100px;
            margin: 4rem auto;
            padding: 0 5%;
            text-align: center;
        }

        .header-section {
            margin-bottom: 4rem;
        }

        .header-section h1 {
            font-size: 3.5rem;
            font-weight: 800;
            margin-bottom: 1rem;
            letter-spacing: -1px;
        }

        .header-section p {
            color: var(--text-secondary);
            font-size: 1.25rem;
            max-width: 600px;
            margin: 0 auto;
        }

        .pricing-card {
            background: rgba(19, 16, 34, 0.6);
            border: 1px solid var(--border-color);
            border-radius: 30px;
            padding: 3rem;
            max-width: 500px;
            margin: 0 auto;
            position: relative;
            overflow: hidden;
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.5);
        }

        .pricing-card::before {
            content: "";
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: linear-gradient(180deg, rgba(44, 15, 189, 0.1) 0%, transparent 50%);
            z-index: 0;
            pointer-events: none;
        }

        .plan-name {
            font-size: 0.9rem;
            text-transform: uppercase;
            letter-spacing: 2px;
            font-weight: 800;
            color: #7c4dff;
            margin-bottom: 1rem;
        }

        .price {
            font-size: 5rem;
            font-weight: 900;
            line-height: 1;
            margin-bottom: 1.5rem;
            color: white;
            position: relative;
            z-index: 1;
        }

        .price span {
            font-size: 1.5rem;
            font-weight: 600;
            color: var(--text-dim);
        }

        .features-list {
            list-style: none;
            text-align: left;
            margin: 2rem 0;
            position: relative;
            z-index: 1;
        }

        .features-list li {
            padding: 0.8rem 0;
            color: var(--text-secondary);
            display: flex;
            align-items: center;
            gap: 12px;
            font-size: 1.05rem;
        }

        .features-list li i {
            color: var(--accent-primary);
        }

        .btn-primary {
            display: inline-flex;
            width: 100%;
            justify-content: center;
            background: var(--accent-primary);
            color: white;
            padding: 1.2rem;
            border-radius: 16px;
            font-weight: 800;
            text-decoration: none;
            transition: 0.3s;
            box-shadow: 0 10px 40px var(--accent-glow);
            position: relative;
            z-index: 1;
        }

        .btn-primary:hover {
            transform: translateY(-3px);
            background: #3e1fd6;
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
            <a href="pricing.php" style="color: white;">Pricing</a>
            <a href="news.php">News</a>
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
            <h1>Simple, Honest Pricing.</h1>
            <p>We believe basic security is a human right. That's why SecureVault is completely free to use.</p>
        </div>

        <div class="pricing-card">
            <div class="plan-name">Community Edition</div>
            <div class="price">Free<span>/forever</span></div>

            <ul class="features-list">
                <li><i data-lucide="check-circle" style="width: 20px;"></i> Unlimited Passwords</li>
                <li><i data-lucide="check-circle" style="width: 20px;"></i> Unlimited Devices</li>
                <li><i data-lucide="check-circle" style="width: 20px;"></i> Secure Notes</li>
                <li><i data-lucide="check-circle" style="width: 20px;"></i> Cross-Device Sync</li>
                <li><i data-lucide="check-circle" style="width: 20px;"></i> Basic Breach Alerts</li>
                <li><i data-lucide="check-circle" style="width: 20px;"></i> Zero-Knowledge Encryption</li>
            </ul>

            <a href="register.php" class="btn-primary">Start Securing Now</a>
        </div>

        <p style="margin-top: 3rem; color: var(--text-dim); font-size: 0.9rem;">
            * Premium enterprise plans with team management and SSO are coming soon.
        </p>
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