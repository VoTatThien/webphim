<?php
// Script to execute the database upgrade SQL
// Uses the same PDO connection config as the project

// Smart environment detection (same as pdo.php)
$isLocal = (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN');
if ($isLocal) {
    $db_host = 'localhost';
    $db_port = '3307';
    $db_name = 'cinepass';
    $db_user = 'root';
    $db_pass = '';
} else {
    $db_host = 'localhost';
    $db_port = '3306';
    $db_name = 'cinepass';
    $db_user = 'root';
    $db_pass = '';
}

try {
    $pdo = new PDO("mysql:host=$db_host;port=$db_port;dbname=$db_name;charset=utf8mb4", $db_user, $db_pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    echo "✅ Connected to database successfully.\n";

    $sql = file_get_contents(__DIR__ . '/DB/db_upgrade_8features.sql');

    // Remove comments
    $sql = preg_replace('/--.*$/m', '', $sql);
    // Split into individual statements
    $statements = array_filter(array_map('trim', explode(';', $sql)));

    $success = 0;
    $skipped = 0;
    foreach ($statements as $stmt) {
        if (empty($stmt)) continue;
        try {
            $pdo->exec($stmt);
            $success++;
            // Show first 80 chars of the statement
            echo "✅ OK: " . substr(preg_replace('/\s+/', ' ', $stmt), 0, 80) . "...\n";
        } catch (PDOException $e) {
            if (strpos($e->getMessage(), 'already exists') !== false || strpos($e->getMessage(), 'Duplicate') !== false) {
                $skipped++;
                echo "⏭️ SKIPPED (already exists): " . substr(preg_replace('/\s+/', ' ', $stmt), 0, 60) . "...\n";
            } else {
                echo "❌ ERROR: " . $e->getMessage() . "\n";
                echo "   Statement: " . substr(preg_replace('/\s+/', ' ', $stmt), 0, 100) . "...\n";
            }
        }
    }

    echo "\n🎯 RESULT: $success succeeded, $skipped skipped.\n";

    // Verify tables were created
    $tables_check = ['su_co', 'claimed_missions'];
    foreach ($tables_check as $t) {
        $check = $pdo->query("SHOW TABLES LIKE '$t'")->fetchColumn();
        echo ($check ? "✅" : "❌") . " Table '$t': " . ($check ? "EXISTS" : "MISSING") . "\n";
    }

    // Verify columns on ve table
    $cols = $pdo->query("SHOW COLUMNS FROM `ve` LIKE 'fb_check_in%'")->fetchAll(PDO::FETCH_COLUMN);
    echo "✅ ve table F&B columns: " . implode(', ', $cols) . "\n";

} catch (PDOException $e) {
    echo "❌ Connection failed: " . $e->getMessage() . "\n";
}
