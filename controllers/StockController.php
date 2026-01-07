<?php
/**
 * Stock Controller
 * 
 * Handles stock movement operations (IN/OUT)
 */

require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../models/Product.php';
require_once __DIR__ . '/../models/StockMovement.php';
require_once __DIR__ . '/../includes/helpers.php';

class StockController
{
    private $productModel;
    private $stockMovementModel;

    public function __construct()
    {
        $this->productModel = new Product();
        $this->stockMovementModel = new StockMovement();
    }

    /**
     * List all stock movements
     */
    public function index()
    {
        requireLogin();

        $page = isset($_GET['p']) ? (int) $_GET['p'] : 1;
        $productId = isset($_GET['product_id']) ? (int) $_GET['product_id'] : null;

        $movements = $this->stockMovementModel->getAll($page, ITEMS_PER_PAGE, $productId);
        $totalMovements = $this->stockMovementModel->getTotalCount($productId);
        $totalPages = ceil($totalMovements / ITEMS_PER_PAGE);

        // Get all products for filter dropdown
        $products = $this->productModel->getAllForDropdown();

        require_once __DIR__ . '/../views/stock/index.php';
    }

    /**
     * Show stock IN form
     */
    public function stockIn()
    {
        requireRole(['admin', 'staff']);

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->processStockIn();
            return;
        }

        $products = $this->productModel->getAllForDropdown();
        $movementType = 'IN';

        require_once __DIR__ . '/../views/stock/form.php';
    }

    /**
     * Process stock IN
     */
    private function processStockIn()
    {
        requireRole(['admin', 'staff']);

        $errors = $this->validateStockMovement($_POST, 'IN');

        if (!empty($errors)) {
            setFlashMessage('error', implode('<br>', $errors));
            redirect('index.php?page=stock&action=in');
        }

        $data = [
            'product_id' => (int) $_POST['product_id'],
            'movement_type' => 'IN',
            'quantity' => (int) $_POST['quantity'],
            'note' => sanitize($_POST['note'] ?? ''),
            'created_by' => getCurrentUserId()
        ];

        $movementId = $this->stockMovementModel->create($data);

        if ($movementId) {
            setFlashMessage('success', 'Stock added successfully.');
            redirect('index.php?page=products&action=view&id=' . $data['product_id']);
        } else {
            setFlashMessage('error', 'Failed to add stock.');
            redirect('index.php?page=stock&action=in');
        }
    }

    /**
     * Show stock OUT form
     */
    public function stockOut()
    {
        requireRole(['admin', 'staff']);

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->processStockOut();
            return;
        }

        $products = $this->productModel->getAllForDropdown();
        $movementType = 'OUT';

        require_once __DIR__ . '/../views/stock/form.php';
    }

    /**
     * Process stock OUT
     */
    private function processStockOut()
    {
        requireRole(['admin', 'staff']);

        $errors = $this->validateStockMovement($_POST, 'OUT');

        if (!empty($errors)) {
            setFlashMessage('error', implode('<br>', $errors));
            redirect('index.php?page=stock&action=out');
        }

        $data = [
            'product_id' => (int) $_POST['product_id'],
            'movement_type' => 'OUT',
            'quantity' => (int) $_POST['quantity'],
            'note' => sanitize($_POST['note'] ?? ''),
            'created_by' => getCurrentUserId()
        ];

        $movementId = $this->stockMovementModel->create($data);

        if ($movementId) {
            setFlashMessage('success', 'Stock removed successfully.');
            redirect('index.php?page=products&action=view&id=' . $data['product_id']);
        } else {
            setFlashMessage('error', 'Failed to remove stock. Please check available quantity.');
            redirect('index.php?page=stock&action=out');
        }
    }

    /**
     * Validate stock movement data
     * 
     * @param array $data Movement data
     * @param string $type Movement type (IN/OUT)
     * @return array Validation errors
     */
    private function validateStockMovement($data, $type)
    {
        $errors = [];

        // Product validation
        if (empty($data['product_id'])) {
            $errors[] = 'Please select a product.';
        } else {
            $product = $this->productModel->getById($data['product_id']);

            if (!$product) {
                $errors[] = 'Invalid product selected.';
            } elseif ($type === 'OUT') {
                // Check if sufficient stock for OUT movement
                $quantity = (int) $data['quantity'];

                if ($quantity > $product['quantity']) {
                    $errors[] = "Insufficient stock. Available: {$product['quantity']}, Requested: $quantity";
                }
            }
        }

        // Quantity validation
        if (empty($data['quantity']) || $data['quantity'] <= 0) {
            $errors[] = 'Quantity must be greater than zero.';
        }

        return $errors;
    }
}
