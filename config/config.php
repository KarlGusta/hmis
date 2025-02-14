<?php
// Application configuration
define('APP_NAME', 'Hospital Management System');
define('APP_VERSION', '1.0.0');
define('BASE_URL', 'http://localhost/hmis');

// Session configuration
define('SESSION_LIFETIME', 3600); // 1 hour
define('SESSION_NAME', 'HMS_SESSION');

// Security configuration
define('PASSWORD_MIN_LENGTH', 8);
define('PASSWORD_REQUIRES_SPECIAL', true);
define('PASSWORD_REQUIRES_NUMBER', true);
define('PASSWORD_REQUIRES_UPPERCASE', true);

// File upload configuration
define('MAX_FILE_SIZE', 5242880); // 5MB
define('ALLOWED_FILE_TYPES', ['jpg', 'jpeg', 'png', 'pdf']);
define('UPLOAD_PATH', __DIR__ . '/../uploads/');

// Email configuration
define('SMTP_HOST', 'smtp.example.com');
define('SMTP_PORT', 587);
define('SMTP_USERNAME', 'your-email@example.com');
define('SMTP_PASSWORD', 'your-smtp-password');
define('SMTP_FROM_EMAIL', 'noreply@yourhospital.com');
define('SMTP_FROM_NAME', 'Hospital Management System');

// Error reporting
error_reporting(E_ALL);
ini_set('display_errors', 0);
ini_set('log_errors', 1);
ini_set('error_log', __DIR__ . '/../logs/error.log');

// Database configuration constants
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'hospital_management');

// Time zone
date_default_timezone_set('UTC');

// System paths
define('ROOT_PATH', dirname(__DIR__));
define('INCLUDES_PATH', ROOT_PATH . '/includes');
define('CLASSES_PATH', ROOT_PATH . '/classes');
define('TEMPLATES_PATH', ROOT_PATH . '/templates');

// Cache configuration
define('CACHE_ENABLED', true);
define('CACHE_LIFETIME', 3600);
define('CACHE_PATH', ROOT_PATH . '/cache');

// API configuration
define('API_VERSION', 'v1');
define('API_KEY_REQUIRED', true);
define('API_RATE_LIMIT', 100); // requests per hour

// Pagination defaults
define('ITEMS_PER_PAGE', 20);
define('MAX_PAGINATION_LINKS', 5);

// Security headers
header('X-Frame-Options: DENY');
header('X-XSS-Protection: 1; mode=block');
header('X-Content-Type-Options: nosniff');
header('Strict-Transport-Security: max-age=31536000; includeSubDomains');

// Initialize session with secure settings
function initSession() {
    ini_set('session.cookie_httponly', 1);
    ini_set('session.use_only_cookies', 1);
    ini_set('session.cookie_secure', 1);
    ini_set('session.cookie_samesite', 'Strict');
    ini_set('session.gc_maxlifetime', SESSION_LIFETIME);
    session_name(SESSION_NAME);
    session_start();
}

// Load environment-specific configuration
$env = getenv('APP_ENV') ?: 'production';
$envFile = __DIR__ . "/environments/{$env}.php";
if (file_exists($envFile)) {
    require_once $envFile;
} 
?>