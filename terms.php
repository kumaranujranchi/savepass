<?php
session_start();
$isLoggedIn = isset($_SESSION["id"]);
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Terms of Service | SecureVault</title>
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
            background: radial-gradient(circle at 80% 10%, rgba(44, 15, 189, 0.15) 0%, transparent 40%),
                radial-gradient(circle at 20% 80%, rgba(124, 77, 255, 0.1) 0%, transparent 40%);
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

        h1 {
            font-size: 3rem;
            font-weight: 800;
            margin-bottom: 1rem;
        }

        .last-updated {
            color: var(--text-dim);
            font-size: 0.9rem;
            margin-bottom: 3rem;
        }

        .content h2 {
            font-size: 1.8rem;
            margin-top: 3rem;
            margin-bottom: 1rem;
            color: var(--text-primary);
        }

        .content p {
            color: var(--text-secondary);
            margin-bottom: 1.5rem;
            font-size: 1.05rem;
        }

        .content ul {
            list-style: none;
            margin-bottom: 1.5rem;
            padding-left: 1rem;
        }

        .content li {
            position: relative;
            padding-left: 1.5rem;
            margin-bottom: 0.5rem;
            color: var(--text-secondary);
        }

        .content li::before {
            content: "•";
            color: var(--accent-primary);
            position: absolute;
            left: 0;
            font-weight: bold;
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
        <h1>Terms of Service</h1>
        <p class="last-updated">Last Updated:
            <?php echo date("F j, Y"); ?>
        </p>

        <div class="content">
            <h2>1. Agreement to Terms</h2>
            <p>By accessing or using SecureVault, you agree to be bound by these Terms of Service. If you disagree with
                any part of the terms, you may not access the service.</p>

            <h2>2. Description of Service</h2>
            <p>SecureVault provides a zero-knowledge password and digital asset manager. We provide the tools for you to
                encrypt and store your data. We do not have access to your decrypted data.</p>

            <h2>3. User Responsibilities</h2>
            <p>You are responsible for:</p>
            <ul>
                <li><strong>Safeguarding your Master Password:</strong> It is the only key to your vault. We cannot
                    recover it for you.</li>
                <li><strong>Activity on your account:</strong> You are responsible for all actions taken under your
                    account.</li>
                <li><strong>Compliance:</strong> You agree not to use the service for any illegal or unauthorized
                    purpose.</li>
            </ul>

            <h2>4. Limitation of Liability</h2>
            <p>To the maximum extent permitted by law, SecureVault shall not be liable for any indirect, incidental,
                special, consequential, or punitive damages, including without limitation, loss of profits, data, use,
                goodwill, or other intangible losses, resulting from:</p>
            <ul>
                <li>Your access to or use of or inability to access or use the service.</li>
                <li>Any unauthorized access to or use of our servers and/or any personal information stored therein.
                </li>
                <li>Loss of your Master Password.</li>
            </ul>

            <h2>5. Termination</h2>
            <p>We may terminate or suspend access to our service immediately, without prior notice or liability, for any
                reason whatsoever, including without limitation if you breach the Terms.</p>

            <h2>6. Governing Law</h2>
            <p>These Terms shall be governed and construed in accordance with the laws of the jurisdiction in which
                SecureVault operates, without regard to its conflict of law provisions.</p>

            <h2>7. Contact Us</h2>
            <p>If you have any questions about these Terms, please contact us at legal@securevault.com.</p>
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