<?php
/**
 * Brayne Jewelry Manager - Installation Script
 * 
 * This script handles the actual installation process
 */

class JewelryManagerInstaller {
    private $errors = [];
    private $success = [];
    
    public function install($config) {
        try {
            // Step 1: Create .env file
            $this->createEnvFile($config);
            $this->addSuccess('Environment file created');
            
            // Step 2: Generate application key
            $this->generateAppKey();
            $this->addSuccess('Application key generated');
            
            // Step 3: Run database migrations
            $this->runMigrations();
            $this->addSuccess('Database migrations completed');
            
            // Step 4: Seed database
            $this->seedDatabase($config);
            $this->addSuccess('Database seeded with initial data');
            
            // Step 5: Create storage link
            $this->createStorageLink();
            $this->addSuccess('Storage link created');
            
            // Step 6: Cache configurations
            $this->cacheConfigurations();
            $this->addSuccess('Configurations cached');
            
            // Step 7: Set file permissions
            $this->setPermissions();
            $this->addSuccess('File permissions set');
            
            return true;
            
        } catch (Exception $e) {
            $this->addError('Installation failed: ' . $e->getMessage());
            return false;
        }
    }
    
    private function createEnvFile($config) {
        $env_content = $this->generateEnvContent($config);
        $result = file_put_contents('../.env', $env_content);
        
        if ($result === false) {
            throw new Exception('Could not create .env file');
        }
    }
    
    private function generateEnvContent($config) {
        return "APP_NAME=\"{$config['app_name']}\"
APP_ENV=production
APP_KEY=
APP_DEBUG=false
APP_URL={$config['app_url']}

LOG_CHANNEL=stack
LOG_DEPRECATIONS_CHANNEL=null
LOG_LEVEL=error

DB_CONNECTION=mysql
DB_HOST={$config['db_host']}
DB_PORT=3306
DB_DATABASE={$config['db_name']}
DB_USERNAME={$config['db_user']}
DB_PASSWORD={$config['db_pass']}

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
MAIL_FROM_ADDRESS=\"noreply@" . parse_url($config['app_url'], PHP_URL_HOST) . "\"
MAIL_FROM_NAME=\"{$config['app_name']}\"

SESSION_SECURE_COOKIE=true
PASSWORD_TIMEOUT=10800
LOGIN_THROTTLE=6
LOGIN_THROTTLE_DECAY=60";
    }
    
    private function generateAppKey() {
        $app_key = 'base64:' . base64_encode(random_bytes(32));
        
        $env_content = file_get_contents('../.env');
        $env_content = str_replace('APP_KEY=', 'APP_KEY=' . $app_key, $env_content);
        file_put_contents('../.env', $env_content);
    }
    
    private function runMigrations() {
        chdir('../');
        
        // Run migrations
        $output = [];
        $return_var = 0;
        exec('php artisan migrate:fresh --force 2>&1', $output, $return_var);
        
        if ($return_var !== 0) {
            throw new Exception('Migration failed: ' . implode("\n", $output));
        }
    }
    
    private function seedDatabase($config) {
        // Create admin user with custom credentials
        $this->createAdminUser($config);
        
        // Run default seeder
        $output = [];
        $return_var = 0;
        exec('php artisan db:seed --force 2>&1', $output, $return_var);
        
        if ($return_var !== 0) {
            // If seeder fails, create basic data manually
            $this->createBasicData();
        }
    }
    
    private function createAdminUser($config) {
        // Connect to database
        $pdo = new PDO(
            "mysql:host={$config['db_host']};dbname={$config['db_name']}", 
            $config['db_user'], 
            $config['db_pass']
        );
        
        // Create admin user
        $hashed_password = password_hash($config['admin_password'], PASSWORD_DEFAULT);
        $stmt = $pdo->prepare("
            INSERT INTO users (name, email, password, role, created_at, updated_at) 
            VALUES (?, ?, ?, 'admin', NOW(), NOW())
        ");
        $stmt->execute(['Admin User', $config['admin_email'], $hashed_password]);
    }
    
    private function createBasicData() {
        // Create basic data if seeder fails
        $pdo = new PDO(
            "mysql:host={$_SESSION['db_config']['host']};dbname={$_SESSION['db_config']['name']}", 
            $_SESSION['db_config']['user'], 
            $_SESSION['db_config']['pass']
        );
        
        // Create distributor users
        $distributor1_password = password_hash('password', PASSWORD_DEFAULT);
        $distributor2_password = password_hash('password', PASSWORD_DEFAULT);
        $factory_password = password_hash('password', PASSWORD_DEFAULT);
        
        $stmt = $pdo->prepare("
            INSERT INTO users (name, email, password, role, created_at, updated_at) 
            VALUES 
            ('John Distributor', 'distributor1@jewelry.com', ?, 'distributor', NOW(), NOW()),
            ('Jane Distributor', 'distributor2@jewelry.com', ?, 'distributor', NOW(), NOW()),
            ('Factory Manager', 'factory@jewelry.com', ?, 'factory', NOW(), NOW())
        ");
        $stmt->execute([$distributor1_password, $distributor2_password, $factory_password]);
        
        // Create basic products
        $stmt = $pdo->prepare("
            INSERT INTO products (name, price, sku, category, is_active, created_at, updated_at) 
            VALUES 
            ('Diamond Ring', 2500.00, 'DR-001', 'Rings', 1, NOW(), NOW()),
            ('Pearl Necklace', 800.00, 'PN-001', 'Necklaces', 1, NOW(), NOW()),
            ('Sapphire Earrings', 1200.00, 'SE-001', 'Earrings', 1, NOW(), NOW()),
            ('Gold Bracelet', 600.00, 'GB-001', 'Bracelets', 1, NOW(), NOW())
        ");
        $stmt->execute();
        
        // Create couriers
        $stmt = $pdo->prepare("
            INSERT INTO couriers (name, phone, email, is_active, created_at, updated_at) 
            VALUES 
            ('Express Delivery', '+1-555-0303', 'express@delivery.com', 1, NOW(), NOW()),
            ('Fast Shipping Co.', '+1-555-0404', 'fast@shipping.com', 1, NOW(), NOW()),
            ('Premium Logistics', '+1-555-0505', 'premium@logistics.com', 1, NOW(), NOW())
        ");
        $stmt->execute();
    }
    
    private function createStorageLink() {
        // Create storage directory if it doesn't exist
        if (!is_dir('../public/storage')) {
            mkdir('../public/storage', 0755, true);
        }
        
        // Copy storage files if symbolic link fails
        if (!is_link('../public/storage')) {
            $this->copyDirectory('../storage/app/public', '../public/storage');
        }
    }
    
    private function copyDirectory($source, $destination) {
        if (!is_dir($destination)) {
            mkdir($destination, 0755, true);
        }
        
        $dir = opendir($source);
        while (($file = readdir($dir)) !== false) {
            if ($file != '.' && $file != '..') {
                $source_path = $source . '/' . $file;
                $dest_path = $destination . '/' . $file;
                
                if (is_dir($source_path)) {
                    $this->copyDirectory($source_path, $dest_path);
                } else {
                    copy($source_path, $dest_path);
                }
            }
        }
        closedir($dir);
    }
    
    private function cacheConfigurations() {
        chdir('../');
        
        $commands = [
            'php artisan config:cache --force',
            'php artisan route:cache --force',
            'php artisan view:cache --force'
        ];
        
        foreach ($commands as $command) {
            $output = [];
            $return_var = 0;
            exec($command . ' 2>&1', $output, $return_var);
            
            if ($return_var !== 0) {
                // Log warning but don't fail installation
                $this->addError('Warning: ' . $command . ' failed');
            }
        }
    }
    
    private function setPermissions() {
        $directories = [
            '../storage' => 0775,
            '../bootstrap/cache' => 0775,
            '../public/storage' => 0775
        ];
        
        foreach ($directories as $dir => $permission) {
            if (is_dir($dir)) {
                chmod($dir, $permission);
            }
        }
    }
    
    private function addError($message) {
        $this->errors[] = $message;
    }
    
    private function addSuccess($message) {
        $this->success[] = $message;
    }
    
    public function getErrors() {
        return $this->errors;
    }
    
    public function getSuccess() {
        return $this->success;
    }
}

// Handle AJAX installation request
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'install') {
    header('Content-Type: application/json');
    
    $config = [
        'db_host' => $_POST['db_host'],
        'db_name' => $_POST['db_name'],
        'db_user' => $_POST['db_user'],
        'db_pass' => $_POST['db_pass'],
        'app_name' => $_POST['app_name'],
        'app_url' => $_POST['app_url'],
        'admin_email' => $_POST['admin_email'],
        'admin_password' => $_POST['admin_password']
    ];
    
    $installer = new JewelryManagerInstaller();
    $success = $installer->install($config);
    
    echo json_encode([
        'success' => $success,
        'errors' => $installer->getErrors(),
        'messages' => $installer->getSuccess()
    ]);
    exit;
}
?> 