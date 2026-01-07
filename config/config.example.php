<?php
/**
 * Configuration Template
 * 
 * Copy this file to config.php and update with your actual credentials
 * IMPORTANT: config.php is git-ignored for security
 */

// Database Configuration
define('DB_HOST', 'localhost');
define('DB_NAME', 'stock_management_system');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_CHARSET', 'utf8mb4');

// Application Configuration
define('APP_NAME', 'Stock Management System');
define('APP_URL', 'http://localhost/Stock%20Management%20System');
define('APP_ENV', 'development'); // development or production

// Session Configuration
define('SESSION_LIFETIME', 3600); // 1 hour in seconds
define('SESSION_NAME', 'STOCK_MGMT_SESSION');

// Timezone
date_default_timezone_set('UTC');

// Error Reporting
if (APP_ENV === 'development') {
    error_reporting(E_ALL);
    ini_set('display_errors', 1);
} else {
    error_reporting(0);
    ini_set('display_errors', 0);
    ini_set('log_errors', 1);
    ini_set('error_log', __DIR__ . '/../logs/error.log');
}

// Pagination
define('ITEMS_PER_PAGE', 20);

// Security
define('PASSWORD_MIN_LENGTH', 6);
