<?php
/**
 * Fix Products Table Script
 * 
 * This script fixes missing columns in the products table that are causing errors.
 * Specifically addresses the 'sub_category' column issue.
 */

// Prevent direct access in production
if (file_exists('.env') && !isset($_GET['force'])) {
    die('This script is disabled in production. Add ?force=1 to the URL to run it.');
}

echo "<h1>Fix Products Table Script</h1>";

// Check if we're in the right directory
if (!file_exists('public/index.php')) {
    echo "<p style='color: red;'>❌ This script must be run from the Laravel root directory!</p>";
    echo "<p>Make sure you're in the directory that contains the 'public' folder.</p>";
    exit;
}

echo "<p>Fixing products table missing columns...</p>";

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
    
    // Check if products table exists
    $stmt = $pdo->query("SHOW TABLES LIKE 'products'");
    if ($stmt->rowCount() == 0) {
        echo "<p style='color: red;'>❌ products table does not exist!</p>";
        echo "<p>Creating products table...</p>";
        
        $createProductsTable = "
        CREATE TABLE products (
            id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
            name VARCHAR(255) NOT NULL,
            image VARCHAR(255) NULL,
            sku VARCHAR(100) UNIQUE NOT NULL,
            category VARCHAR(100) NOT NULL,
            sub_category VARCHAR(100) NULL,
            custom_sub_category VARCHAR(255) NULL,
            description TEXT NULL,
            fonts JSON NULL,
            font_requirement BOOLEAN DEFAULT FALSE,
            metals JSON NULL,
            local_pricing JSON NULL,
            international_pricing JSON NULL,
            is_active BOOLEAN DEFAULT TRUE,
            created_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci";
        
        try {
            $pdo->exec($createProductsTable);
            echo "<p style='color: green;'>✅ Created products table</p>";
        } catch (PDOException $e) {
            echo "<p style='color: red;'>❌ Failed to create products table: " . $e->getMessage() . "</p>";
            exit;
        }
    } else {
        echo "<p style='color: green;'>✅ products table exists</p>";
    }
    
    // Get current columns in products table
    $stmt = $pdo->query("DESCRIBE products");
    $existingColumns = [];
    while ($row = $stmt->fetch()) {
        $existingColumns[] = $row['Field'];
    }
    
    echo "<h3>Current columns in products table:</h3>";
    echo "<ul>";
    foreach ($existingColumns as $column) {
        echo "<li>$column</li>";
    }
    echo "</ul>";
    
    // Define missing columns that need to be added
    $missingColumns = [
        'sub_category' => "ALTER TABLE products ADD COLUMN sub_category VARCHAR(100) NULL AFTER category",
        'custom_sub_category' => "ALTER TABLE products ADD COLUMN custom_sub_category VARCHAR(255) NULL AFTER sub_category",
        'name' => "ALTER TABLE products ADD COLUMN name VARCHAR(255) NOT NULL AFTER id",
        'image' => "ALTER TABLE products ADD COLUMN image VARCHAR(255) NULL AFTER name",
        'sku' => "ALTER TABLE products ADD COLUMN sku VARCHAR(100) UNIQUE NOT NULL AFTER image",
        'category' => "ALTER TABLE products ADD COLUMN category VARCHAR(100) NOT NULL AFTER sku",
        'description' => "ALTER TABLE products ADD COLUMN description TEXT NULL AFTER custom_sub_category",
        'fonts' => "ALTER TABLE products ADD COLUMN fonts JSON NULL AFTER description",
        'font_requirement' => "ALTER TABLE products ADD COLUMN font_requirement BOOLEAN DEFAULT FALSE AFTER fonts",
        'metals' => "ALTER TABLE products ADD COLUMN metals JSON NULL AFTER font_requirement",
        'local_pricing' => "ALTER TABLE products ADD COLUMN local_pricing JSON NULL AFTER metals",
        'international_pricing' => "ALTER TABLE products ADD COLUMN international_pricing JSON NULL AFTER local_pricing",
        'is_active' => "ALTER TABLE products ADD COLUMN is_active BOOLEAN DEFAULT TRUE AFTER international_pricing",
        'created_at' => "ALTER TABLE products ADD COLUMN created_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP",
        'updated_at' => "ALTER TABLE products ADD COLUMN updated_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP"
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
    
    // Add unique index on sku if it doesn't exist
    echo "<h3>Adding unique index on sku...</h3>";
    
    try {
        // Check if index already exists
        $stmt = $pdo->query("SHOW INDEX FROM products WHERE Key_name = 'products_sku_unique'");
        if ($stmt->rowCount() == 0) {
            $pdo->exec("ALTER TABLE products ADD UNIQUE INDEX products_sku_unique (sku)");
            echo "<p style='color: green;'>✅ Added unique index on sku</p>";
        } else {
            echo "<p style='color: blue;'>ℹ Unique index on sku already exists</p>";
        }
    } catch (PDOException $e) {
        echo "<p style='color: orange;'>⚠ Could not add unique index on sku: " . $e->getMessage() . "</p>";
    }
    
    // Insert some sample data if table is empty
    $stmt = $pdo->query("SELECT COUNT(*) as count FROM products");
    $count = $stmt->fetch()['count'];
    
    if ($count == 0) {
        echo "<h3>Adding sample products...</h3>";
        
        $sampleProducts = [
            [
                'name' => 'Classic Ring',
                'sku' => 'RING-001',
                'category' => 'Rings',
                'sub_category' => 'Wedding',
                'fonts' => json_encode(['Arial', 'Times New Roman']),
                'font_requirement' => 1,
                'metals' => json_encode(['Stainless', 'Gold']),
                'local_pricing' => json_encode(['Stainless' => 1500, 'Gold' => 3000]),
                'international_pricing' => json_encode(['Stainless' => 30, 'Gold' => 60]),
                'is_active' => 1
            ],
            [
                'name' => 'Elegant Necklace',
                'sku' => 'NECK-001',
                'category' => 'Necklaces',
                'sub_category' => 'Pendant',
                'fonts' => json_encode(['Arial', 'Helvetica']),
                'font_requirement' => 0,
                'metals' => json_encode(['Silver', 'Gold']),
                'local_pricing' => json_encode(['Silver' => 2000, 'Gold' => 4000]),
                'international_pricing' => json_encode(['Silver' => 40, 'Gold' => 80]),
                'is_active' => 1
            ]
        ];
        
        foreach ($sampleProducts as $product) {
            try {
                $stmt = $pdo->prepare("INSERT INTO products (name, sku, category, sub_category, fonts, font_requirement, metals, local_pricing, international_pricing, is_active) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
                $stmt->execute([
                    $product['name'],
                    $product['sku'],
                    $product['category'],
                    $product['sub_category'],
                    $product['fonts'],
                    $product['font_requirement'],
                    $product['metals'],
                    $product['local_pricing'],
                    $product['international_pricing'],
                    $product['is_active']
                ]);
                echo "<p style='color: green;'>✅ Added sample product: " . $product['name'] . "</p>";
            } catch (PDOException $e) {
                echo "<p style='color: orange;'>⚠ Could not add sample product " . $product['name'] . ": " . $e->getMessage() . "</p>";
            }
        }
    } else {
        echo "<p style='color: blue;'>ℹ products table already has data ($count records)</p>";
    }
    
    echo "<hr>";
    echo "<h2>Summary</h2>";
    echo "<p style='color: green; font-weight: bold;'>🎉 Products table fix completed!</p>";
    
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
    echo "<li><strong>Test product creation:</strong> Visit <a href='/products/create' target='_blank'>create product page</a></li>";
    echo "<li><strong>If you need sample data:</strong> Run <a href='seed-default-users.php?force=1' target='_blank'>seed-default-users.php</a></li>";
    echo "</ol>";
    
    echo "<p><strong>The 'sub_category' column error should now be fixed!</strong></p>";
    
} catch (PDOException $e) {
    echo "<p style='color: red;'>❌ Database connection failed: " . $e->getMessage() . "</p>";
    echo "<p>Please check your database configuration in the .env file.</p>";
} catch (Exception $e) {
    echo "<p style='color: red;'>❌ Error: " . $e->getMessage() . "</p>";
}

?> 