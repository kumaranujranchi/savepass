<?php
// Database credentials
define('DB_SERVER', 'localhost');
define('DB_USERNAME', 'root');
define('DB_PASSWORD', 'root'); // Default for MAMP, might be '' for XAMPP
define('DB_NAME', 'securevault');

/* Attempt to connect to MySQL database */
try {
    $pdo = new PDO("mysql:host=" . DB_SERVER . ";dbname=" . DB_NAME, DB_USERNAME, DB_PASSWORD);
    // Set the PDO error mode to exception
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch(PDOException $e) {
    die("ERROR: Could not connect. " . $e->getMessage());
}

// Encryption Key (In production, stored in ENV variables)
// This is a 32-byte key for AES-256
define('ENCRYPTION_KEY', 'v3rYsEcUr3K3yF0rD3m0PurP0s3sOnLy!'); 
define('CIPHER_METHOD', 'AES-256-CBC');
?>
