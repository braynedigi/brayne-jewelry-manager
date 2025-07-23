<?php
/**
 * Brayne Jewelry Manager - Web Installer
 * 
 * This installer will guide you through setting up the application
 * Visit: https://orders.braynejewelry.com/install
 */

session_start();

// Check if already installed
if (file_exists('../.env') && !isset($_GET['force'])) {
    die('Application is already installed. Add ?force=1 to reinstall.');
}

$step = isset($_GET['step']) ? (int)$_GET['step'] : 1;
$error = '';
$success = '';

// Handle form submissions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    switch ($step) {
        case 2:
            // Database configuration
            $db_host = $_POST['db_host'] ?? 'localhost';
            $db_name = $_POST['db_name'] ?? '';
            $db_user = $_POST['db_user'] ?? '';
            $db_pass = $_POST['db_pass'] ?? '';
            
            // Test database connection
            try {
                $pdo = new PDO("mysql:host=$db_host;dbname=$db_name", $db_user, $db_pass);
                $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
                
                $_SESSION['db_config'] = [
                    'host' => $db_host,
                    'name' => $db_name,
                    'user' => $db_user,
                    'pass' => $db_pass
                ];
                
                header('Location: ?step=3');
                exit;
            } catch (PDOException $e) {
                $error = 'Database connection failed: ' . $e->getMessage();
            }
            break;
            
        case 3:
            // Application configuration
            $app_name = $_POST['app_name'] ?? 'Brayne Jewelry Manager';
            $app_url = $_POST['app_url'] ?? '';
            $admin_email = $_POST['admin_email'] ?? '';
            $admin_password = $_POST['admin_password'] ?? '';
            
            if (empty($app_url) || empty($admin_email) || empty($admin_password)) {
                $error = 'All fields are required.';
            } else {
                $_SESSION['app_config'] = [
                    'name' => $app_name,
                    'url' => $app_url,
                    'admin_email' => $admin_email,
                    'admin_password' => $admin_password
                ];
                
                // Proceed with installation
                if (installApplication()) {
                    header('Location: ?step=4');
                    exit;
                } else {
                    $error = 'Installation failed. Check error logs.';
                }
            }
            break;
    }
}

function installApplication() {
    try {
        // 1. Create .env file
        $env_content = generateEnvFile();
        file_put_contents('../.env', $env_content);
        
        // 2. Generate application key
        $app_key = 'base64:' . base64_encode(random_bytes(32));
        $env_content = str_replace('APP_KEY=', 'APP_KEY=' . $app_key, $env_content);
        file_put_contents('../.env', $env_content);
        
        // 3. Run migrations
        chdir('../');
        exec('php artisan migrate:fresh --force', $output, $return);
        
        // 4. Run seeder
        exec('php artisan db:seed --force', $output, $return);
        
        // 5. Create storage link
        exec('php artisan storage:link --force', $output, $return);
        
        // 6. Cache configurations
        exec('php artisan config:cache --force', $output, $return);
        exec('php artisan route:cache --force', $output, $return);
        exec('php artisan view:cache --force', $output, $return);
        
        return true;
    } catch (Exception $e) {
        error_log('Installation error: ' . $e->getMessage());
        return false;
    }
}

function generateEnvFile() {
    $db_config = $_SESSION['db_config'];
    $app_config = $_SESSION['app_config'];
    
    return "APP_NAME=\"{$app_config['name']}\"
APP_ENV=production
APP_KEY=
APP_DEBUG=false
APP_URL={$app_config['url']}

LOG_CHANNEL=stack
LOG_DEPRECATIONS_CHANNEL=null
LOG_LEVEL=error

DB_CONNECTION=mysql
DB_HOST={$db_config['host']}
DB_PORT=3306
DB_DATABASE={$db_config['name']}
DB_USERNAME={$db_config['user']}
DB_PASSWORD={$db_config['pass']}

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
MAIL_FROM_ADDRESS=\"noreply@{$app_config['url']}\"
MAIL_FROM_NAME=\"{$app_config['name']}\"

SESSION_SECURE_COOKIE=true
PASSWORD_TIMEOUT=10800
LOGIN_THROTTLE=6
LOGIN_THROTTLE_DECAY=60";
}

function checkRequirements() {
    $requirements = [];
    
    // PHP version
    $requirements['php_version'] = [
        'name' => 'PHP Version',
        'required' => '8.2.0',
        'current' => PHP_VERSION,
        'status' => version_compare(PHP_VERSION, '8.2.0', '>=')
    ];
    
    // Extensions
    $extensions = ['pdo', 'pdo_mysql', 'mbstring', 'openssl', 'tokenizer', 'xml', 'ctype', 'json'];
    foreach ($extensions as $ext) {
        $requirements['ext_' . $ext] = [
            'name' => "PHP Extension: $ext",
            'required' => 'Installed',
            'current' => extension_loaded($ext) ? 'Installed' : 'Missing',
            'status' => extension_loaded($ext)
        ];
    }
    
    // Directory permissions
    $directories = ['../storage', '../bootstrap/cache'];
    foreach ($directories as $dir) {
        $requirements['dir_' . str_replace(['../', '/'], ['', '_'], $dir)] = [
            'name' => "Directory Writable: $dir",
            'required' => 'Writable',
            'current' => is_writable($dir) ? 'Writable' : 'Not Writable',
            'status' => is_writable($dir)
        ];
    }
    
    return $requirements;
}

$requirements = checkRequirements();
$all_requirements_met = true;
foreach ($requirements as $req) {
    if (!$req['status']) {
        $all_requirements_met = false;
        break;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Brayne Jewelry Manager - Installer</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        body { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); min-height: 100vh; }
        .installer-card { background: white; border-radius: 15px; box-shadow: 0 10px 30px rgba(0,0,0,0.1); }
        .step-indicator { background: #f8f9fa; border-radius: 10px; padding: 20px; margin-bottom: 30px; }
        .step { display: inline-block; width: 40px; height: 40px; border-radius: 50%; text-align: center; line-height: 40px; margin: 0 10px; }
        .step.active { background: #007bff; color: white; }
        .step.completed { background: #28a745; color: white; }
        .step.pending { background: #dee2e6; color: #6c757d; }
        .requirement-item { padding: 10px; border-radius: 5px; margin-bottom: 10px; }
        .requirement-item.success { background: #d4edda; border: 1px solid #c3e6cb; }
        .requirement-item.danger { background: #f8d7da; border: 1px solid #f5c6cb; }
    </style>
</head>
<body>
    <div class="container py-5">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="installer-card p-5">
                    <!-- Header -->
                    <div class="text-center mb-4">
                        <h1 class="mb-3">
                            <i class="fas fa-gem text-primary"></i>
                            Brayne Jewelry Manager
                        </h1>
                        <h4 class="text-muted">Web Installer</h4>
                    </div>

                    <!-- Step Indicator -->
                    <div class="step-indicator text-center">
                        <div class="step <?= $step >= 1 ? ($step == 1 ? 'active' : 'completed') : 'pending' ?>">1</div>
                        <div class="step <?= $step >= 2 ? ($step == 2 ? 'active' : 'completed') : 'pending' ?>">2</div>
                        <div class="step <?= $step >= 3 ? ($step == 3 ? 'active' : 'completed') : 'pending' ?>">3</div>
                        <div class="step <?= $step >= 4 ? 'completed' : 'pending' ?>">4</div>
                    </div>

                    <!-- Error/Success Messages -->
                    <?php if ($error): ?>
                        <div class="alert alert-danger">
                            <i class="fas fa-exclamation-triangle"></i> <?= htmlspecialchars($error) ?>
                        </div>
                    <?php endif; ?>

                    <?php if ($success): ?>
                        <div class="alert alert-success">
                            <i class="fas fa-check-circle"></i> <?= htmlspecialchars($success) ?>
                        </div>
                    <?php endif; ?>

                    <!-- Step Content -->
                    <?php if ($step == 1): ?>
                        <!-- Step 1: Requirements Check -->
                        <div class="text-center mb-4">
                            <h3><i class="fas fa-clipboard-check"></i> System Requirements</h3>
                            <p class="text-muted">Checking if your server meets the requirements...</p>
                        </div>

                        <?php foreach ($requirements as $req): ?>
                            <div class="requirement-item <?= $req['status'] ? 'success' : 'danger' ?>">
                                <div class="row align-items-center">
                                    <div class="col-md-6">
                                        <strong><?= htmlspecialchars($req['name']) ?></strong>
                                    </div>
                                    <div class="col-md-3">
                                        <small class="text-muted">Required: <?= htmlspecialchars($req['required']) ?></small>
                                    </div>
                                    <div class="col-md-3 text-end">
                                        <span class="badge <?= $req['status'] ? 'bg-success' : 'bg-danger' ?>">
                                            <?= htmlspecialchars($req['current']) ?>
                                        </span>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>

                        <div class="text-center mt-4">
                            <?php if ($all_requirements_met): ?>
                                <a href="?step=2" class="btn btn-primary btn-lg">
                                    <i class="fas fa-arrow-right"></i> Continue to Database Setup
                                </a>
                            <?php else: ?>
                                <div class="alert alert-warning">
                                    <i class="fas fa-exclamation-triangle"></i>
                                    Please fix the requirements above before continuing.
                                </div>
                            <?php endif; ?>
                        </div>

                    <?php elseif ($step == 2): ?>
                        <!-- Step 2: Database Configuration -->
                        <div class="text-center mb-4">
                            <h3><i class="fas fa-database"></i> Database Configuration</h3>
                            <p class="text-muted">Enter your database connection details</p>
                        </div>

                        <form method="POST" action="?step=2">
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
                            <div class="text-center">
                                <button type="submit" class="btn btn-primary btn-lg">
                                    <i class="fas fa-database"></i> Test Connection & Continue
                                </button>
                            </div>
                        </form>

                    <?php elseif ($step == 3): ?>
                        <!-- Step 3: Application Configuration -->
                        <div class="text-center mb-4">
                            <h3><i class="fas fa-cog"></i> Application Configuration</h3>
                            <p class="text-muted">Configure your application settings</p>
                        </div>

                        <form method="POST" action="?step=3">
                            <div class="mb-3">
                                <label for="app_name" class="form-label">Application Name</label>
                                <input type="text" class="form-control" id="app_name" name="app_name" value="Brayne Jewelry Manager" required>
                            </div>
                            <div class="mb-3">
                                <label for="app_url" class="form-label">Application URL</label>
                                <input type="url" class="form-control" id="app_url" name="app_url" value="https://orders.braynejewelry.com" required>
                                <div class="form-text">Include https:// and no trailing slash</div>
                            </div>
                            <div class="mb-3">
                                <label for="admin_email" class="form-label">Admin Email</label>
                                <input type="email" class="form-control" id="admin_email" name="admin_email" required>
                            </div>
                            <div class="mb-3">
                                <label for="admin_password" class="form-label">Admin Password</label>
                                <input type="password" class="form-control" id="admin_password" name="admin_password" required>
                                <div class="form-text">This will be your admin login password</div>
                            </div>
                            <div class="text-center">
                                <button type="submit" class="btn btn-success btn-lg">
                                    <i class="fas fa-rocket"></i> Install Application
                                </button>
                            </div>
                        </form>

                    <?php elseif ($step == 4): ?>
                        <!-- Step 4: Installation Complete -->
                        <div class="text-center">
                            <div class="mb-4">
                                <i class="fas fa-check-circle text-success" style="font-size: 4rem;"></i>
                            </div>
                            <h3 class="text-success">Installation Complete!</h3>
                            <p class="text-muted">Your Brayne Jewelry Manager has been successfully installed.</p>
                            
                            <div class="alert alert-info">
                                <h5><i class="fas fa-info-circle"></i> Next Steps:</h5>
                                <ul class="text-start">
                                    <li>Login to your application</li>
                                    <li>Change default passwords</li>
                                    <li>Configure email settings</li>
                                    <li>Add your company logo</li>
                                </ul>
                            </div>

                            <div class="row mt-4">
                                <div class="col-md-6">
                                    <a href="../login" class="btn btn-primary btn-lg w-100">
                                        <i class="fas fa-sign-in-alt"></i> Go to Login
                                    </a>
                                </div>
                                <div class="col-md-6">
                                    <a href="../" class="btn btn-outline-primary btn-lg w-100">
                                        <i class="fas fa-home"></i> Go to Homepage
                                    </a>
                                </div>
                            </div>

                            <div class="mt-4">
                                <small class="text-muted">
                                    <i class="fas fa-shield-alt"></i>
                                    For security, you should delete the install directory after installation.
                                </small>
                            </div>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html> 