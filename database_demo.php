<?php

declare(strict_types=1);

/**
 * Laravel-Style Database Configuration Demo
 * 
 * This script demonstrates how the new Laravel-style database configuration works.
 * Just change DATABASE_CONNECTION in your .env file and the system adapts automatically!
 */

// Load environment variables (in real app, this would be done by your framework)
if (file_exists('.env')) {
    $lines = file('.env', FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($lines as $line) {
        if (strpos($line, '#') === 0) continue; // Skip comments
        [$key, $value] = explode('=', $line, 2);
        $_ENV[trim($key)] = trim($value);
    }
}

// Load the modern database class
require_once 'src/Config/Database.php';
use Swifty\Config\Database;

echo "=== Laravel-Style Database Configuration Demo ===\n\n";

try {
    // Create database connection using Laravel-style environment variables
    $database = Database::fromEnv();
    $connection = $database->connect();
    
    $driver = $database->getDriver();
    echo "✅ Successfully connected to: {$driver}\n";
    
    // Show configuration details
    echo "\n--- Configuration Details ---\n";
    echo "Database Driver: {$driver}\n";
    
    if ($database->isSqlite()) {
        echo "SQLite File: Automatically using db.sqlite in project root\n";
        echo "🎉 Zero configuration needed for SQLite!\n";
    } elseif ($database->isMysql()) {
        echo "MySQL Host: " . ($_ENV['DB_HOST'] ?? 'localhost') . "\n";
        echo "MySQL Port: " . ($_ENV['DB_PORT'] ?? '3306') . "\n";
        echo "MySQL Database: " . ($_ENV['DB_NAME'] ?? 'swifty') . "\n";
    } elseif ($database->isPostgresql()) {
        echo "PostgreSQL Host: " . ($_ENV['DB_HOST'] ?? 'localhost') . "\n";
        echo "PostgreSQL Port: " . ($_ENV['DB_PORT'] ?? '5432') . "\n";
        echo "PostgreSQL Database: " . ($_ENV['DB_NAME'] ?? 'swifty') . "\n";
    }
    
    echo "\n--- Laravel-Style Features ---\n";
    echo "• Convention over configuration ✅\n";
    echo "• Automatic driver detection ✅\n";
    echo "• Zero-config SQLite setup ✅\n";
    echo "• Sensible defaults ✅\n";
    echo "• Backward compatibility ✅\n";
    
    echo "\n--- Usage Examples ---\n";
    echo "SQLite:     DATABASE_CONNECTION=sqlite (that's it!)\n";
    echo "MySQL:      DATABASE_CONNECTION=mysql + credentials\n";
    echo "PostgreSQL: DATABASE_CONNECTION=pgsql + credentials\n";
    
} catch (Exception $e) {
    echo "❌ Connection failed: " . $e->getMessage() . "\n";
    echo "\n--- Troubleshooting ---\n";
    echo "1. Check your .env file configuration\n";
    echo "2. Ensure database server is running (for MySQL/PostgreSQL)\n";
    echo "3. For SQLite, ensure db.sqlite file exists\n";
    echo "4. Verify credentials are correct\n";
}

echo "\n=== End Demo ===\n";