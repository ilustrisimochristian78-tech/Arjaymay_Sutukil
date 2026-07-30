<?php
require_once 'config/database.php';

echo "Fixing database issues...\n";

$pdo = getDBConnection();

// Reset any stuck transactions
$pdo->exec("ROLLBACK");

// Cancel stuck pending orders (older than 5 minutes)
$stmt = $pdo->prepare("
    UPDATE orders 
    SET status = 'cancelled' 
    WHERE status = 'pending' 
    AND created_at < DATE_SUB(NOW(), INTERVAL 5 MINUTE)
");
$stmt->execute();
echo "Cancelled " . $stmt->rowCount() . " stuck orders\n";

// Fix negative stock
$stmt = $pdo->prepare("UPDATE menu_items SET stock = 0 WHERE stock < 0");
$stmt->execute();
echo "Fixed " . $stmt->rowCount() . " negative stock items\n";

// Reset any failed transactions
echo "Database fixed successfully!\n";
?>