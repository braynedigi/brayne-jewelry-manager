<?php
/**
 * cPanel Configuration Helper
 * This file helps configure Laravel for cPanel hosting
 */

// Set proper error reporting for production
error_reporting(E_ALL);
ini_set('display_errors', 0);
ini_set('log_errors', 1);

// Set timezone
date_default_timezone_set('UTC');

// Ensure proper session handling
ini_set('session.cookie_httponly', 1);
ini_set('session.use_only_cookies', 1);
ini_set('session.cookie_secure', 1);

// Set memory limit
ini_set('memory_limit', '256M');

// Set max execution time
ini_set('max_execution_time', 300);

// Set upload limits
ini_set('upload_max_filesize', '10M');
ini_set('post_max_size', '10M');

echo "cPanel configuration applied successfully!";
?> 