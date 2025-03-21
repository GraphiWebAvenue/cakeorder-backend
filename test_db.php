<?php
require 'config.php';

try {
    $stmt = $pdo->query("SELECT 'Database connection successful!' AS message");
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    echo $row['message'];
} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
}
?>
