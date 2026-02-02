<?php
session_start();
require_once "includes/functions.php";

// If already logged in, show a different CTA or redirect
$isLoggedIn = isset($_SESSION["id"]);
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SecureVault | Your Premium Digital Vault</title>
    <!-- Favicon -->
    <link rel="icon" type="image/png" href="assets/images/favicon.png">
    <!-- SEO Meta Tags -->
    <meta name="description"
        content="SecureVault is a premium, zero-knowledge password and API key manager. Secure your digital life with AES-256 encryption and advanced leak detection.">

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
            overflow-x: hidden;
            line-height: 1.6;
        }

        /* Ambient Background Background */
        .ambient-bg {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            z-index: -1;
            background:
                radial-gradient(circle at 10% 10%, rgba(44, 15, 189, 0.15) 0%, transparent 40%),
                radial-gradient(circle at 90% 80%, rgba(124, 77, 255, 0.1) 0%, transparent 40%);
        }

        /* Navbar */
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

        .btn-nav {
            background: var(--accent-primary);
            color: white !important;
            padding: 0.6rem 1.5rem;
            border-radius: 12px;
            font-weight: 700;
            box-shadow: 0 4px 15px var(--accent-glow);
        }

        /* Hero Section */
        header {
            padding: 8rem 5% 4rem;
            text-align: center;
            max-width: 1000px;
            margin: 0 auto;
        }

        .badge {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            background: rgba(44, 15, 189, 0.1);
            border: 1px solid rgba(44, 15, 189, 0.3);
            color: #7c4dff;
            padding: 6px 16px;
            border-radius: 100px;
            font-size: 0.8rem;
            font-weight: 700;
            margin-bottom: 2rem;
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        h1 {
            font-size: clamp(2.5rem, 6vw, 4.5rem);
            font-weight: 900;
            line-height: 1.1;
            margin-bottom: 1.5rem;
            letter-spacing: -2px;
        }

        h1 span {
            background: linear-gradient(135deg, #fff 0%, var(--accent-primary) 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }

        .hero-desc {
            font-size: 1.25rem;
            color: var(--text-secondary);
            margin-bottom: 3rem;
            max-width: 700px;
            margin-left: auto;
            margin-right: auto;
        }

        .cta-group {
            display: flex;
            gap: 1.5rem;
            justify-content: center;
            flex-wrap: wrap;
        }

        .btn-large {
            padding: 1.25rem 3rem;
            border-radius: 16px;
            font-weight: 800;
            text-decoration: none;
            transition: 0.3s;
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .btn-primary {
            background: var(--accent-primary);
            color: white;
            box-shadow: 0 10px 40px var(--accent-glow);
        }

        .btn-primary:hover {
            transform: translateY(-3px);
            background: #3e1fd6;
        }

        .btn-secondary {
            background: rgba(255, 255, 255, 0.05);
            border: 1px solid var(--border-color);
            color: white;
        }

        .btn-secondary:hover {
            background: rgba(255, 255, 255, 0.1);
        }

        /* Features Section */
        .section-padding {
            padding: 6rem 5%;
        }

        .section-header {
            text-align: center;
            margin-bottom: 4rem;
        }

        .section-header h2 {
            font-size: 2.5rem;
            font-weight: 800;
            margin-bottom: 1rem;
        }

        .section-header p {
            color: var(--text-secondary);
            max-width: 600px;
            margin: 0 auto;
        }

        .features-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 2rem;
            max-width: 1200px;
            margin: 0 auto;
        }

        .feature-card {
            background: rgba(19, 16, 34, 0.6);
            border: 1px solid var(--border-color);
            padding: 2.5rem;
            border-radius: 24px;
            transition: 0.3s;
        }

        .feature-card:hover {
            transform: translateY(-10px);
            border-color: rgba(44, 15, 189, 0.4);
            background: rgba(27, 24, 43, 0.8);
        }

        .feature-icon {
            width: 60px;
            height: 60px;
            background: rgba(44, 15, 189, 0.2);
            border-radius: 16px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 1.5rem;
            color: var(--accent-primary);
        }

        .feature-card h3 {
            font-size: 1.5rem;
            margin-bottom: 1rem;
        }

        .feature-card p {
            color: var(--text-secondary);
            font-size: 0.95rem;
        }

        /* Trust & Security */
        .security-banner {
            background: linear-gradient(90deg, rgba(44, 15, 189, 0.1) 0%, rgba(10, 11, 16, 0.1) 100%);
            border-top: 1px solid var(--border-color);
            border-bottom: 1px solid var(--border-color);
            padding: 4rem 5%;
            display: flex;
            align-items: center;
            gap: 4rem;
            flex-wrap: wrap;
            justify-content: center;
        }

        .security-item {
            display: flex;
            align-items: center;
            gap: 1.5rem;
            max-width: 400px;
        }

        .security-item i {
            color: var(--accent-primary);
        }

        .security-item h4 {
            font-size: 1.1rem;
            margin-bottom: 4px;
        }

        .security-item p {
            font-size: 0.85rem;
            color: var(--text-dim);
        }

        /* Footer */
        footer {
            padding: 4rem 5%;
            border-top: 1px solid var(--border-color);
            text-align: center;
        }

        .footer-logo {
            font-size: 1.5rem;
            font-weight: 900;
            margin-bottom: 1rem;
        }

        .footer-links {
            display: flex;
            gap: 2rem;
            justify-content: center;
            margin-bottom: 2rem;
            list-style: none;
        }

        .footer-links a {
            color: var(--text-dim);
            transition: 0.3s;
        }

        .footer-links a:hover {
            color: white;
        }

        .copyright {
            color: #444;
            font-size: 0.8rem;
        }

        /* Responsive */
        @media (max-width: 768px) {
            h1 {
                font-size: 2.5rem;
            }

            .cta-group {
                flex-direction: column;
            }

            .security-banner {
                gap: 2rem;
            }
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
            <a href="#features">Features</a>
            <a href="#security">Security</a>
            <a href="pricing.php">Pricing</a>
            <a href="news.php">News</a>
            <?php if ($isLoggedIn): ?>
                <a href="dashboard.php" class="btn-nav">Dashboard</a>
            <?php else: ?>
                <a href="login.php">Login</a>
                <a href="register.php" class="btn-nav">Get Started</a>
            <?php endif; ?>
        </div>
        <div class="mobile-menu-btn" onclick="toggleMobileMenu()">
            <i data-lucide="menu"></i>
        </div>
    </nav>

    <header>
        <div class="badge">
            <i data-lucide="award" style="width: 14px; height: 14px;"></i>
            Premium Security Suite
        </div>
        <h1>Your Entire Digital Life, <span>Secured.</span> <br>Not Just Stored.</h1>
        <p class="hero-desc">
            The world's most elegant, zero-knowledge vault for your passwords, API keys, and sensitive notes. Built for
            developers and security enthusiasts.
        </p>
        <div class="cta-group">
            <a href="register.php" class="btn-large btn-primary">
                Explore Your Vault
                <i data-lucide="arrow-right"></i>
            </a>
            <a href="#features" class="btn-large btn-secondary">See Features</a>
        </div>
    </header>

    <section id="features" class="section-padding">
        <div class="section-header">
            <h2>Everything you need. In one place.</h2>
            <p>Designed to save you time and provide peace of mind in an increasingly complex digital world.</p>
        </div>
        <div class="features-grid">
            <div class="feature-card">
                <div class="feature-icon"><i data-lucide="lock"></i></div>
                <h3>Password Manager</h3>
                <p>Store unlimited passwords with AES-256 military-grade encryption. Generate strong, unique passwords
                    with our built-in generator.</p>
            </div>
            <div class="feature-card">
                <div class="feature-icon"><i data-lucide="code"></i></div>
                <h3>API Key Manager</h3>
                <p>The first vault built for developers. Manage your API keys for different environments without ever
                    exposing them to your codebase.</p>
            </div>
            <div class="feature-card">
                <div class="feature-icon"><i data-lucide="file-text"></i></div>
                <h3>Secure Notes</h3>
                <p>Keep your most private thoughts, recovery codes, and sensitive documents behind our double-encryption
                    layer.</p>
            </div>
            <div class="feature-card">
                <div class="feature-icon"><i data-lucide="browser"></i></div>
                <h3>Chrome Extension</h3>
                <p>Stop manual copying. Our companion extension detects logins and saves passwords to your vault with
                    just one click.</p>
            </div>
            <div class="feature-card">
                <div class="feature-icon"><i data-lucide="shield-alert"></i></div>
                <h3>Leak Detection</h3>
                <p>We proactively check if your credentials have been part of any global breaches using
                    privacy-preserving k-anonymity checks.</p>
            </div>
            <div class="feature-card">
                <div class="feature-icon"><i data-lucide="zap"></i></div>
                <h3>Instant Productivity</h3>
                <p>Keyboard shortcuts, quick-search (Cmd+K), and intuitive UI built to speed up your workflow
                    significantly.</p>
            </div>
        </div>
    </section>

    <section id="security" class="security-banner">
        <div class="security-item">
            <i data-lucide="eye-off" style="width: 48px; height: 48px;"></i>
            <div>
                <h4>Zero-Knowledge</h4>
                <p>We never see your passwords. Decryption happens only on your device using your Master Key.</p>
            </div>
        </div>
        <div class="security-item">
            <i data-lucide="database" style="width: 48px; height: 48px;"></i>
            <div>
                <h4>AES-256 Encryption</h4>
                <p>Your data is encrypted using the industry-standard AES-256 algorithm before touching our servers.</p>
            </div>
        </div>
        <div class="security-item">
            <i data-lucide="shield-check" style="width: 48px; height: 48px;"></i>
            <div>
                <h4>New Browser Guard</h4>
                <p>Login from an unrecognized browser? We'll require an email OTP to ensure it's actually you.</p>
            </div>
        </div>
        <div class="security-item">
            <i data-lucide="timer" style="width: 48px; height: 48px;"></i>
            <div>
                <h4>Auto-Lock & Protection</h4>
                <p>Variable auto-lock timers and clipboard clearing ensure your vault stays inaccessible to others.</p>
            </div>
        </div>
    </section>

    <section class="section-padding" style="text-align: center;">
        <div
            style="background: rgba(44, 15, 189, 0.05); padding: 4rem; border-radius: 40px; border: 1px solid rgba(44, 15, 189, 0.2); max-width: 900px; margin: 0 auto;">
            <h2 style="font-size: 2.5rem; margin-bottom: 1.5rem; font-weight: 800;">Trust is built on Transparency.</h2>
            <p style="color: var(--text-secondary); margin-bottom: 2.5rem; font-size: 1.1rem;">
                Unlike traditional managers, SecureVault is built with a focus on privacy-preserving technology. Even if
                our servers are breached, your data remains an unreadable pile of encrypted fragments.
            </p>
            <a href="register.php" class="btn-large btn-primary" style="display: inline-flex;">Create Your Free
                Vault</a>
        </div>
    </section>

    <footer>
        <div class="logo footer-logo" style="justify-content: center;">
            <img src="assets/images/logo.png" alt="SecureVault" style="height: 28px;">
        </div>
        <ul class="footer-links">
            <li><a href="privacy.php">Privacy Policy</a></li>
            <li><a href="terms.php">Terms of Service</a></li>
            <li><a href="news.php">News</a></li>
            <li><a href="pricing.php">Pricing</a></li>
            <li><a href="login.php">Login</a></li>
            <li><a href="register.php">Register</a></li>
        </ul>
        <p class="copyright">&copy; <?php echo date('Y'); ?> SecureVault. All rights reserved. Handcrafted for Security.
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