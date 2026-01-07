<?php
/**
 * Stock Management System - Main Entry Point
 * 
 * Simple routing system for MVC architecture
 */

// Start session and load configuration
session_start();
require_once __DIR__ . '/config/config.php';
require_once __DIR__ . '/includes/helpers.php';

// Get page and action from URL
$page = isset($_GET['page']) ? sanitize($_GET['page']) : 'login';
$action = isset($_GET['action']) ? sanitize($_GET['action']) : 'index';

// Route to appropriate controller
try {
    switch ($page) {
        case 'login':
            require_once __DIR__ . '/controllers/AuthController.php';
            $controller = new AuthController();

            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                $controller->login();
            } else {
                $controller->showLogin();
            }
            break;

        case 'logout':
            require_once __DIR__ . '/controllers/AuthController.php';
            $controller = new AuthController();
            $controller->logout();
            break;

        case 'dashboard':
            require_once __DIR__ . '/controllers/DashboardController.php';
            $controller = new DashboardController();
            $controller->index();
            break;

        case 'products':
            require_once __DIR__ . '/controllers/ProductController.php';
            $controller = new ProductController();

            switch ($action) {
                case 'view':
                    $controller->view();
                    break;
                case 'create':
                    $controller->create();
                    break;
                case 'edit':
                    $controller->edit();
                    break;
                case 'delete':
                    $controller->delete();
                    break;
                case 'export':
                    $controller->export();
                    break;
                default:
                    $controller->index();
            }
            break;

        case 'users':
            require_once __DIR__ . '/controllers/UserController.php';
            $controller = new UserController();

            switch ($action) {
                case 'create':
                    $controller->create();
                    break;
                case 'edit':
                    $controller->edit();
                    break;
                case 'delete':
                    $controller->delete();
                    break;
                default:
                    $controller->index();
            }
            break;

        case 'stock':
            require_once __DIR__ . '/controllers/StockController.php';
            $controller = new StockController();

            switch ($action) {
                case 'in':
                    $controller->stockIn();
                    break;
                case 'out':
                    $controller->stockOut();
                    break;
                default:
                    $controller->index();
            }
            break;

        case 'suppliers':
            require_once __DIR__ . '/controllers/SupplierController.php';
            $controller = new SupplierController();

            switch ($action) {
                case 'create':
                    $controller->create();
                    break;
                case 'edit':
                    $controller->edit();
                    break;
                case 'delete':
                    $controller->delete();
                    break;
                default:
                    $controller->index();
            }
            break;

        default:
            // Redirect to login if page not found
            if (!isLoggedIn()) {
                redirect('index.php?page=login');
            } else {
                redirect('index.php?page=dashboard');
            }
    }

} catch (Exception $e) {
    // Log error
    error_log("Application Error: " . $e->getMessage());

    // Show user-friendly error
    if (APP_ENV === 'development') {
        die("Error: " . $e->getMessage());
    } else {
        die("An error occurred. Please try again later.");
    }
}
