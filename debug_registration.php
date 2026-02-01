<?php
// Debug script to check registration issues
require_once "config/db.php";

echo "<h2>Registration Debug Report</h2>";
echo "<style>body{font-family:Arial;padding:20px;background:#f5f5f5;} .box{background:white;padding:15px;margin:10px 0;border-radius:8px;box-shadow:0 2px 4px rgba(0,0,0,0.1);} .success{border-left:4px solid #4CAF50;} .error{border-left:4px solid #f44336;} .info{border-left:4px solid #2196F3;} pre{background:#f5f5f5;padding:10px;border-radius:4px;overflow-x:auto;}</style>";

// 1. Check database connection
echo "<div class='box info'>";
echo "<h3>1. Database Connection</h3>";
try {
    $pdo->query("SELECT 1");
    echo "<p style='color:green;'>✓ Database connection successful</p>";
    echo "<p>Server: " . DB_SERVER . "</p>";
    echo "<p>Database: " . DB_NAME . "</p>";
} catch (PDOException $e) {
    echo "<p style='color:red;'>✗ Database connection failed: " . $e->getMessage() . "</p>";
}
echo "</div>";

// 2. Check if users table exists
echo "<div class='box info'>";
echo "<h3>2. Users Table Check</h3>";
try {
    $stmt = $pdo->query("DESCRIBE users");
    echo "<p style='color:green;'>✓ Users table exists</p>";
    echo "<pre>";
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        echo $row['Field'] . " - " . $row['Type'] . "\n";
    }
    echo "</pre>";
} catch (PDOException $e) {
    echo "<p style='color:red;'>✗ Users table error: " . $e->getMessage() . "</p>";
}
echo "</div>";

// 3. List all existing users
echo "<div class='box info'>";
echo "<h3>3. Existing Users</h3>";
try {
    $stmt = $pdo->query("SELECT id, email, created_at FROM users ORDER BY created_at DESC");
    $users = $stmt->fetchAll(PDO::FETCH_ASSOC);

    if (count($users) > 0) {
        echo "<p>Total users: " . count($users) . "</p>";
        echo "<table style='width:100%;border-collapse:collapse;'>";
        echo "<tr style='background:#f0f0f0;'><th style='padding:8px;text-align:left;'>ID</th><th style='padding:8px;text-align:left;'>Email</th><th style='padding:8px;text-align:left;'>Created At</th></tr>";
        foreach ($users as $user) {
            echo "<tr style='border-bottom:1px solid #ddd;'>";
            echo "<td style='padding:8px;'>" . $user['id'] . "</td>";
            echo "<td style='padding:8px;'>" . $user['email'] . "</td>";
            echo "<td style='padding:8px;'>" . $user['created_at'] . "</td>";
            echo "</tr>";
        }
        echo "</table>";
    } else {
        echo "<p>No users found in database</p>";
    }
} catch (PDOException $e) {
    echo "<p style='color:red;'>✗ Error fetching users: " . $e->getMessage() . "</p>";
}
echo "</div>";

// 4. Test email check query
echo "<div class='box info'>";
echo "<h3>4. Test Email Availability Check</h3>";
$test_emails = ['test@example.com', 'newuser@example.com', 'admin@example.com'];
foreach ($test_emails as $test_email) {
    try {
        $sql = "SELECT id FROM users WHERE email = :email";
        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(":email", $test_email, PDO::PARAM_STR);
        $stmt->execute();

        if ($stmt->rowCount() == 1) {
            echo "<p style='color:orange;'>⚠ Email '$test_email' is already taken</p>";
        } else {
            echo "<p style='color:green;'>✓ Email '$test_email' is available</p>";
        }
    } catch (PDOException $e) {
        echo "<p style='color:red;'>✗ Error checking '$test_email': " . $e->getMessage() . "</p>";
    }
}
echo "</div>";

// 5. Test registration simulation
echo "<div class='box info'>";
echo "<h3>5. Registration Test Simulation</h3>";
echo "<p>This simulates what happens when you try to register with a new email</p>";
$test_new_email = "debugtest_" . time() . "@example.com";
$test_password = "test_password_hash_123";

try {
    // Check if email exists
    $sql = "SELECT id FROM users WHERE email = :email";
    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(":email", $test_new_email, PDO::PARAM_STR);
    $stmt->execute();

    if ($stmt->rowCount() == 1) {
        echo "<p style='color:orange;'>⚠ Email would be rejected (already exists)</p>";
    } else {
        echo "<p style='color:green;'>✓ Email check passed</p>";

        // Try to insert (we'll rollback)
        $pdo->beginTransaction();
        try {
            $sql = "INSERT INTO users (email, password_hash) VALUES (:email, :password)";
            $stmt = $pdo->prepare($sql);
            $stmt->bindParam(":email", $test_new_email, PDO::PARAM_STR);
            $stmt->bindParam(":password", $test_password, PDO::PARAM_STR);

            if ($stmt->execute()) {
                echo "<p style='color:green;'>✓ Test insert successful (will be rolled back)</p>";
                $pdo->rollBack();
                echo "<p style='color:blue;'>ℹ Transaction rolled back - no data was actually inserted</p>";
            }
        } catch (PDOException $e) {
            $pdo->rollBack();
            echo "<p style='color:red;'>✗ Test insert failed: " . $e->getMessage() . "</p>";
        }
    }
} catch (PDOException $e) {
    echo "<p style='color:red;'>✗ Simulation error: " . $e->getMessage() . "</p>";
}
echo "</div>";

// 6. Check PHP error log settings
echo "<div class='box info'>";
echo "<h3>6. PHP Configuration</h3>";
echo "<p>Error Reporting: " . (error_reporting() ? "Enabled" : "Disabled") . "</p>";
echo "<p>Display Errors: " . ini_get('display_errors') . "</p>";
echo "<p>Log Errors: " . ini_get('log_errors') . "</p>";
echo "<p>Error Log File: " . ini_get('error_log') . "</p>";
echo "</div>";

echo "<div class='box success'>";
echo "<h3>Summary</h3>";
echo "<p>Upload this file to your server and access it via browser to see the debug report.</p>";
echo "<p>This will help identify why registration is failing.</p>";
echo "</div>";
?>