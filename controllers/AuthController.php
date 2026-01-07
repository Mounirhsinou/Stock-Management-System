<?php
/**
 * Authentication Controller
 * 
 * Handles user authentication and session management
 */

require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../models/User.php';
require_once __DIR__ . '/../includes/helpers.php';

class AuthController
{
    private $userModel;

    public function __construct()
    {
        $this->userModel = new User();

        // Start session if not already started
        if (session_status() === PHP_SESSION_NONE) {
            session_name(SESSION_NAME);
            session_start();
        }
    }

    /**
     * Show login page
     */
    public function showLogin()
    {
        // Redirect if already logged in
        if (isLoggedIn()) {
            redirect('index.php?page=dashboard');
        }

        require_once __DIR__ . '/../views/auth/login.php';
    }

    /**
     * Handle login form submission
     */
    public function login()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            redirect('index.php?page=login');
        }

        $username = sanitize($_POST['username'] ?? '');
        $password = $_POST['password'] ?? '';

        // Validate input
        if (empty($username) || empty($password)) {
            setFlashMessage('error', 'Please enter both username and password.');
            redirect('index.php?page=login');
        }

        // Authenticate user
        $user = $this->userModel->authenticate($username, $password);

        if ($user) {
            // Set session variables
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['full_name'] = $user['full_name'];
            $_SESSION['user_role'] = $user['role'];
            $_SESSION['logged_in'] = true;
            $_SESSION['last_activity'] = time();

            // Regenerate session ID for security
            session_regenerate_id(true);

            setFlashMessage('success', 'Welcome back, ' . $user['full_name'] . '!');
            redirect('index.php?page=dashboard');
        } else {
            setFlashMessage('error', 'Invalid username or password.');
            redirect('index.php?page=login');
        }
    }

    /**
     * Handle logout
     */
    public function logout()
    {
        // Destroy session
        $_SESSION = [];

        if (isset($_COOKIE[session_name()])) {
            setcookie(session_name(), '', time() - 3600, '/');
        }

        session_destroy();

        redirect('index.php?page=login');
    }

    /**
     * Check if user is authenticated
     * Redirect to login if not
     */
    public function checkAuth()
    {
        if (!isLoggedIn()) {
            redirect('index.php?page=login');
        }

        // Check session timeout
        if (isset($_SESSION['last_activity']) && (time() - $_SESSION['last_activity'] > SESSION_LIFETIME)) {
            $this->logout();
        }

        // Update last activity time
        $_SESSION['last_activity'] = time();
    }

    /**
     * Check if user has required role
     * 
     * @param string|array $roles Required role(s)
     */
    public function checkRole($roles)
    {
        $this->checkAuth();

        if (!hasRole($roles)) {
            setFlashMessage('error', 'You do not have permission to access this page.');
            redirect('index.php?page=dashboard');
        }
    }
}
