<?php

declare(strict_types=1);

/**
 * Laravel Database Configuration Demo
 * 
 * This script demonstrates how the Laravel-compatible database configuration works.
 * Uses Laravel's exact environment variable format!
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

echo "=== Laravel Database Configuration Demo ===\n\n";

try {
    // Create database connection using Laravel's exact environment variables
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
        echo "MySQL Host: " . ($_ENV['DB_HOST'] ?? '127.0.0.1') . "\n";
        echo "MySQL Port: " . ($_ENV['DB_PORT'] ?? '3306') . "\n";
        echo "MySQL Database: " . ($_ENV['DB_DATABASE'] ?? 'swifty') . "\n";
    } elseif ($database->isPostgresql()) {
        echo "PostgreSQL Host: " . ($_ENV['DB_HOST'] ?? '127.0.0.1') . "\n";
        echo "PostgreSQL Port: " . ($_ENV['DB_PORT'] ?? '5432') . "\n";
        echo "PostgreSQL Database: " . ($_ENV['DB_DATABASE'] ?? 'swifty') . "\n";
    }
    
    echo "\n--- Laravel Compatibility Features ---\n";
    echo "• Uses Laravel's exact environment variables ✅\n";
    echo "• DB_CONNECTION instead of DATABASE_CONNECTION ✅\n";
    echo "• DB_DATABASE instead of DB_NAME ✅\n";
    echo "• DB_HOST defaults to 127.0.0.1 ✅\n";
    echo "• Zero-config SQLite setup ✅\n";
    echo "• Single .env file approach ✅\n";
    
    echo "\n--- Laravel Format Examples ---\n";
    echo "SQLite:     DB_CONNECTION=sqlite (that's it!)\n";
    echo "MySQL:      DB_CONNECTION=mysql + standard Laravel variables\n";
    echo "PostgreSQL: DB_CONNECTION=pgsql + standard Laravel variables\n";
    
    echo "\n--- Environment Variables Used ---\n";
    echo "DB_CONNECTION=" . ($_ENV['DB_CONNECTION'] ?? 'mysql') . "\n";
    if ($database->isMysql() || $database->isPostgresql()) {
        echo "DB_HOST=" . ($_ENV['DB_HOST'] ?? '127.0.0.1') . "\n";
        echo "DB_PORT=" . ($_ENV['DB_PORT'] ?? ($database->isMysql() ? '3306' : '5432')) . "\n";
        echo "DB_DATABASE=" . ($_ENV['DB_DATABASE'] ?? 'swifty') . "\n";
        echo "DB_USERNAME=" . ($_ENV['DB_USERNAME'] ?? ($database->isMysql() ? 'root' : 'postgres')) . "\n";
    }
    
} catch (Exception $e) {
    echo "❌ Connection failed: " . $e->getMessage() . "\n";
    echo "\n--- Troubleshooting ---\n";
    echo "1. Check your .env file uses Laravel format\n";
    echo "2. Ensure database server is running (for MySQL/PostgreSQL)\n";
    echo "3. For SQLite, ensure db.sqlite file exists\n";
    echo "4. Verify credentials are correct\n";
    echo "\n--- Laravel .env Format ---\n";
    echo "DB_CONNECTION=mysql\n";
    echo "DB_HOST=127.0.0.1\n";  
    echo "DB_PORT=3306\n";
    echo "DB_DATABASE=swifty\n";
    echo "DB_USERNAME=root\n";
    echo "DB_PASSWORD=\n";
}

echo "\n=== End Demo ===\n";