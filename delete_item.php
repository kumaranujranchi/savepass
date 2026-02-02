<?php
// delete_item.php
session_start();
require_once "config/db.php";

// Check if user is logged in
if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true) {
    header("location: login.php");
    exit;
}

if ($_SERVER["REQUEST_METHOD"] == "GET" && isset($_GET["id"])) {
    $item_id = intval($_GET["id"]);
    $user_id = $_SESSION["id"];

    try {
        // Prepare statement to delete the item BUT ONLY if it belongs to the current user
        $sql = "DELETE FROM vault_items WHERE id = :id AND user_id = :user_id";
        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(":id", $item_id, PDO::PARAM_INT);
        $stmt->bindParam(":user_id", $user_id, PDO::PARAM_INT);

        if ($stmt->execute()) {
            if ($stmt->rowCount() > 0) {
                // Deletion successful
                header("location: passwords.php?msg=deleted");
            } else {
                // Item not found or doesn't belong to user
                header("location: passwords.php?err=not_found");
            }
        } else {
            header("location: passwords.php?err=db_error");
        }
    } catch (PDOException $e) {
        // Log error (optional) and redirect
        header("location: passwords.php?err=exception");
    }
} else {
    // Invalid request
    header("location: passwords.php");
}
?>