<?php
/**
 * Brayne Jewelry Manager - Standalone Installer
 * 
 * Place this file in your domain root and visit: https://yourdomain.com/install.php
 */

// Check if already installed
if (file_exists('.env') && !isset($_GET['force'])) {
    die('
    <div style="font-family: Arial, sans-serif; max-width: 600px; margin: 50px auto; padding: 20px; border: 1px solid #ddd; border-radius: 10px;">
        <h2 style="color: #333;">🏪 Brayne Jewelry Manager</h2>
        <p style="color: #666;">Application is already installed.</p>
        <p><a href="login" style="background: #007bff; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px;">Go to Login</a></p>
        <p><small>Add ?force=1 to reinstall</small></p>
    </div>
    ');
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $result = performInstallation($_POST);
    if ($result['success']) {
        showSuccessPage($result);
    } else {
        showErrorPage($result['error']);
    }
    exit;
}

// Show installation form
showInstallationForm();

function performInstallation($data) {
    try {
        // Validate input
        if (empty($data['db_host']) || empty($data['db_name']) || empty($data['db_user']) || 
            empty($data['app_url']) || empty($data['admin_email']) || empty($data['admin_password']) || empty($data['db_pass'])) {
            return ['success' => false, 'error' => 'All fields are required.'];
        }

        // Test database connection
        $pdo = new PDO("mysql:host={$data['db_host']};dbname={$data['db_name']}", $data['db_user'], $data['db_pass']);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        // Check if tables already exist
        $existingTables = checkExistingTables($pdo);
        if (!empty($existingTables)) {
            // If tables exist, just update the .env file and add admin user
            $env_content = generateEnvContent($data);
            if (file_put_contents('.env', $env_content) === false) {
                return ['success' => false, 'error' => 'Could not create .env file. Check file permissions.'];
            }

            // Generate app key
            $app_key = 'base64:' . base64_encode(random_bytes(32));
            $env_content = str_replace('APP_KEY=', 'APP_KEY=' . $app_key, $env_content);
            file_put_contents('.env', $env_content);

            // Add admin user if it doesn't exist
            addAdminUserIfNotExists($pdo, $data);

            return ['success' => true, 'data' => $data, 'message' => 'Application configured successfully. Tables already existed.'];
        }

        // Create .env file
        $env_content = generateEnvContent($data);
        if (file_put_contents('.env', $env_content) === false) {
            return ['success' => false, 'error' => 'Could not create .env file. Check file permissions.'];
        }

        // Generate app key
        $app_key = 'base64:' . base64_encode(random_bytes(32));
        $env_content = str_replace('APP_KEY=', 'APP_KEY=' . $app_key, $env_content);
        file_put_contents('.env', $env_content);

        // Create complete database schema
        createCompleteDatabaseSchema($pdo);

        // Setup basic data
        setupBasicData($pdo, $data);

        return ['success' => true, 'data' => $data];

    } catch (PDOException $e) {
        return ['success' => false, 'error' => 'Database connection failed: ' . $e->getMessage()];
    } catch (Exception $e) {
        return ['success' => false, 'error' => 'Installation failed: ' . $e->getMessage()];
    }
}

function checkExistingTables($pdo) {
    $existingTables = [];
    $tables = ['users', 'distributors', 'customers', 'products', 'couriers', 'orders', 'order_product', 'order_status_histories', 'settings', 'notifications'];
    
    foreach ($tables as $table) {
        try {
            $stmt = $pdo->query("SHOW TABLES LIKE '$table'");
            if ($stmt->rowCount() > 0) {
                $existingTables[] = $table;
            }
        } catch (Exception $e) {
            // Table doesn't exist
        }
    }
    
    return $existingTables;
}

function addAdminUserIfNotExists($pdo, $data) {
    // Check if admin user already exists
    $stmt = $pdo->prepare("SELECT id FROM users WHERE email = ?");
    $stmt->execute([$data['admin_email']]);
    
    if ($stmt->rowCount() == 0) {
        // Create admin user
        $hashed_password = password_hash($data['admin_password'], PASSWORD_DEFAULT);
        $stmt = $pdo->prepare("INSERT INTO users (name, email, password, role) VALUES (?, ?, ?, 'admin')");
        $stmt->execute(['Admin User', $data['admin_email'], $hashed_password]);
    }
}

function createCompleteDatabaseSchema($pdo) {
    // Disable foreign key checks temporarily
    $pdo->exec("SET FOREIGN_KEY_CHECKS = 0");
    
    // Drop existing tables if they exist (in reverse dependency order)
    $pdo->exec("DROP TABLE IF EXISTS order_status_histories");
    $pdo->exec("DROP TABLE IF EXISTS order_product");
    $pdo->exec("DROP TABLE IF EXISTS orders");
    $pdo->exec("DROP TABLE IF EXISTS customers");
    $pdo->exec("DROP TABLE IF EXISTS distributors");
    $pdo->exec("DROP TABLE IF EXISTS notifications");
    $pdo->exec("DROP TABLE IF EXISTS products");
    $pdo->exec("DROP TABLE IF EXISTS couriers");
    $pdo->exec("DROP TABLE IF EXISTS users");
    $pdo->exec("DROP TABLE IF EXISTS settings");

    // Create users table first (no dependencies)
    $pdo->exec("
        CREATE TABLE users (
            id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
            name VARCHAR(255) NOT NULL,
            email VARCHAR(255) UNIQUE NOT NULL,
            password VARCHAR(255) NOT NULL,
            role ENUM('admin', 'distributor', 'factory') DEFAULT 'distributor',
            logo VARCHAR(255) NULL,
            created_at TIMESTAMP NULL DEFAULT NULL,
            updated_at TIMESTAMP NULL DEFAULT NULL
        )
    ");

    // Create distributors table (depends on users)
    $pdo->exec("
        CREATE TABLE distributors (
            id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
            user_id BIGINT UNSIGNED NOT NULL,
            company_name VARCHAR(255) NOT NULL,
            phone VARCHAR(255) NULL,
            street VARCHAR(255) NULL,
            city VARCHAR(255) NULL,
            province VARCHAR(255) NULL,
            country VARCHAR(255) NULL,
            is_international BOOLEAN DEFAULT FALSE,
            created_at TIMESTAMP NULL DEFAULT NULL,
            updated_at TIMESTAMP NULL DEFAULT NULL,
            FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
        )
    ");

    // Create customers table (depends on distributors)
    $pdo->exec("
        CREATE TABLE customers (
            id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
            distributor_id BIGINT UNSIGNED NOT NULL,
            name VARCHAR(255) NOT NULL,
            email VARCHAR(255) NULL,
            phone VARCHAR(255) NULL,
            street VARCHAR(255) NULL,
            city VARCHAR(255) NULL,
            province VARCHAR(255) NULL,
            country VARCHAR(255) NULL,
            postal_code VARCHAR(255) NULL,
            created_at TIMESTAMP NULL DEFAULT NULL,
            updated_at TIMESTAMP NULL DEFAULT NULL,
            FOREIGN KEY (distributor_id) REFERENCES distributors(id) ON DELETE CASCADE
        )
    ");

    // Create products table (no dependencies)
    $pdo->exec("
        CREATE TABLE products (
            id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
            name VARCHAR(255) NOT NULL,
            description TEXT NULL,
            price DECIMAL(10,2) NOT NULL,
            sku VARCHAR(255) UNIQUE NOT NULL,
            category VARCHAR(255) NOT NULL,
            is_active BOOLEAN DEFAULT TRUE,
            image VARCHAR(255) NULL,
            created_at TIMESTAMP NULL DEFAULT NULL,
            updated_at TIMESTAMP NULL DEFAULT NULL
        )
    ");

    // Create couriers table (no dependencies)
    $pdo->exec("
        CREATE TABLE couriers (
            id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
            name VARCHAR(255) NOT NULL,
            phone VARCHAR(255) NULL,
            email VARCHAR(255) NULL,
            is_active BOOLEAN DEFAULT TRUE,
            created_at TIMESTAMP NULL DEFAULT NULL,
            updated_at TIMESTAMP NULL DEFAULT NULL
        )
    ");

    // Create orders table (depends on distributors and customers)
    $pdo->exec("
        CREATE TABLE orders (
            id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
            distributor_id BIGINT UNSIGNED NOT NULL,
            customer_id BIGINT UNSIGNED NOT NULL,
            order_number VARCHAR(255) UNIQUE NOT NULL,
            status VARCHAR(255) DEFAULT 'pending',
            payment_status ENUM('unpaid', 'partially_paid', 'fully_paid') DEFAULT 'unpaid',
            total_amount DECIMAL(10,2) DEFAULT 0.00,
            priority ENUM('low', 'medium', 'high') DEFAULT 'medium',
            timeline_days INT DEFAULT 7,
            created_at TIMESTAMP NULL DEFAULT NULL,
            updated_at TIMESTAMP NULL DEFAULT NULL,
            FOREIGN KEY (distributor_id) REFERENCES distributors(id) ON DELETE CASCADE,
            FOREIGN KEY (customer_id) REFERENCES customers(id) ON DELETE CASCADE
        )
    ");

    // Create order_product table (depends on orders and products)
    $pdo->exec("
        CREATE TABLE order_product (
            id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
            order_id BIGINT UNSIGNED NOT NULL,
            product_id BIGINT UNSIGNED NOT NULL,
            quantity INT NOT NULL DEFAULT 1,
            price DECIMAL(10,2) NOT NULL,
            metal VARCHAR(255) NULL,
            font VARCHAR(255) NULL,
            created_at TIMESTAMP NULL DEFAULT NULL,
            updated_at TIMESTAMP NULL DEFAULT NULL,
            FOREIGN KEY (order_id) REFERENCES orders(id) ON DELETE CASCADE,
            FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE
        )
    ");

    // Create order_status_histories table (depends on orders)
    $pdo->exec("
        CREATE TABLE order_status_histories (
            id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
            order_id BIGINT UNSIGNED NOT NULL,
            status VARCHAR(255) NOT NULL,
            notes TEXT NULL,
            created_at TIMESTAMP NULL DEFAULT NULL,
            updated_at TIMESTAMP NULL DEFAULT NULL,
            FOREIGN KEY (order_id) REFERENCES orders(id) ON DELETE CASCADE
        )
    ");

    // Create settings table (no dependencies)
    $pdo->exec("
        CREATE TABLE settings (
            id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
            `key` VARCHAR(255) UNIQUE NOT NULL,
            value TEXT NULL,
            created_at TIMESTAMP NULL DEFAULT NULL,
            updated_at TIMESTAMP NULL DEFAULT NULL
        )
    ");

    // Create notifications table (depends on users)
    $pdo->exec("
        CREATE TABLE notifications (
            id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
            user_id BIGINT UNSIGNED NOT NULL,
            title VARCHAR(255) NOT NULL,
            message TEXT NOT NULL,
            type VARCHAR(255) DEFAULT 'info',
            is_read BOOLEAN DEFAULT FALSE,
            created_at TIMESTAMP NULL DEFAULT NULL,
            updated_at TIMESTAMP NULL DEFAULT NULL,
            FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
        )
    ");

    // Re-enable foreign key checks
    $pdo->exec("SET FOREIGN_KEY_CHECKS = 1");
}

function generateEnvContent($data) {
    return "APP_NAME=\"Brayne Jewelry Manager\"
APP_ENV=production
APP_KEY=
APP_DEBUG=false
APP_URL={$data['app_url']}

LOG_CHANNEL=stack
LOG_DEPRECATIONS_CHANNEL=null
LOG_LEVEL=error

DB_CONNECTION=mysql
DB_HOST={$data['db_host']}
DB_PORT=3306
DB_DATABASE={$data['db_name']}
DB_USERNAME={$data['db_user']}
DB_PASSWORD={$data['db_pass']}

BROADCAST_DRIVER=log
CACHE_DRIVER=file
FILESYSTEM_DISK=public
QUEUE_CONNECTION=sync
SESSION_DRIVER=file
SESSION_LIFETIME=120

MAIL_MAILER=smtp
MAIL_HOST=mailpit
MAIL_PORT=1025
MAIL_USERNAME=null
MAIL_PASSWORD=null
MAIL_ENCRYPTION=null
MAIL_FROM_ADDRESS=\"noreply@" . parse_url($data['app_url'], PHP_URL_HOST) . "\"
MAIL_FROM_NAME=\"Brayne Jewelry Manager\"

SESSION_SECURE_COOKIE=true
PASSWORD_TIMEOUT=10800
LOGIN_THROTTLE=6
LOGIN_THROTTLE_DECAY=60";
}

function setupBasicData($pdo, $data) {
    // Create admin user
    $hashed_password = password_hash($data['admin_password'], PASSWORD_DEFAULT);
    $stmt = $pdo->prepare("INSERT INTO users (name, email, password, role) VALUES (?, ?, ?, 'admin')");
    $stmt->execute(['Admin User', $data['admin_email'], $hashed_password]);

    // Create distributor users
    $distributor1_password = password_hash('password', PASSWORD_DEFAULT);
    $distributor2_password = password_hash('password', PASSWORD_DEFAULT);
    $factory_password = password_hash('password', PASSWORD_DEFAULT);
    
    $stmt = $pdo->prepare("INSERT INTO users (name, email, password, role) VALUES (?, ?, ?, 'distributor')");
    $stmt->execute(['John Distributor', 'distributor1@jewelry.com', $distributor1_password]);
    $distributor1_id = $pdo->lastInsertId();
    
    $stmt->execute(['Jane Distributor', 'distributor2@jewelry.com', $distributor2_password]);
    $distributor2_id = $pdo->lastInsertId();
    
    $stmt = $pdo->prepare("INSERT INTO users (name, email, password, role) VALUES (?, ?, ?, 'factory')");
    $stmt->execute(['Factory Manager', 'factory@jewelry.com', $factory_password]);

    // Create distributor profiles
    $stmt = $pdo->prepare("INSERT INTO distributors (user_id, company_name, phone, street, city, province, country) VALUES (?, ?, ?, ?, ?, ?, ?)");
    $stmt->execute([$distributor1_id, 'Golden Jewelers', '+1-555-0101', '123 Main Street', 'New York', 'NY', 'USA']);
    $stmt->execute([$distributor2_id, 'Silver & Gold Co.', '+1-555-0202', '456 Oak Avenue', 'Los Angeles', 'CA', 'USA']);

    // Create sample products
    $products = [
        ['Diamond Ring', 'Beautiful 18k gold diamond ring', 2500.00, 'DR-001', 'Rings'],
        ['Pearl Necklace', 'Elegant freshwater pearl necklace', 800.00, 'PN-001', 'Necklaces'],
        ['Sapphire Earrings', 'Stunning sapphire and diamond earrings', 1200.00, 'SE-001', 'Earrings'],
        ['Gold Bracelet', 'Classic 14k gold bracelet', 600.00, 'GB-001', 'Bracelets']
    ];

    $stmt = $pdo->prepare("INSERT INTO products (name, description, price, sku, category) VALUES (?, ?, ?, ?, ?)");
    foreach ($products as $product) {
        $stmt->execute($product);
    }

    // Create couriers
    $stmt = $pdo->prepare("INSERT INTO couriers (name, phone, email) VALUES (?, ?, ?)");
    $couriers = [
        ['Express Delivery', '+1-555-0303', 'express@delivery.com'],
        ['Fast Shipping Co.', '+1-555-0404', 'fast@shipping.com'],
        ['Premium Logistics', '+1-555-0505', 'premium@logistics.com']
    ];
    foreach ($couriers as $courier) {
        $stmt->execute($courier);
    }

    // Create sample customers
    $stmt = $pdo->prepare("INSERT INTO customers (distributor_id, name, email, phone, street, city, province, country) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
    $customers = [
        [$distributor1_id, 'Alice Johnson', 'alice@email.com', '+1-555-1001', '789 Pine St', 'New York', 'NY', 'USA'],
        [$distributor1_id, 'Bob Smith', 'bob@email.com', '+1-555-1002', '321 Elm St', 'New York', 'NY', 'USA'],
        [$distributor2_id, 'Carol Davis', 'carol@email.com', '+1-555-2001', '654 Maple Ave', 'Los Angeles', 'CA', 'USA']
    ];
    foreach ($customers as $customer) {
        $stmt->execute($customer);
    }
}

function showInstallationForm() {
    ?>
    <!DOCTYPE html>
    <html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Brayne Jewelry Manager - Installer</title>
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
        <style>
            body { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); min-height: 100vh; }
            .installer-card { background: white; border-radius: 15px; box-shadow: 0 10px 30px rgba(0,0,0,0.1); }
        </style>
    </head>
    <body>
        <div class="container py-5">
            <div class="row justify-content-center">
                <div class="col-md-8">
                    <div class="installer-card p-5">
                        <div class="text-center mb-4">
                            <h1><i class="fas fa-gem text-primary"></i> Brayne Jewelry Manager</h1>
                            <h4 class="text-muted">Quick Installer</h4>
                        </div>

                        <form method="POST">
                            <h5 class="mb-3">Database Configuration</h5>
                            <div class="mb-3">
                                <label for="db_host" class="form-label">Database Host</label>
                                <input type="text" class="form-control" id="db_host" name="db_host" value="localhost" required>
                            </div>
                            <div class="mb-3">
                                <label for="db_name" class="form-label">Database Name</label>
                                <input type="text" class="form-control" id="db_name" name="db_name" required>
                            </div>
                            <div class="mb-3">
                                <label for="db_user" class="form-label">Database Username</label>
                                <input type="text" class="form-control" id="db_user" name="db_user" required>
                            </div>
                            <div class="mb-3">
                                <label for="db_pass" class="form-label">Database Password</label>
                                <input type="password" class="form-control" id="db_pass" name="db_pass" required>
                            </div>

                            <h5 class="mb-3 mt-4">Application Configuration</h5>
                            <div class="mb-3">
                                <label for="app_url" class="form-label">Application URL</label>
                                <input type="url" class="form-control" id="app_url" name="app_url" value="https://<?= $_SERVER['HTTP_HOST'] ?>" required>
                                <div class="form-text">Include https:// and no trailing slash</div>
                            </div>
                            <div class="mb-3">
                                <label for="admin_email" class="form-label">Admin Email</label>
                                <input type="email" class="form-control" id="admin_email" name="admin_email" required>
                            </div>
                            <div class="mb-3">
                                <label for="admin_password" class="form-label">Admin Password</label>
                                <input type="password" class="form-control" id="admin_password" name="admin_password" required>
                            </div>

                            <div class="text-center">
                                <button type="submit" class="btn btn-primary btn-lg">
                                    <i class="fas fa-rocket"></i> Install Application
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </body>
    </html>
    <?php
}

function showSuccessPage($result) {
    $message = isset($result['message']) ? $result['message'] : 'Your Brayne Jewelry Manager has been successfully installed.';
    ?>
    <!DOCTYPE html>
    <html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Installation Complete - Brayne Jewelry Manager</title>
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
        <style>
            body { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); min-height: 100vh; }
            .success-card { background: white; border-radius: 15px; box-shadow: 0 10px 30px rgba(0,0,0,0.1); }
        </style>
    </head>
    <body>
        <div class="container py-5">
            <div class="row justify-content-center">
                <div class="col-md-8">
                    <div class="success-card p-5 text-center">
                        <div class="mb-4">
                            <i class="fas fa-check-circle text-success" style="font-size: 4rem;"></i>
                        </div>
                        <h2 class="text-success">Installation Complete!</h2>
                        <p class="text-muted"><?= htmlspecialchars($message) ?></p>
                        
                        <div class="alert alert-info text-start">
                            <h5><i class="fas fa-info-circle"></i> Login Credentials:</h5>
                            <ul>
                                <li><strong>Admin:</strong> <?= htmlspecialchars($result['data']['admin_email']) ?> / (password you set)</li>
                                <li><strong>Distributor:</strong> distributor1@jewelry.com / password</li>
                                <li><strong>Factory:</strong> factory@jewelry.com / password</li>
                            </ul>
                        </div>

                        <div class="alert alert-warning text-start">
                            <h5><i class="fas fa-exclamation-triangle"></i> Important Next Steps:</h5>
                            <ol>
                                <li><strong>Run Post-Installation Setup:</strong> Visit <code><?= htmlspecialchars($result['data']['app_url']) ?>/post-install.php</code></li>
                                <li><strong>Delete Install Files:</strong> Remove <code>install.php</code> and <code>post-install.php</code> for security</li>
                                <li><strong>Test Login:</strong> Try logging in with your admin credentials</li>
                            </ol>
                        </div>

                        <div class="row mt-4">
                            <div class="col-md-6">
                                <a href="post-install.php" class="btn btn-warning btn-lg w-100">
                                    <i class="fas fa-cog"></i> Run Post-Install Setup
                                </a>
                            </div>
                            <div class="col-md-6">
                                <a href="login" class="btn btn-primary btn-lg w-100">
                                    <i class="fas fa-sign-in-alt"></i> Go to Login
                                </a>
                            </div>
                        </div>

                        <div class="mt-4">
                            <small class="text-muted">
                                <i class="fas fa-shield-alt"></i>
                                For security, you should delete this install.php file after installation.
                            </small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </body>
    </html>
    <?php
}

function showErrorPage($error) {
    ?>
    <!DOCTYPE html>
    <html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Installation Error - Brayne Jewelry Manager</title>
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
        <style>
            body { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); min-height: 100vh; }
            .error-card { background: white; border-radius: 15px; box-shadow: 0 10px 30px rgba(0,0,0,0.1); }
        </style>
    </head>
    <body>
        <div class="container py-5">
            <div class="row justify-content-center">
                <div class="col-md-8">
                    <div class="error-card p-5 text-center">
                        <div class="mb-4">
                            <i class="fas fa-exclamation-triangle text-danger" style="font-size: 4rem;"></i>
                        </div>
                        <h2 class="text-danger">Installation Failed</h2>
                        <p class="text-muted"><?= htmlspecialchars($error) ?></p>
                        
                        <a href="install.php" class="btn btn-primary btn-lg">
                            <i class="fas fa-redo"></i> Try Again
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </body>
    </html>
    <?php
}
?> 