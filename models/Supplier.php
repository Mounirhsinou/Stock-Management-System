<?php
/**
 * Supplier Model
 * 
 * Handles all supplier-related database operations
 */

require_once __DIR__ . '/../config/Database.php';

class Supplier
{
    private $db;

    public function __construct()
    {
        $this->db = Database::getInstance()->getConnection();
    }

    /**
     * Get all suppliers
     * 
     * @param int $page Page number
     * @param int $perPage Items per page
     * @return array Suppliers data
     */
    public function getAll($page = 1, $perPage = ITEMS_PER_PAGE)
    {
        $offset = ($page - 1) * $perPage;

        $sql = "SELECT * FROM suppliers ORDER BY name ASC LIMIT :limit OFFSET :offset";
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':limit', $perPage, PDO::PARAM_INT);
        $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll();
    }

    /**
     * Get total supplier count
     * 
     * @return int Total suppliers
     */
    public function getTotalCount()
    {
        $sql = "SELECT COUNT(*) as count FROM suppliers";
        $stmt = $this->db->query($sql);
        $result = $stmt->fetch();

        return $result['count'];
    }

    /**
     * Get supplier by ID
     * 
     * @param int $id Supplier ID
     * @return array|false Supplier data or false
     */
    public function getById($id)
    {
        $sql = "SELECT * FROM suppliers WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['id' => $id]);

        return $stmt->fetch();
    }

    /**
     * Create new supplier
     * 
     * @param array $data Supplier data
     * @return int|false Supplier ID or false
     */
    public function create($data)
    {
        $sql = "INSERT INTO suppliers (name, contact_name, email, phone, address, is_active) 
                VALUES (:name, :contact_name, :email, :phone, :address, :is_active)";

        $stmt = $this->db->prepare($sql);
        $params = [
            'name' => $data['name'],
            'contact_name' => $data['contact_name'] ?? null,
            'email' => $data['email'] ?? null,
            'phone' => $data['phone'] ?? null,
            'address' => $data['address'] ?? null,
            'is_active' => $data['is_active'] ?? 1
        ];

        if ($stmt->execute($params)) {
            return $this->db->lastInsertId();
        }

        return false;
    }

    /**
     * Update supplier
     * 
     * @param int $id Supplier ID
     * @param array $data Supplier data
     * @return bool Success status
     */
    public function update($id, $data)
    {
        $fields = [];
        $params = ['id' => $id];

        $allowedFields = ['name', 'contact_name', 'email', 'phone', 'address', 'is_active'];

        foreach ($allowedFields as $field) {
            if (isset($data[$field])) {
                $fields[] = "$field = :$field";
                $params[$field] = $data[$field];
            }
        }

        if (empty($fields)) {
            return false;
        }

        $sql = "UPDATE suppliers SET " . implode(', ', $fields) . " WHERE id = :id";
        $stmt = $this->db->prepare($sql);

        return $stmt->execute($params);
    }

    /**
     * Delete supplier
     * 
     * @param int $id Supplier ID
     * @return bool Success status
     */
    public function delete($id)
    {
        $sql = "DELETE FROM suppliers WHERE id = :id";
        $stmt = $this->db->prepare($sql);

        return $stmt->execute(['id' => $id]);
    }

    /**
     * Get all active suppliers for dropdown
     * 
     * @return array Suppliers
     */
    public function getAllForDropdown()
    {
        $sql = "SELECT id, name FROM suppliers WHERE is_active = 1 ORDER BY name ASC";
        $stmt = $this->db->query($sql);

        return $stmt->fetchAll();
    }
}
