<?php
// Database credentials template
// Rename this file to db.php and update with your local/server credentials

define('DB_SERVER', 'localhost');
define('DB_USERNAME', 'your_username');
define('DB_PASSWORD', 'your_password');
define('DB_NAME', 'your_db_name');

/* Attempt to connect to MySQL database */
try {
    $pdo = new PDO("mysql:host=" . DB_SERVER . ";dbname=" . DB_NAME, DB_USERNAME, DB_PASSWORD);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("ERROR: Could not connect. " . $e->getMessage());
}

// Encryption Key (This should be a unique 32-byte string)
define('ENCRYPTION_KEY', 'CHANGE_ME_TO_A_SECURE_RANDOM_KEY');
define('CIPHER_METHOD', 'AES-256-CBC');
?>