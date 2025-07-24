<?php
/**
 * Fix Customers Table Script
 * 
 * This script fixes missing columns in the customers table that are causing errors.
 * Specifically addresses the 'barangay' column issue.
 */

// Prevent direct access in production
if (file_exists('.env') && !isset($_GET['force'])) {
    die('This script is disabled in production. Add ?force=1 to the URL to run it.');
}

echo "<h1>Fix Customers Table Script</h1>";

// Check if we're in the right directory
if (!file_exists('public/index.php')) {
    echo "<p style='color: red;'>❌ This script must be run from the Laravel root directory!</p>";
    echo "<p>Make sure you're in the directory that contains the 'public' folder.</p>";
    exit;
}

echo "<p>Fixing customers table missing columns...</p>";

// Load .env file to get database configuration
if (!file_exists('.env')) {
    echo "<p style='color: red;'>❌ .env file not found!</p>";
    exit;
}

$envContent = file_get_contents('.env');
$envLines = explode("\n", $envContent);
$dbConfig = [];

foreach ($envLines as $line) {
    $line = trim($line);
    if (strpos($line, '=') !== false && !str_starts_with($line, '#')) {
        list($key, $value) = explode('=', $line, 2);
        $dbConfig[trim($key)] = trim($value);
    }
}

// Get database configuration
$dbHost = $dbConfig['DB_HOST'] ?? 'localhost';
$dbPort = $dbConfig['DB_PORT'] ?? '3306';
$dbDatabase = $dbConfig['DB_DATABASE'] ?? '';
$dbUsername = $dbConfig['DB_USERNAME'] ?? '';
$dbPassword = $dbConfig['DB_PASSWORD'] ?? '';

if (empty($dbDatabase) || empty($dbUsername)) {
    echo "<p style='color: red;'>❌ Database configuration not found in .env file!</p>";
    echo "<p>Make sure DB_DATABASE, DB_USERNAME, and DB_PASSWORD are set.</p>";
    exit;
}

echo "<h3>Database Configuration:</h3>";
echo "<ul>";
echo "<li><strong>Host:</strong> $dbHost</li>";
echo "<li><strong>Port:</strong> $dbPort</li>";
echo "<li><strong>Database:</strong> $dbDatabase</li>";
echo "<li><strong>Username:</strong> $dbUsername</li>";
echo "</ul>";

try {
    // Connect to database
    $dsn = "mysql:host=$dbHost;port=$dbPort;dbname=$dbDatabase;charset=utf8mb4";
    $pdo = new PDO($dsn, $dbUsername, $dbPassword, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES => false,
    ]);
    
    echo "<p style='color: green;'>✅ Database connection successful</p>";
    
    // Check if customers table exists
    $stmt = $pdo->query("SHOW TABLES LIKE 'customers'");
    if ($stmt->rowCount() == 0) {
        echo "<p style='color: red;'>❌ customers table does not exist!</p>";
        echo "<p>Creating customers table...</p>";
        
        $createCustomersTable = "
        CREATE TABLE customers (
            id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
            name VARCHAR(255) NOT NULL,
            email VARCHAR(255) UNIQUE NULL,
            phone VARCHAR(50) NULL,
            street VARCHAR(255) NULL,
            barangay VARCHAR(255) NULL,
            city VARCHAR(255) NULL,
            province VARCHAR(255) NULL,
            country VARCHAR(255) NULL,
            distributor_id BIGINT UNSIGNED NOT NULL,
            created_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            FOREIGN KEY (distributor_id) REFERENCES distributors(id) ON DELETE CASCADE
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci";
        
        try {
            $pdo->exec($createCustomersTable);
            echo "<p style='color: green;'>✅ Created customers table</p>";
        } catch (PDOException $e) {
            echo "<p style='color: red;'>❌ Failed to create customers table: " . $e->getMessage() . "</p>";
            exit;
        }
    } else {
        echo "<p style='color: green;'>✅ customers table exists</p>";
    }
    
    // Get current columns in customers table
    $stmt = $pdo->query("DESCRIBE customers");
    $existingColumns = [];
    while ($row = $stmt->fetch()) {
        $existingColumns[] = $row['Field'];
    }
    
    echo "<h3>Current columns in customers table:</h3>";
    echo "<ul>";
    foreach ($existingColumns as $column) {
        echo "<li>$column</li>";
    }
    echo "</ul>";
    
    // Define missing columns that need to be added
    $missingColumns = [
        'barangay' => "ALTER TABLE customers ADD COLUMN barangay VARCHAR(255) NULL AFTER street",
        'name' => "ALTER TABLE customers ADD COLUMN name VARCHAR(255) NOT NULL AFTER id",
        'email' => "ALTER TABLE customers ADD COLUMN email VARCHAR(255) UNIQUE NULL AFTER name",
        'phone' => "ALTER TABLE customers ADD COLUMN phone VARCHAR(50) NULL AFTER email",
        'street' => "ALTER TABLE customers ADD COLUMN street VARCHAR(255) NULL AFTER phone",
        'city' => "ALTER TABLE customers ADD COLUMN city VARCHAR(255) NULL AFTER barangay",
        'province' => "ALTER TABLE customers ADD COLUMN province VARCHAR(255) NULL AFTER city",
        'country' => "ALTER TABLE customers ADD COLUMN country VARCHAR(255) NULL AFTER province",
        'distributor_id' => "ALTER TABLE customers ADD COLUMN distributor_id BIGINT UNSIGNED NOT NULL AFTER country",
        'created_at' => "ALTER TABLE customers ADD COLUMN created_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP",
        'updated_at' => "ALTER TABLE customers ADD COLUMN updated_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP"
    ];
    
    // Add missing columns
    $addedColumns = [];
    foreach ($missingColumns as $columnName => $sql) {
        if (!in_array($columnName, $existingColumns)) {
            try {
                $pdo->exec($sql);
                $addedColumns[] = $columnName;
                echo "<p style='color: green;'>✅ Added column: $columnName</p>";
            } catch (PDOException $e) {
                echo "<p style='color: orange;'>⚠ Could not add column $columnName: " . $e->getMessage() . "</p>";
            }
        } else {
            echo "<p style='color: blue;'>ℹ Column already exists: $columnName</p>";
        }
    }
    
    // Add foreign key constraint if it doesn't exist
    echo "<h3>Adding foreign key constraint...</h3>";
    
    try {
        // Check if constraint already exists
        $stmt = $pdo->query("SELECT CONSTRAINT_NAME FROM information_schema.TABLE_CONSTRAINTS WHERE TABLE_SCHEMA = '$dbDatabase' AND TABLE_NAME = 'customers' AND CONSTRAINT_NAME = 'customers_distributor_id_foreign'");
        if ($stmt->rowCount() == 0) {
            $pdo->exec("ALTER TABLE customers ADD CONSTRAINT customers_distributor_id_foreign FOREIGN KEY (distributor_id) REFERENCES distributors(id) ON DELETE CASCADE");
            echo "<p style='color: green;'>✅ Added foreign key constraint: customers_distributor_id_foreign</p>";
        } else {
            echo "<p style='color: blue;'>ℹ Foreign key constraint already exists: customers_distributor_id_foreign</p>";
        }
    } catch (PDOException $e) {
        echo "<p style='color: orange;'>⚠ Could not add foreign key constraint: " . $e->getMessage() . "</p>";
    }
    
    // Add unique index on email if it doesn't exist
    echo "<h3>Adding unique index on email...</h3>";
    
    try {
        // Check if index already exists
        $stmt = $pdo->query("SHOW INDEX FROM customers WHERE Key_name = 'customers_email_unique'");
        if ($stmt->rowCount() == 0) {
            $pdo->exec("ALTER TABLE customers ADD UNIQUE INDEX customers_email_unique (email)");
            echo "<p style='color: green;'>✅ Added unique index on email</p>";
        } else {
            echo "<p style='color: blue;'>ℹ Unique index on email already exists</p>";
        }
    } catch (PDOException $e) {
        echo "<p style='color: orange;'>⚠ Could not add unique index on email: " . $e->getMessage() . "</p>";
    }
    
    echo "<hr>";
    echo "<h2>Summary</h2>";
    echo "<p style='color: green; font-weight: bold;'>🎉 Customers table fix completed!</p>";
    
    if (!empty($addedColumns)) {
        echo "<h3>Added columns:</h3>";
        echo "<ul>";
        foreach ($addedColumns as $column) {
            echo "<li style='color: green;'>✅ $column</li>";
        }
        echo "</ul>";
    }
    
    echo "<h3>Next Steps:</h3>";
    echo "<ol>";
    echo "<li><strong>Test the application:</strong> Visit <a href='/' target='_blank'>your domain</a></li>";
    echo "<li><strong>Test customer creation:</strong> Visit <a href='/customers/create' target='_blank'>create customer page</a></li>";
    echo "<li><strong>If you need sample data:</strong> Run <a href='seed-default-users.php?force=1' target='_blank'>seed-default-users.php</a></li>";
    echo "</ol>";
    
    echo "<p><strong>The 'barangay' column error should now be fixed!</strong></p>";
    
} catch (PDOException $e) {
    echo "<p style='color: red;'>❌ Database connection failed: " . $e->getMessage() . "</p>";
    echo "<p>Please check your database configuration in the .env file.</p>";
} catch (Exception $e) {
    echo "<p style='color: red;'>❌ Error: " . $e->getMessage() . "</p>";
}

?> 