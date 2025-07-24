<?php
/**
 * Brayne Jewelry Manager - cPanel Installation Script
 * 
 * Upload this file to your jewelry-manager directory on cPanel
 * Then visit: https://yourdomain.com/jewelry-manager/cpanel-install.php
 */

// Prevent direct access if already installed
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

        // Create .env file
        $envContent = generateEnvContent($data);
        if (file_put_contents('.env', $envContent) === false) {
            return ['success' => false, 'error' => 'Could not create .env file. Check file permissions.'];
        }

        // Generate app key
        $app_key = 'base64:' . base64_encode(random_bytes(32));
        $envContent = str_replace('APP_KEY=', 'APP_KEY=' . $app_key, $envContent);
        file_put_contents('.env', $envContent);

        // Create storage directories
        $directories = [
            'storage/app/public',
            'storage/framework/cache',
            'storage/framework/sessions',
            'storage/framework/views',
            'storage/logs',
            'bootstrap/cache'
        ];

        foreach ($directories as $dir) {
            if (!is_dir($dir)) {
                mkdir($dir, 0755, true);
            }
        }

        // Set permissions
        chmod('storage', 0755);
        chmod('bootstrap/cache', 0755);

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

function createCompleteDatabaseSchema($pdo) {
    // Create users table
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS users (
            id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
            name VARCHAR(255) NOT NULL,
            email VARCHAR(255) UNIQUE NOT NULL,
            email_verified_at TIMESTAMP NULL,
            password VARCHAR(255) NOT NULL,
            logo VARCHAR(255) NULL,
            role ENUM('admin', 'distributor', 'factory') DEFAULT 'distributor',
            remember_token VARCHAR(100) NULL,
            created_at TIMESTAMP NULL,
            updated_at TIMESTAMP NULL
        )
    ");

    // Create distributors table
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS distributors (
            id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
            user_id BIGINT UNSIGNED NOT NULL,
            company_name VARCHAR(255) NOT NULL,
            phone VARCHAR(255) NULL,
            street VARCHAR(255) NULL,
            city VARCHAR(255) NULL,
            province VARCHAR(255) NULL,
            postal_code VARCHAR(20) NULL,
            country VARCHAR(255) NULL,
            is_international BOOLEAN DEFAULT FALSE,
            created_at TIMESTAMP NULL,
            updated_at TIMESTAMP NULL,
            FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
        )
    ");

    // Create customers table
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS customers (
            id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
            distributor_id BIGINT UNSIGNED NOT NULL,
            name VARCHAR(255) NOT NULL,
            email VARCHAR(255) NULL,
            phone VARCHAR(255) NULL,
            street VARCHAR(255) NULL,
            city VARCHAR(255) NULL,
            province VARCHAR(255) NULL,
            postal_code VARCHAR(20) NULL,
            country VARCHAR(255) NULL,
            created_at TIMESTAMP NULL,
            updated_at TIMESTAMP NULL,
            FOREIGN KEY (distributor_id) REFERENCES distributors(id) ON DELETE CASCADE
        )
    ");

    // Create products table
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS products (
            id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
            name VARCHAR(255) NOT NULL,
            image VARCHAR(255) NULL,
            sku VARCHAR(255) UNIQUE NOT NULL,
            category VARCHAR(255) NULL,
            sub_category VARCHAR(255) NULL,
            custom_sub_category VARCHAR(255) NULL,
            metals JSON NULL,
            local_pricing JSON NULL,
            international_pricing JSON NULL,
            fonts JSON NULL,
            font_requirement INT DEFAULT 0,
            is_active BOOLEAN DEFAULT TRUE,
            created_at TIMESTAMP NULL,
            updated_at TIMESTAMP NULL
        )
    ");

    // Create couriers table
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS couriers (
            id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
            name VARCHAR(255) NOT NULL,
            phone VARCHAR(255) NULL,
            email VARCHAR(255) NULL,
            is_active BOOLEAN DEFAULT TRUE,
            created_at TIMESTAMP NULL,
            updated_at TIMESTAMP NULL
        )
    ");

    // Create orders table
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS orders (
            id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
            distributor_id BIGINT UNSIGNED NOT NULL,
            customer_id BIGINT UNSIGNED NOT NULL,
            courier_id BIGINT UNSIGNED NULL,
            order_number VARCHAR(255) UNIQUE NOT NULL,
            total_amount DECIMAL(10,2) NOT NULL,
            payment_status ENUM('unpaid', 'partially_paid', 'fully_paid') DEFAULT 'unpaid',
            order_status VARCHAR(50) DEFAULT 'pending_payment',
            priority ENUM('low', 'normal', 'high') DEFAULT 'normal',
            estimated_start_date TIMESTAMP NULL,
            estimated_production_complete TIMESTAMP NULL,
            estimated_finishing_complete TIMESTAMP NULL,
            estimated_delivery_ready TIMESTAMP NULL,
            production_started_at TIMESTAMP NULL,
            production_completed_at TIMESTAMP NULL,
            finishing_started_at TIMESTAMP NULL,
            finishing_completed_at TIMESTAMP NULL,
            estimated_production_hours INT NULL,
            estimated_finishing_hours INT NULL,
            production_notes TEXT NULL,
            notes TEXT NULL,
            template_id BIGINT UNSIGNED NULL,
            created_at TIMESTAMP NULL,
            updated_at TIMESTAMP NULL,
            FOREIGN KEY (distributor_id) REFERENCES distributors(id) ON DELETE CASCADE,
            FOREIGN KEY (customer_id) REFERENCES customers(id) ON DELETE CASCADE,
            FOREIGN KEY (courier_id) REFERENCES couriers(id) ON DELETE SET NULL
        )
    ");

    // Create order_product pivot table
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS order_product (
            id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
            order_id BIGINT UNSIGNED NOT NULL,
            product_id BIGINT UNSIGNED NOT NULL,
            quantity INT NOT NULL,
            price DECIMAL(10,2) NOT NULL,
            metal VARCHAR(255) NULL,
            font VARCHAR(255) NULL,
            created_at TIMESTAMP NULL,
            updated_at TIMESTAMP NULL,
            FOREIGN KEY (order_id) REFERENCES orders(id) ON DELETE CASCADE,
            FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE
        )
    ");

    // Create order_status_histories table
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS order_status_histories (
            id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
            order_id BIGINT UNSIGNED NOT NULL,
            status VARCHAR(50) NOT NULL,
            notes TEXT NULL,
            created_at TIMESTAMP NULL,
            updated_at TIMESTAMP NULL,
            FOREIGN KEY (order_id) REFERENCES orders(id) ON DELETE CASCADE
        )
    ");

    // Create settings table
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS settings (
            id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
            key_name VARCHAR(255) UNIQUE NOT NULL,
            value TEXT NULL,
            created_at TIMESTAMP NULL,
            updated_at TIMESTAMP NULL
        )
    ");

    // Create notifications table
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS notifications (
            id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
            user_id BIGINT UNSIGNED NOT NULL,
            title VARCHAR(255) NOT NULL,
            message TEXT NOT NULL,
            type VARCHAR(50) DEFAULT 'info',
            is_read BOOLEAN DEFAULT FALSE,
            created_at TIMESTAMP NULL,
            updated_at TIMESTAMP NULL,
            FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
        )
    ");

    // Create order_templates table
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS order_templates (
            id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
            distributor_id BIGINT UNSIGNED NOT NULL,
            name VARCHAR(255) NOT NULL,
            description TEXT NULL,
            products JSON NOT NULL,
            created_at TIMESTAMP NULL,
            updated_at TIMESTAMP NULL,
            FOREIGN KEY (distributor_id) REFERENCES distributors(id) ON DELETE CASCADE
        )
    ");

    // Create cache table
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS cache (
            key VARCHAR(255) PRIMARY KEY,
            value LONGTEXT NOT NULL,
            expiration INT NOT NULL
        )
    ");

    // Create jobs table
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS jobs (
            id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
            queue VARCHAR(255) NOT NULL,
            payload LONGTEXT NOT NULL,
            attempts TINYINT UNSIGNED NOT NULL,
            reserved_at INT UNSIGNED NULL,
            available_at INT UNSIGNED NOT NULL,
            created_at INT UNSIGNED NOT NULL
        )
    ");
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
FILESYSTEM_DISK=local
QUEUE_CONNECTION=sync
SESSION_DRIVER=file
SESSION_LIFETIME=120

MEMCACHED_HOST=127.0.0.1

REDIS_HOST=127.0.0.1
REDIS_PASSWORD=null
REDIS_PORT=6379

MAIL_MAILER=smtp
MAIL_HOST=mail.yourdomain.com
MAIL_PORT=587
MAIL_USERNAME=noreply@yourdomain.com
MAIL_PASSWORD=
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=\"noreply@yourdomain.com\"
MAIL_FROM_NAME=\"\${APP_NAME}\"

AWS_ACCESS_KEY_ID=
AWS_SECRET_ACCESS_KEY=
AWS_DEFAULT_REGION=us-east-1
AWS_BUCKET=
AWS_USE_PATH_STYLE_ENDPOINT=false

PUSHER_APP_ID=
PUSHER_APP_KEY=
PUSHER_APP_SECRET=
PUSHER_HOST=
PUSHER_PORT=443
PUSHER_SCHEME=https
PUSHER_APP_CLUSTER=mt1

VITE_APP_NAME=\"\${APP_NAME}\"
VITE_PUSHER_APP_KEY=\"\${PUSHER_APP_KEY}\"
VITE_PUSHER_HOST=\"\${PUSHER_HOST}\"
VITE_PUSHER_PORT=\"\${PUSHER_PORT}\"
VITE_PUSHER_SCHEME=\"\${PUSHER_SCHEME}\"
VITE_PUSHER_APP_CLUSTER=\"\${PUSHER_APP_CLUSTER}\"";
}

function setupBasicData($pdo, $data) {
    // Create admin user
    $adminPassword = password_hash($data['admin_password'], PASSWORD_DEFAULT);
    $stmt = $pdo->prepare("INSERT INTO users (name, email, password, role, created_at, updated_at) VALUES (?, ?, ?, 'admin', NOW(), NOW())");
    $stmt->execute(['Admin User', $data['admin_email'], $adminPassword]);
    $adminId = $pdo->lastInsertId();

    // Create distributor users
    $distributorPassword = password_hash('password', PASSWORD_DEFAULT);
    $stmt = $pdo->prepare("INSERT INTO users (name, email, password, role, created_at, updated_at) VALUES (?, ?, ?, 'distributor', NOW(), NOW())");
    $stmt->execute(['John Distributor', 'distributor1@jewelry.com', $distributorPassword]);
    $distributor1Id = $pdo->lastInsertId();

    $stmt->execute(['Jane Distributor', 'distributor2@jewelry.com', $distributorPassword]);
    $distributor2Id = $pdo->lastInsertId();

    // Create factory user
    $stmt = $pdo->prepare("INSERT INTO users (name, email, password, role, created_at, updated_at) VALUES (?, ?, ?, 'factory', NOW(), NOW())");
    $stmt->execute(['Factory Manager', 'factory@jewelry.com', $distributorPassword]);

    // Create distributor profiles
    $stmt = $pdo->prepare("INSERT INTO distributors (user_id, company_name, phone, street, city, province, country, created_at, updated_at) VALUES (?, ?, ?, ?, ?, ?, ?, NOW(), NOW())");
    $stmt->execute([$distributor1Id, 'Golden Jewelers', '+1-555-0101', '123 Main Street', 'New York', 'NY', 'USA']);
    $stmt->execute([$distributor2Id, 'Silver & Gold Co.', '+1-555-0202', '456 Oak Avenue', 'Los Angeles', 'CA', 'USA']);

    // Create sample products
    $stmt = $pdo->prepare("INSERT INTO products (name, sku, category, local_pricing, international_pricing, metals, fonts, font_requirement, is_active, created_at, updated_at) VALUES (?, ?, ?, ?, ?, ?, ?, ?, TRUE, NOW(), NOW())");
    
    $localPricing = json_encode(['Stainless' => 2500.00, 'Brass Gold' => 2800.00, '925 Pure Sterling Silver' => 3200.00]);
    $internationalPricing = json_encode(['Stainless' => 50.00, 'Brass Gold' => 56.00, '925 Pure Sterling Silver' => 64.00]);
    $metals = json_encode(['Stainless', 'Brass Gold', '925 Pure Sterling Silver']);
    $fonts = json_encode(['Arial', 'Times New Roman', 'Helvetica']);
    
    $stmt->execute(['Diamond Ring', 'DR-001', 'Rings', $localPricing, $internationalPricing, $metals, $fonts, 1]);

    // Create couriers
    $stmt = $pdo->prepare("INSERT INTO couriers (name, phone, email, is_active, created_at, updated_at) VALUES (?, ?, ?, TRUE, NOW(), NOW())");
    $stmt->execute(['Express Delivery', '+1-555-0303', 'express@delivery.com']);
    $stmt->execute(['Fast Shipping Co.', '+1-555-0404', 'fast@shipping.com']);
    $stmt->execute(['Premium Logistics', '+1-555-0505', 'premium@logistics.com']);

    // Create settings
    $stmt = $pdo->prepare("INSERT INTO settings (key_name, value, created_at, updated_at) VALUES (?, ?, NOW(), NOW())");
    $stmt->execute(['app_title', 'Brayne Jewelry Manager']);
    $stmt->execute(['company_name', 'Brayne Jewelry']);
    $stmt->execute(['company_email', 'info@braynejewelry.com']);
}

function showInstallationForm() {
    echo '<!DOCTYPE html>
    <html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Brayne Jewelry Manager - cPanel Installation</title>
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
        <style>
            body { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); min-height: 100vh; }
            .install-card { background: white; border-radius: 15px; box-shadow: 0 10px 30px rgba(0,0,0,0.1); }
        </style>
    </head>
    <body>
        <div class="container py-5">
            <div class="row justify-content-center">
                <div class="col-md-8">
                    <div class="install-card p-5">
                        <div class="text-center mb-4">
                            <h1>🏪 Brayne Jewelry Manager</h1>
                            <h4 class="text-muted">cPanel Installation</h4>
                        </div>
                        
                        <form method="POST" action="">
                            <h5 class="mb-3">Database Configuration</h5>
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Database Host</label>
                                    <input type="text" class="form-control" name="db_host" value="localhost" required>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Database Name</label>
                                    <input type="text" class="form-control" name="db_name" placeholder="yourusername_jewelry_db" required>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Database Username</label>
                                    <input type="text" class="form-control" name="db_user" placeholder="yourusername_jewelry_user" required>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Database Password</label>
                                    <input type="password" class="form-control" name="db_pass" required>
                                </div>
                            </div>
                            
                            <h5 class="mb-3 mt-4">Application Configuration</h5>
                            <div class="mb-3">
                                <label class="form-label">Application URL</label>
                                <input type="url" class="form-control" name="app_url" placeholder="https://yourdomain.com/jewelry-manager" required>
                            </div>
                            
                            <h5 class="mb-3 mt-4">Admin Account</h5>
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Admin Email</label>
                                    <input type="email" class="form-control" name="admin_email" placeholder="admin@yourdomain.com" required>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Admin Password</label>
                                    <input type="password" class="form-control" name="admin_password" required>
                                </div>
                            </div>
                            
                            <div class="d-grid">
                                <button type="submit" class="btn btn-primary btn-lg">Install Application</button>
                            </div>
                        </form>
                        
                        <div class="mt-4 text-center text-muted">
                            <small>Make sure your database is created in cPanel before running this installation.</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </body>
    </html>';
}

function showSuccessPage($result) {
    echo '<!DOCTYPE html>
    <html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Installation Complete</title>
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
        <style>
            body { background: linear-gradient(135deg, #28a745 0%, #20c997 100%); min-height: 100vh; }
            .success-card { background: white; border-radius: 15px; box-shadow: 0 10px 30px rgba(0,0,0,0.1); }
        </style>
    </head>
    <body>
        <div class="container py-5">
            <div class="row justify-content-center">
                <div class="col-md-8">
                    <div class="success-card p-5 text-center">
                        <h1 class="text-success mb-4">🎉 Installation Complete!</h1>
                        <p class="lead">Your Brayne Jewelry Manager has been successfully installed.</p>
                        
                        <div class="alert alert-info">
                            <h5>Access Information:</h5>
                            <p><strong>URL:</strong> <a href="' . $result['data']['app_url'] . '">' . $result['data']['app_url'] . '</a></p>
                        </div>
                        
                        <div class="alert alert-warning">
                            <h5>Default Login Credentials:</h5>
                            <ul class="list-unstyled">
                                <li><strong>Admin:</strong> ' . $result['data']['admin_email'] . ' / (password you set)</li>
                                <li><strong>Distributor:</strong> distributor1@jewelry.com / password</li>
                                <li><strong>Factory:</strong> factory@jewelry.com / password</li>
                            </ul>
                        </div>
                        
                        <div class="d-grid gap-2">
                            <a href="' . $result['data']['app_url'] . '/login" class="btn btn-primary btn-lg">Go to Login</a>
                            <a href="' . $result['data']['app_url'] . '" class="btn btn-outline-secondary">Visit Dashboard</a>
                        </div>
                        
                        <div class="mt-4">
                            <small class="text-muted">
                                <strong>Important:</strong> Delete this installation file (cpanel-install.php) for security.
                            </small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </body>
    </html>';
}

function showErrorPage($error) {
    echo '<!DOCTYPE html>
    <html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Installation Error</title>
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
        <style>
            body { background: linear-gradient(135deg, #dc3545 0%, #fd7e14 100%); min-height: 100vh; }
            .error-card { background: white; border-radius: 15px; box-shadow: 0 10px 30px rgba(0,0,0,0.1); }
        </style>
    </head>
    <body>
        <div class="container py-5">
            <div class="row justify-content-center">
                <div class="col-md-8">
                    <div class="error-card p-5 text-center">
                        <h1 class="text-danger mb-4">❌ Installation Failed</h1>
                        <div class="alert alert-danger">
                            <h5>Error Details:</h5>
                            <p>' . htmlspecialchars($error) . '</p>
                        </div>
                        
                        <div class="d-grid">
                            <a href="cpanel-install.php" class="btn btn-primary btn-lg">Try Again</a>
                        </div>
                        
                        <div class="mt-4">
                            <small class="text-muted">
                                Please check your database credentials and try again.
                            </small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </body>
    </html>';
}
?> 