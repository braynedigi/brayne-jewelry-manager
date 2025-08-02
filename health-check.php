<?php

// Simple health check for CapRover
// This file should be accessible at /health-check.php

header('Content-Type: application/json');

try {
    // Check if Laravel is working
    if (file_exists(__DIR__ . '/vendor/autoload.php')) {
        require_once __DIR__ . '/vendor/autoload.php';
        
        // Check if we can load the application
        $app = require_once __DIR__ . '/bootstrap/app.php';
        
        echo json_encode([
            'status' => 'healthy',
            'timestamp' => date('Y-m-d H:i:s'),
            'laravel_version' => app()->version(),
            'environment' => app()->environment()
        ]);
    } else {
        throw new Exception('Laravel not properly installed');
    }
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'status' => 'unhealthy',
        'error' => $e->getMessage(),
        'timestamp' => date('Y-m-d H:i:s')
    ]);
} 