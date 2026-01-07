<?php
/**
 * Helper Functions
 * 
 * Common utility functions used throughout the application
 */

/**
 * Sanitize input data
 * 
 * @param string $data Input data to sanitize
 * @return string Sanitized data
 */
function sanitize($data)
{
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data, ENT_QUOTES, 'UTF-8');
    return $data;
}

/**
 * Redirect to a URL
 * 
 * @param string $url URL to redirect to
 * @return void
 */
function redirect($url)
{
    header("Location: " . $url);
    exit();
}

/**
 * Format currency
 * 
 * @param float $amount Amount to format
 * @param string $currency Currency symbol
 * @return string Formatted currency
 */
function formatCurrency($amount, $currency = '$')
{
    return $currency . number_format($amount, 2);
}

/**
 * Format date
 * 
 * @param string $date Date string
 * @param string $format Date format
 * @return string Formatted date
 */
function formatDate($date, $format = 'Y-m-d H:i:s')
{
    return date($format, strtotime($date));
}

/**
 * Check if product is low on stock
 * 
 * @param int $quantity Current quantity
 * @param int $minimum_quantity Minimum quantity threshold
 * @return bool True if low stock
 */
function isLowStock($quantity, $minimum_quantity)
{
    return $quantity <= $minimum_quantity;
}

/**
 * Generate CSRF token
 * 
 * @return string CSRF token
 */
function generateCSRFToken()
{
    if (!isset($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

/**
 * Verify CSRF token
 * 
 * @param string $token Token to verify
 * @return bool True if valid
 */
function verifyCSRFToken($token)
{
    return isset($_SESSION['csrf_token']) && hash_equals($_SESSION['csrf_token'], $token);
}

/**
 * Flash message helper
 * 
 * @param string $type Message type (success, error, warning, info)
 * @param string $message Message content
 * @return void
 */
function setFlashMessage($type, $message)
{
    $_SESSION['flash_message'] = [
        'type' => $type,
        'message' => $message
    ];
}

/**
 * Get and clear flash message
 * 
 * @return array|null Flash message or null
 */
function getFlashMessage()
{
    if (isset($_SESSION['flash_message'])) {
        $message = $_SESSION['flash_message'];
        unset($_SESSION['flash_message']);
        return $message;
    }
    return null;
}

/**
 * Check if user is logged in
 * 
 * @return bool True if logged in
 */
function isLoggedIn()
{
    return isset($_SESSION['user_id']);
}

/**
 * Get current user ID
 * 
 * @return int|null User ID or null
 */
function getCurrentUserId()
{
    return $_SESSION['user_id'] ?? null;
}

/**
 * Get current user role
 * 
 * @return string|null User role or null
 */
function getCurrentUserRole()
{
    return $_SESSION['user_role'] ?? null;
}

/**
 * Check if user has role
 * 
 * @param string|array $roles Role(s) to check
 * @return bool True if user has role
 */
function hasRole($roles)
{
    if (!isLoggedIn()) {
        return false;
    }

    $userRole = getCurrentUserRole();

    if (is_array($roles)) {
        return in_array($userRole, $roles);
    }

    return $userRole === $roles;
}

/**
 * Require login
 * Redirect to login page if not logged in
 * 
 * @return void
 */
function requireLogin()
{
    if (!isLoggedIn()) {
        redirect('index.php?page=login');
    }
}

/**
 * Require role
 * Redirect to dashboard if user doesn't have required role
 * 
 * @param string|array $roles Required role(s)
 * @return void
 */
function requireRole($roles)
{
    requireLogin();

    if (!hasRole($roles)) {
        setFlashMessage('error', 'You do not have permission to access this page.');
        redirect('index.php?page=dashboard');
    }
}

/**
 * Validate SKU format
 * 
 * @param string $sku SKU to validate
 * @return bool True if valid
 */
function isValidSKU($sku)
{
    // SKU should be alphanumeric with hyphens, 3-50 characters
    return preg_match('/^[A-Z0-9\-]{3,50}$/i', $sku);
}

/**
 * Generate pagination HTML
 * 
 * @param int $currentPage Current page number
 * @param int $totalPages Total number of pages
 * @param string $baseUrl Base URL for pagination links
 * @return string Pagination HTML
 */
function generatePagination($currentPage, $totalPages, $baseUrl)
{
    if ($totalPages <= 1) {
        return '';
    }

    $html = '<div class="pagination">';

    // Previous button
    if ($currentPage > 1) {
        $html .= '<a href="' . $baseUrl . '&page=' . ($currentPage - 1) . '" class="page-link">&laquo; Previous</a>';
    }

    // Page numbers
    for ($i = 1; $i <= $totalPages; $i++) {
        if ($i == $currentPage) {
            $html .= '<span class="page-link active">' . $i . '</span>';
        } else {
            $html .= '<a href="' . $baseUrl . '&page=' . $i . '" class="page-link">' . $i . '</a>';
        }
    }

    // Next button
    if ($currentPage < $totalPages) {
        $html .= '<a href="' . $baseUrl . '&page=' . ($currentPage + 1) . '" class="page-link">Next &raquo;</a>';
    }

    $html .= '</div>';

    return $html;
}
