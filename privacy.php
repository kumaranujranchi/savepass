<?php
session_start();
$isLoggedIn = isset($_SESSION["id"]);
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Privacy Policy | SecureVault</title>
    <!-- Favicon -->
    <link rel="icon" type="image/png" href="assets/images/favicon.png">
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700;800;900&display=swap" rel="stylesheet">
    <!-- Lucide Icons -->
    <script src="https://unpkg.com/lucide@latest"></script>

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
            background: radial-gradient(circle at 10% 10%, rgba(44, 15, 189, 0.15) 0%, transparent 40%),
                radial-gradient(circle at 90% 80%, rgba(124, 77, 255, 0.1) 0%, transparent 40%);
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
            <i data-lucide="shield-check" style="width: 28px; height: 28px; color: #2c0fbd;"></i>
            SecureVault
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
    </nav>

    <div class="container">
        <h1>Privacy Policy</h1>
        <p class="last-updated">Last Updated:
            <?php echo date("F j, Y"); ?>
        </p>

        <div class="content">
            <h2>1. Introduction</h2>
            <p>At SecureVault, your privacy is not just a policy; it's our core product. We interpret "Zero Knowledge"
                literally. We have engineered our systems so that we cannot see, read, or monetize your data, even if we
                wanted to.</p>

            <h2>2. Data We Collect</h2>
            <p>We believe in data minimization. We only collect what is strictly necessary to provide our service:</p>
            <ul>
                <li><strong>Account Information:</strong> Your email address (for authentication and alerts) and a hash
                    of your master password (which we cannot reverse).</li>
                <li><strong>Encrypted Blobs:</strong> Your vault data is uploaded to our servers as encrypted blobs. We
                    do not hold the keys to decrypt this data.</li>
                <li><strong>Log Data:</strong> Minimal server logs for security auditing and debugging, which are
                    regularly rotated and purged.</li>
            </ul>

            <h2>3. How We Use Your Data</h2>
            <p>We use your data solely to:</p>
            <ul>
                <li>Provide and maintain the SecureVault service.</li>
                <li>Notify you of security alerts (e.g., unrecognized logins).</li>
                <li>Prevent abuse and ensure system integrity.</li>
            </ul>
            <p><strong>We do NOT sell, rent, or share your personal data with advertisers or third parties.</strong></p>

            <h2>4. Your Master Password</h2>
            <p>Your Master Password is the key to your vault. <strong>We do not know it, and we cannot reset
                    it.</strong> If you lose your Master Password, you lose access to your data. This is the trade-off
                for true security.</p>

            <h2>5. Data Security</h2>
            <p>We employ industry-standard security measures, including:</p>
            <ul>
                <li><strong>AES-256 Encryption:</strong> Applied to your data locally on your device before it ever
                    reaches our servers.</li>
                <li><strong>TLS/SSL:</strong> All data in transit is encrypted.</li>
                <li><strong>Salted Hashing:</strong> We use strong algorithms to protect your authentication
                    credentials.</li>
            </ul>

            <h2>6. Changes to This Policy</h2>
            <p>We may update this Privacy Policy from time to time. We will notify you of any significant changes via
                email or a prominent notice on our website.</p>

            <h2>7. Contact Us</h2>
            <p>If you have any questions about this Privacy Policy, please contact us at privacy@securevault.com.</p>
        </div>
    </div>

    <footer>
        <p style="color: var(--text-dim);">&copy;
            <?php echo date('Y'); ?> SecureVault. All rights reserved.
        </p>
    </footer>

    <script>
        lucide.createIcons();
    </script>
</body>

</html>