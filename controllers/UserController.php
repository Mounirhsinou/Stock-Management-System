<?php
/**
 * User Controller
 * 
 * Handles user management operations (Admin only)
 */

require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../models/User.php';
require_once __DIR__ . '/../includes/helpers.php';

class UserController
{
    private $userModel;

    public function __construct()
    {
        $this->userModel = new User();
    }

    /**
     * List all users
     */
    public function index()
    {
        requireRole('admin');

        $page = isset($_GET['p']) ? (int) $_GET['p'] : 1;
        $users = $this->userModel->getAll($page, ITEMS_PER_PAGE);
        $totalUsers = $this->userModel->getTotalCount();
        $totalPages = ceil($totalUsers / ITEMS_PER_PAGE);

        require_once __DIR__ . '/../views/users/index.php';
    }

    /**
     * Show create user form
     */
    public function create()
    {
        requireRole('admin');

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->store();
            return;
        }

        $user = null; // For form reuse
        require_once __DIR__ . '/../views/users/form.php';
    }

    /**
     * Store new user
     */
    private function store()
    {
        requireRole('admin');

        $errors = $this->validateUser($_POST);

        if (!empty($errors)) {
            setFlashMessage('error', implode('<br>', $errors));
            redirect('index.php?page=users&action=create');
        }

        $data = [
            'username' => sanitize($_POST['username']),
            'email' => sanitize($_POST['email']),
            'password' => $_POST['password'],
            'full_name' => sanitize($_POST['full_name']),
            'role' => $_POST['role'],
            'is_active' => isset($_POST['is_active']) ? 1 : 0
        ];

        if ($this->userModel->create($data)) {
            setFlashMessage('success', 'User created successfully.');
            redirect('index.php?page=users');
        } else {
            setFlashMessage('error', 'Failed to create user.');
            redirect('index.php?page=users&action=create');
        }
    }

    /**
     * Show edit user form
     */
    public function edit()
    {
        requireRole('admin');

        $id = isset($_GET['id']) ? (int) $_GET['id'] : 0;
        $user = $this->userModel->getById($id);

        if (!$user) {
            setFlashMessage('error', 'User not found.');
            redirect('index.php?page=users');
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->update($id);
            return;
        }

        require_once __DIR__ . '/../views/users/form.php';
    }

    /**
     * Update user
     */
    private function update($id)
    {
        requireRole('admin');

        $errors = $this->validateUser($_POST, $id);

        if (!empty($errors)) {
            setFlashMessage('error', implode('<br>', $errors));
            redirect('index.php?page=users&action=edit&id=' . $id);
        }

        $data = [
            'username' => sanitize($_POST['username']),
            'email' => sanitize($_POST['email']),
            'full_name' => sanitize($_POST['full_name']),
            'role' => $_POST['role'],
            'is_active' => isset($_POST['is_active']) ? 1 : 0
        ];

        // Update password only if provided
        if (!empty($_POST['password'])) {
            $data['password'] = $_POST['password'];
        }

        if ($this->userModel->update($id, $data)) {
            setFlashMessage('success', 'User updated successfully.');
            redirect('index.php?page=users');
        } else {
            setFlashMessage('error', 'Failed to update user.');
            redirect('index.php?page=users&action=edit&id=' . $id);
        }
    }

    /**
     * Delete user
     */
    public function delete()
    {
        requireRole('admin');

        $id = isset($_GET['id']) ? (int) $_GET['id'] : 0;

        // Prevent self-deletion
        if ($id === getCurrentUserId()) {
            setFlashMessage('error', 'You cannot delete your own account.');
            redirect('index.php?page=users');
        }

        if ($this->userModel->delete($id)) {
            setFlashMessage('success', 'User deleted successfully.');
        } else {
            setFlashMessage('error', 'Failed to delete user.');
        }

        redirect('index.php?page=users');
    }

    /**
     * Validate user data
     */
    private function validateUser($data, $id = null)
    {
        $errors = [];

        if (empty($data['username'])) {
            $errors[] = 'Username is required.';
        } elseif ($this->userModel->usernameExists($data['username'], $id)) {
            $errors[] = 'Username already exists.';
        }

        if (empty($data['email'])) {
            $errors[] = 'Email is required.';
        } elseif (!filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
            $errors[] = 'Invalid email format.';
        } elseif ($this->userModel->emailExists($data['email'], $id)) {
            $errors[] = 'Email already exists.';
        }

        if ($id === null && empty($data['password'])) {
            $errors[] = 'Password is required for new users.';
        }

        if (!empty($data['password']) && strlen($data['password']) < PASSWORD_MIN_LENGTH) {
            $errors[] = 'Password must be at least ' . PASSWORD_MIN_LENGTH . ' characters.';
        }

        if (empty($data['full_name'])) {
            $errors[] = 'Full name is required.';
        }

        if (empty($data['role']) || !in_array($data['role'], ['admin', 'staff', 'viewer'])) {
            $errors[] = 'Invalid role selected.';
        }

        return $errors;
    }
}
