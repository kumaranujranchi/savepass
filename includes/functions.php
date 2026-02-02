<?php
// Function to encrypt data
function encryptData($data)
{
    $key = ENCRYPTION_KEY;
    $ivLength = openssl_cipher_iv_length(CIPHER_METHOD);
    $iv = openssl_random_pseudo_bytes($ivLength);
    $encrypted = openssl_encrypt($data, CIPHER_METHOD, $key, 0, $iv);
    // Return IV + Encrypted Data as base64
    return base64_encode($iv . $encrypted);
}

// Function to decrypt data
function decryptData($data)
{
    $key = ENCRYPTION_KEY;
    $data = base64_decode($data);
    $ivLength = openssl_cipher_iv_length(CIPHER_METHOD);
    $iv = substr($data, 0, $ivLength);
    $encrypted = substr($data, $ivLength);
    return openssl_decrypt($encrypted, CIPHER_METHOD, $key, 0, $iv);
}

// Function to check if user is logged in
function checkAuth()
{
    if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true) {
        header("location: login.php");
        exit;
    }
}

// Function to sanitize input
function cleanInput($data)
{
    return htmlspecialchars(stripslashes(trim($data)));
}

// Format date nicely
function formatDate($date)
{
    return date("M d, Y", strtotime($date));
}

// Get icon based on service name
function getServiceIcon($appName)
{
    $name = strtolower($appName);

    // Exact matches or strict contains
    if (strpos($name, 'facebook') !== false)
        return 'facebook';
    if (strpos($name, 'instagram') !== false)
        return 'instagram';
    if (strpos($name, 'twitter') !== false || strpos($name, 'x.com') !== false)
        return 'twitter';
    if (strpos($name, 'linkedin') !== false)
        return 'linkedin';
    if (strpos($name, 'github') !== false)
        return 'github';
    if (strpos($name, 'google') !== false || strpos($name, 'gmail') !== false)
        return 'chrome';
    if (strpos($name, 'apple') !== false || strpos($name, 'icloud') !== false)
        return 'apple';
    if (strpos($name, 'amazon') !== false)
        return 'shopping-cart';
    if (strpos($name, 'netflix') !== false)
        return 'tv';
    if (strpos($name, 'spotify') !== false)
        return 'music';
    if (strpos($name, 'slack') !== false)
        return 'slack';
    if (strpos($name, 'discord') !== false)
        return 'gamepad-2';
    if (strpos($name, 'youtube') !== false)
        return 'youtube';
    if (strpos($name, 'twitch') !== false)
        return 'twitch';
    if (strpos($name, 'figma') !== false)
        return 'figma';
    if (strpos($name, 'dribbble') !== false)
        return 'dribbble';
    if (strpos($name, 'dropbox') !== false)
        return 'box';
    if (strpos($name, 'bank') !== false || strpos($name, 'pay') !== false)
        return 'credit-card';
    if (strpos($name, 'mail') !== false || strpos($name, 'outlook') !== false)
        return 'mail';

    // Default fallback
    return 'globe';
}
?>