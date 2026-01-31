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
?>