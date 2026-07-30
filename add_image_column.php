<?php
// add_image_column.php - Run once then delete
require_once 'config/database.php';

try {
    $pdo = getDBConnection();
    $pdo->exec("ALTER TABLE menu_items ADD COLUMN image VARCHAR(255) DEFAULT NULL");
    echo "✅ Image column added successfully!";
} catch (PDOException $e) {
    if (strpos($e->getMessage(), 'Duplicate column') !== false) {
        echo "ℹ️ Image column already exists.";
    } else {
        echo "❌ Error: " . $e->getMessage();
    }
}
?>