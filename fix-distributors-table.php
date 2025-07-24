<?php
/**
 * Fix Distributors Table Script
 * 
 * This script fixes missing columns in the distributors table that are causing errors.
 * Specifically addresses the 'barangay' column issue.
 */

// Prevent direct access in production
if (file_exists('.env') && !isset($_GET['force'])) {
    die('This script is disabled in production. Add ?force=1 to the URL to run it.');
}

echo "<h1>Fix Distributors Table Script</h1>";

// Check if we're in the right directory
if (!file_exists('public/index.php')) {
    echo "<p style='color: red;'>❌ This script must be run from the Laravel root directory!</p>";
    echo "<p>Make sure you're in the directory that contains the 'public' folder.</p>";
    exit;
}

echo "<p>Fixing distributors table missing columns...</p>";

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
    
    // Check if distributors table exists
    $stmt = $pdo->query("SHOW TABLES LIKE 'distributors'");
    if ($stmt->rowCount() == 0) {
        echo "<p style='color: red;'>❌ distributors table does not exist!</p>";
        echo "<p>Creating distributors table...</p>";
        
        $createDistributorsTable = "
        CREATE TABLE distributors (
            id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
            user_id BIGINT UNSIGNED NOT NULL,
            company_name VARCHAR(255) NOT NULL,
            phone VARCHAR(50) NULL,
            street VARCHAR(255) NULL,
            barangay VARCHAR(255) NULL,
            city VARCHAR(255) NULL,
            province VARCHAR(255) NULL,
            country VARCHAR(255) NULL,
            is_international BOOLEAN DEFAULT FALSE,
            created_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci";
        
        try {
            $pdo->exec($createDistributorsTable);
            echo "<p style='color: green;'>✅ Created distributors table</p>";
        } catch (PDOException $e) {
            echo "<p style='color: red;'>❌ Failed to create distributors table: " . $e->getMessage() . "</p>";
            exit;
        }
    } else {
        echo "<p style='color: green;'>✅ distributors table exists</p>";
    }
    
    // Get current columns in distributors table
    $stmt = $pdo->query("DESCRIBE distributors");
    $existingColumns = [];
    while ($row = $stmt->fetch()) {
        $existingColumns[] = $row['Field'];
    }
    
    echo "<h3>Current columns in distributors table:</h3>";
    echo "<ul>";
    foreach ($existingColumns as $column) {
        echo "<li>$column</li>";
    }
    echo "</ul>";
    
    // Define missing columns that need to be added
    $missingColumns = [
        'barangay' => "ALTER TABLE distributors ADD COLUMN barangay VARCHAR(255) NULL AFTER street",
        'user_id' => "ALTER TABLE distributors ADD COLUMN user_id BIGINT UNSIGNED NOT NULL AFTER id",
        'company_name' => "ALTER TABLE distributors ADD COLUMN company_name VARCHAR(255) NOT NULL AFTER user_id",
        'phone' => "ALTER TABLE distributors ADD COLUMN phone VARCHAR(50) NULL AFTER company_name",
        'street' => "ALTER TABLE distributors ADD COLUMN street VARCHAR(255) NULL AFTER phone",
        'city' => "ALTER TABLE distributors ADD COLUMN city VARCHAR(255) NULL AFTER barangay",
        'province' => "ALTER TABLE distributors ADD COLUMN province VARCHAR(255) NULL AFTER city",
        'country' => "ALTER TABLE distributors ADD COLUMN country VARCHAR(255) NULL AFTER province",
        'is_international' => "ALTER TABLE distributors ADD COLUMN is_international BOOLEAN DEFAULT FALSE AFTER country",
        'created_at' => "ALTER TABLE distributors ADD COLUMN created_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP",
        'updated_at' => "ALTER TABLE distributors ADD COLUMN updated_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP"
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
        $stmt = $pdo->query("SELECT CONSTRAINT_NAME FROM information_schema.TABLE_CONSTRAINTS WHERE TABLE_SCHEMA = '$dbDatabase' AND TABLE_NAME = 'distributors' AND CONSTRAINT_NAME = 'distributors_user_id_foreign'");
        if ($stmt->rowCount() == 0) {
            $pdo->exec("ALTER TABLE distributors ADD CONSTRAINT distributors_user_id_foreign FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE");
            echo "<p style='color: green;'>✅ Added foreign key constraint: distributors_user_id_foreign</p>";
        } else {
            echo "<p style='color: blue;'>ℹ Foreign key constraint already exists: distributors_user_id_foreign</p>";
        }
    } catch (PDOException $e) {
        echo "<p style='color: orange;'>⚠ Could not add foreign key constraint: " . $e->getMessage() . "</p>";
    }
    
    // Insert some sample data if table is empty
    $stmt = $pdo->query("SELECT COUNT(*) as count FROM distributors");
    $count = $stmt->fetch()['count'];
    
    if ($count == 0) {
        echo "<h3>Adding sample distributor profiles...</h3>";
        
        // Get users with distributor role
        $stmt = $pdo->query("SELECT id, name FROM users WHERE role = 'distributor'");
        $distributorUsers = $stmt->fetchAll();
        
        if (count($distributorUsers) > 0) {
            foreach ($distributorUsers as $user) {
                try {
                    $stmt = $pdo->prepare("INSERT INTO distributors (user_id, company_name, phone, street, barangay, city, province, country, is_international) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
                    $stmt->execute([
                        $user['id'],
                        $user['name'] . ' Company',
                        '+1234567890',
                        '123 Business St',
                        'Business District',
                        'Business City',
                        'Business Province',
                        'Philippines',
                        0
                    ]);
                    echo "<p style='color: green;'>✅ Added distributor profile for: " . $user['name'] . "</p>";
                } catch (PDOException $e) {
                    echo "<p style='color: orange;'>⚠ Could not add distributor profile for " . $user['name'] . ": " . $e->getMessage() . "</p>";
                }
            }
        } else {
            echo "<p style='color: blue;'>ℹ No users with distributor role found</p>";
        }
    } else {
        echo "<p style='color: blue;'>ℹ distributors table already has data ($count records)</p>";
    }
    
    echo "<hr>";
    echo "<h2>Summary</h2>";
    echo "<p style='color: green; font-weight: bold;'>🎉 Distributors table fix completed!</p>";
    
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
    echo "<li><strong>Test distributor profile update:</strong> Visit <a href='/distributor/profile/edit' target='_blank'>edit profile page</a></li>";
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