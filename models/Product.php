<?php
/**
 * Product Model
 * 
 * Handles all product-related database operations
 */

require_once __DIR__ . '/../config/Database.php';

class Product
{
    private $db;

    public function __construct()
    {
        $this->db = Database::getInstance()->getConnection();
    }

    /**
     * Get all products
     * 
     * @param int $page Page number
     * @param int $perPage Items per page
     * @param string|null $search Search term
     * @return array Products data
     */
    public function getAll($page = 1, $perPage = ITEMS_PER_PAGE, $search = null)
    {
        $offset = ($page - 1) * $perPage;

        $sql = "SELECT p.*, u.full_name as created_by_name, s.name as supplier_name 
                FROM products p 
                LEFT JOIN users u ON p.created_by = u.id 
                LEFT JOIN suppliers s ON p.supplier_id = s.id 
                WHERE 1=1";

        $params = [];

        if ($search) {
            $sql .= " AND (p.name LIKE :search1 OR p.sku LIKE :search2 OR p.description LIKE :search3)";
            $params['search1'] = "%$search%";
            $params['search2'] = "%$search%";
            $params['search3'] = "%$search%";
        }

        $sql .= " ORDER BY p.created_at DESC LIMIT :limit OFFSET :offset";

        $stmt = $this->db->prepare($sql);

        foreach ($params as $key => $value) {
            $stmt->bindValue(":$key", $value);
        }

        $stmt->bindValue(':limit', $perPage, PDO::PARAM_INT);
        $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll();
    }

    /**
     * Get total product count
     * 
     * @param string|null $search Search term
     * @return int Total products
     */
    public function getTotalCount($search = null)
    {
        $sql = "SELECT COUNT(*) as count FROM products WHERE 1=1";

        $params = [];

        if ($search) {
            $sql .= " AND (name LIKE :search1 OR sku LIKE :search2 OR description LIKE :search3)";
            $params['search1'] = "%$search%";
            $params['search2'] = "%$search%";
            $params['search3'] = "%$search%";
        }

        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        $result = $stmt->fetch();

        return $result['count'];
    }

    /**
     * Get product by ID
     * 
     * @param int $id Product ID
     * @return array|false Product data or false
     */
    public function getById($id)
    {
        $sql = "SELECT p.*, u.full_name as created_by_name, s.name as supplier_name 
                FROM products p 
                LEFT JOIN users u ON p.created_by = u.id 
                LEFT JOIN suppliers s ON p.supplier_id = s.id 
                WHERE p.id = :id";

        $stmt = $this->db->prepare($sql);
        $stmt->execute(['id' => $id]);

        return $stmt->fetch();
    }

    /**
     * Get product by SKU
     * 
     * @param string $sku Product SKU
     * @return array|false Product data or false
     */
    public function getBySKU($sku)
    {
        $sql = "SELECT * FROM products WHERE sku = :sku";
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['sku' => $sku]);

        return $stmt->fetch();
    }

    /**
     * Create new product
     * 
     * @param array $data Product data
     * @return int|false Product ID or false
     */
    public function create($data)
    {
        $sql = "INSERT INTO products (sku, name, description, purchase_price, selling_price, quantity, minimum_quantity, is_active, created_by, supplier_id) 
                VALUES (:sku, :name, :description, :purchase_price, :selling_price, :quantity, :minimum_quantity, :is_active, :created_by, :supplier_id)";

        $stmt = $this->db->prepare($sql);

        $params = [
            'sku' => $data['sku'],
            'name' => $data['name'],
            'description' => $data['description'] ?? null,
            'purchase_price' => $data['purchase_price'],
            'selling_price' => $data['selling_price'],
            'quantity' => $data['quantity'] ?? 0,
            'minimum_quantity' => $data['minimum_quantity'] ?? 10,
            'is_active' => $data['is_active'] ?? 1,
            'created_by' => $data['created_by'],
            'supplier_id' => $data['supplier_id'] ?? null
        ];

        if ($stmt->execute($params)) {
            return $this->db->lastInsertId();
        }

        return false;
    }

    /**
     * Update product
     * 
     * @param int $id Product ID
     * @param array $data Product data
     * @return bool Success status
     */
    public function update($id, $data)
    {
        $fields = [];
        $params = ['id' => $id];

        $allowedFields = ['sku', 'name', 'description', 'purchase_price', 'selling_price', 'quantity', 'minimum_quantity', 'is_active', 'supplier_id'];

        foreach ($allowedFields as $field) {
            if (isset($data[$field])) {
                $fields[] = "$field = :$field";
                $params[$field] = $data[$field];
            }
        }

        if (empty($fields)) {
            return false;
        }

        $sql = "UPDATE products SET " . implode(', ', $fields) . " WHERE id = :id";
        $stmt = $this->db->prepare($sql);

        return $stmt->execute($params);
    }

    /**
     * Delete product
     * 
     * @param int $id Product ID
     * @return bool Success status
     */
    public function delete($id)
    {
        $sql = "DELETE FROM products WHERE id = :id";
        $stmt = $this->db->prepare($sql);

        return $stmt->execute(['id' => $id]);
    }

    /**
     * Update product quantity
     * 
     * @param int $id Product ID
     * @param int $quantity New quantity
     * @return bool Success status
     */
    public function updateQuantity($id, $quantity)
    {
        $sql = "UPDATE products SET quantity = :quantity WHERE id = :id";
        $stmt = $this->db->prepare($sql);

        return $stmt->execute([
            'id' => $id,
            'quantity' => $quantity
        ]);
    }

    /**
     * Get low stock products
     * 
     * @return array Low stock products
     */
    public function getLowStock()
    {
        $sql = "SELECT p.*, u.full_name as created_by_name 
                FROM products p 
                LEFT JOIN users u ON p.created_by = u.id 
                WHERE p.quantity <= p.minimum_quantity AND p.is_active = 1 
                ORDER BY p.quantity ASC";

        $stmt = $this->db->query($sql);

        return $stmt->fetchAll();
    }

    /**
     * Get low stock count
     * 
     * @return int Low stock count
     */
    public function getLowStockCount()
    {
        $sql = "SELECT COUNT(*) as count FROM products WHERE quantity <= minimum_quantity AND is_active = 1";
        $stmt = $this->db->query($sql);
        $result = $stmt->fetch();

        return $result['count'];
    }

    /**
     * Search products
     * 
     * @param string $term Search term
     * @param int $limit Result limit
     * @return array Products
     */
    public function search($term, $limit = 10)
    {
        $sql = "SELECT * FROM products 
                WHERE (name LIKE :term1 OR sku LIKE :term2) AND is_active = 1 
                ORDER BY name ASC 
                LIMIT :limit";

        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':term1', "%$term%");
        $stmt->bindValue(':term2', "%$term%");
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll();
    }

    /**
     * Check if SKU exists
     * 
     * @param string $sku SKU
     * @param int|null $excludeId Exclude product ID (for updates)
     * @return bool True if exists
     */
    public function skuExists($sku, $excludeId = null)
    {
        $sql = "SELECT COUNT(*) as count FROM products WHERE sku = :sku";

        if ($excludeId) {
            $sql .= " AND id != :id";
        }

        $stmt = $this->db->prepare($sql);
        $params = ['sku' => $sku];

        if ($excludeId) {
            $params['id'] = $excludeId;
        }

        $stmt->execute($params);
        $result = $stmt->fetch();

        return $result['count'] > 0;
    }

    /**
     * Get all products for dropdown (active only)
     * 
     * @return array Products
     */
    public function getAllForDropdown()
    {
        $sql = "SELECT id, sku, name, quantity FROM products WHERE is_active = 1 ORDER BY name ASC";
        $stmt = $this->db->query($sql);

        return $stmt->fetchAll();
    }
}
