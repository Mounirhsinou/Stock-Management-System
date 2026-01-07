<?php
/**
 * User Model
 * 
 * Handles all user-related database operations
 */

require_once __DIR__ . '/../config/Database.php';

class User
{
    private $db;

    public function __construct()
    {
        $this->db = Database::getInstance()->getConnection();
    }

    /**
     * Authenticate user
     * 
     * @param string $username Username or email
     * @param string $password Password
     * @return array|false User data or false
     */
    public function authenticate($username, $password)
    {
        $sql = "SELECT * FROM users WHERE (username = :user1 OR email = :user2) AND is_active = 1";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            'user1' => $username,
            'user2' => $username
        ]);

        $user = $stmt->fetch();

        if ($user && password_verify($password, $user['password'])) {
            // Remove password from returned data
            unset($user['password']);
            return $user;
        }

        return false;
    }

    /**
     * Get user by ID
     * 
     * @param int $id User ID
     * @return array|false User data or false
     */
    public function getById($id)
    {
        $sql = "SELECT id, username, email, full_name, role, is_active, created_at FROM users WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['id' => $id]);

        return $stmt->fetch();
    }

    /**
     * Get all users
     * 
     * @param int $page Page number for pagination
     * @param int $perPage Items per page
     * @return array Users data
     */
    public function getAll($page = 1, $perPage = ITEMS_PER_PAGE)
    {
        $offset = ($page - 1) * $perPage;

        $sql = "SELECT id, username, email, full_name, role, is_active, created_at 
                FROM users 
                ORDER BY created_at DESC 
                LIMIT :limit OFFSET :offset";

        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':limit', $perPage, PDO::PARAM_INT);
        $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll();
    }

    /**
     * Get total user count
     * 
     * @return int Total users
     */
    public function getTotalCount()
    {
        $sql = "SELECT COUNT(*) as count FROM users";
        $stmt = $this->db->query($sql);
        $result = $stmt->fetch();

        return $result['count'];
    }

    /**
     * Create new user
     * 
     * @param array $data User data
     * @return int|false User ID or false
     */
    public function create($data)
    {
        $sql = "INSERT INTO users (username, email, password, full_name, role, is_active) 
                VALUES (:username, :email, :password, :full_name, :role, :is_active)";

        $stmt = $this->db->prepare($sql);

        $hashedPassword = password_hash($data['password'], PASSWORD_DEFAULT);

        $params = [
            'username' => $data['username'],
            'email' => $data['email'],
            'password' => $hashedPassword,
            'full_name' => $data['full_name'],
            'role' => $data['role'],
            'is_active' => $data['is_active'] ?? 1
        ];

        if ($stmt->execute($params)) {
            return $this->db->lastInsertId();
        }

        return false;
    }

    /**
     * Update user
     * 
     * @param int $id User ID
     * @param array $data User data
     * @return bool Success status
     */
    public function update($id, $data)
    {
        // Build dynamic SQL based on provided fields
        $fields = [];
        $params = ['id' => $id];

        if (isset($data['username'])) {
            $fields[] = "username = :username";
            $params['username'] = $data['username'];
        }

        if (isset($data['email'])) {
            $fields[] = "email = :email";
            $params['email'] = $data['email'];
        }

        if (isset($data['password'])) {
            $fields[] = "password = :password";
            $params['password'] = password_hash($data['password'], PASSWORD_DEFAULT);
        }

        if (isset($data['full_name'])) {
            $fields[] = "full_name = :full_name";
            $params['full_name'] = $data['full_name'];
        }

        if (isset($data['role'])) {
            $fields[] = "role = :role";
            $params['role'] = $data['role'];
        }

        if (isset($data['is_active'])) {
            $fields[] = "is_active = :is_active";
            $params['is_active'] = $data['is_active'];
        }

        if (empty($fields)) {
            return false;
        }

        $sql = "UPDATE users SET " . implode(', ', $fields) . " WHERE id = :id";
        $stmt = $this->db->prepare($sql);

        return $stmt->execute($params);
    }

    /**
     * Delete user
     * 
     * @param int $id User ID
     * @return bool Success status
     */
    public function delete($id)
    {
        $sql = "DELETE FROM users WHERE id = :id";
        $stmt = $this->db->prepare($sql);

        return $stmt->execute(['id' => $id]);
    }

    /**
     * Check if username exists
     * 
     * @param string $username Username
     * @param int|null $excludeId Exclude user ID (for updates)
     * @return bool True if exists
     */
    public function usernameExists($username, $excludeId = null)
    {
        $sql = "SELECT COUNT(*) as count FROM users WHERE username = :username";

        if ($excludeId) {
            $sql .= " AND id != :id";
        }

        $stmt = $this->db->prepare($sql);
        $params = ['username' => $username];

        if ($excludeId) {
            $params['id'] = $excludeId;
        }

        $stmt->execute($params);
        $result = $stmt->fetch();

        return $result['count'] > 0;
    }

    /**
     * Check if email exists
     * 
     * @param string $email Email
     * @param int|null $excludeId Exclude user ID (for updates)
     * @return bool True if exists
     */
    public function emailExists($email, $excludeId = null)
    {
        $sql = "SELECT COUNT(*) as count FROM users WHERE email = :email";

        if ($excludeId) {
            $sql .= " AND id != :id";
        }

        $stmt = $this->db->prepare($sql);
        $params = ['email' => $email];

        if ($excludeId) {
            $params['id'] = $excludeId;
        }

        $stmt->execute($params);
        $result = $stmt->fetch();

        return $result['count'] > 0;
    }
}
