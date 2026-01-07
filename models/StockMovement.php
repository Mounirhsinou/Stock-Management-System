<?php
/**
 * Stock Movement Model
 * 
 * Handles all stock movement database operations
 */

require_once __DIR__ . '/../config/Database.php';

class StockMovement
{
    private $db;

    public function __construct()
    {
        $this->db = Database::getInstance()->getConnection();
    }

    /**
     * Create stock movement (IN or OUT)
     * Updates product quantity atomically using transaction
     * 
     * @param array $data Movement data
     * @return int|false Movement ID or false
     */
    public function create($data)
    {
        try {
            // Start transaction
            $this->db->beginTransaction();

            // Get current product quantity
            $sql = "SELECT quantity FROM products WHERE id = :product_id FOR UPDATE";
            $stmt = $this->db->prepare($sql);
            $stmt->execute(['product_id' => $data['product_id']]);
            $product = $stmt->fetch();

            if (!$product) {
                throw new Exception("Product not found");
            }

            $currentQuantity = $product['quantity'];
            $newQuantity = $currentQuantity;

            // Calculate new quantity based on movement type
            if ($data['movement_type'] === 'IN') {
                $newQuantity += $data['quantity'];
            } else if ($data['movement_type'] === 'OUT') {
                $newQuantity -= $data['quantity'];

                // Prevent negative stock
                if ($newQuantity < 0) {
                    throw new Exception("Insufficient stock. Available: $currentQuantity, Requested: " . $data['quantity']);
                }
            }

            // Insert stock movement record
            $sql = "INSERT INTO stock_movements (product_id, movement_type, quantity, note, created_by) 
                    VALUES (:product_id, :movement_type, :quantity, :note, :created_by)";

            $stmt = $this->db->prepare($sql);
            $params = [
                'product_id' => $data['product_id'],
                'movement_type' => $data['movement_type'],
                'quantity' => $data['quantity'],
                'note' => $data['note'] ?? null,
                'created_by' => $data['created_by']
            ];

            $stmt->execute($params);
            $movementId = $this->db->lastInsertId();

            // Update product quantity
            $sql = "UPDATE products SET quantity = :quantity WHERE id = :product_id";
            $stmt = $this->db->prepare($sql);
            $stmt->execute([
                'quantity' => $newQuantity,
                'product_id' => $data['product_id']
            ]);

            // Commit transaction
            $this->db->commit();

            return $movementId;

        } catch (Exception $e) {
            // Rollback on error
            $this->db->rollBack();
            error_log("Stock Movement Error: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Get all stock movements
     * 
     * @param int $page Page number
     * @param int $perPage Items per page
     * @param int|null $productId Filter by product ID
     * @return array Movements data
     */
    public function getAll($page = 1, $perPage = ITEMS_PER_PAGE, $productId = null)
    {
        $offset = ($page - 1) * $perPage;

        $sql = "SELECT sm.*, p.name as product_name, p.sku as product_sku, u.full_name as created_by_name 
                FROM stock_movements sm 
                LEFT JOIN products p ON sm.product_id = p.id 
                LEFT JOIN users u ON sm.created_by = u.id 
                WHERE 1=1";

        $params = [];

        if ($productId) {
            $sql .= " AND sm.product_id = :product_id";
            $params['product_id'] = $productId;
        }

        $sql .= " ORDER BY sm.created_at DESC LIMIT :limit OFFSET :offset";

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
     * Get total movement count
     * 
     * @param int|null $productId Filter by product ID
     * @return int Total movements
     */
    public function getTotalCount($productId = null)
    {
        $sql = "SELECT COUNT(*) as count FROM stock_movements WHERE 1=1";

        $params = [];

        if ($productId) {
            $sql .= " AND product_id = :product_id";
            $params['product_id'] = $productId;
        }

        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        $result = $stmt->fetch();

        return $result['count'];
    }

    /**
     * Get movements by product
     * 
     * @param int $productId Product ID
     * @param int $limit Result limit
     * @return array Movements
     */
    public function getByProduct($productId, $limit = 50)
    {
        $sql = "SELECT sm.*, u.full_name as created_by_name 
                FROM stock_movements sm 
                LEFT JOIN users u ON sm.created_by = u.id 
                WHERE sm.product_id = :product_id 
                ORDER BY sm.created_at DESC 
                LIMIT :limit";

        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':product_id', $productId, PDO::PARAM_INT);
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll();
    }

    /**
     * Get recent movements for dashboard
     * 
     * @param int $limit Result limit
     * @return array Recent movements
     */
    public function getRecent($limit = 10)
    {
        $sql = "SELECT sm.*, p.name as product_name, p.sku as product_sku, u.full_name as created_by_name 
                FROM stock_movements sm 
                LEFT JOIN products p ON sm.product_id = p.id 
                LEFT JOIN users u ON sm.created_by = u.id 
                ORDER BY sm.created_at DESC 
                LIMIT :limit";

        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll();
    }

    /**
     * Get movement by ID
     * 
     * @param int $id Movement ID
     * @return array|false Movement data or false
     */
    public function getById($id)
    {
        $sql = "SELECT sm.*, p.name as product_name, p.sku as product_sku, u.full_name as created_by_name 
                FROM stock_movements sm 
                LEFT JOIN products p ON sm.product_id = p.id 
                LEFT JOIN users u ON sm.created_by = u.id 
                WHERE sm.id = :id";

        $stmt = $this->db->prepare($sql);
        $stmt->execute(['id' => $id]);

        return $stmt->fetch();
    }

    /**
     * Get movement statistics
     * 
     * @return array Statistics
     */
    public function getStatistics()
    {
        $sql = "SELECT 
                    COUNT(*) as total_movements,
                    SUM(CASE WHEN movement_type = 'IN' THEN quantity ELSE 0 END) as total_in,
                    SUM(CASE WHEN movement_type = 'OUT' THEN quantity ELSE 0 END) as total_out,
                    COUNT(DISTINCT product_id) as products_affected
                FROM stock_movements";

        $stmt = $this->db->query($sql);

        return $stmt->fetch();
    }
}
