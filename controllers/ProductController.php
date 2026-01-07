<?php
/**
 * Product Controller
 * 
 * Handles product management operations
 */

require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../models/Product.php';
require_once __DIR__ . '/../models/StockMovement.php';
require_once __DIR__ . '/../models/Supplier.php';
require_once __DIR__ . '/../includes/helpers.php';

class ProductController
{
    private $productModel;
    private $stockMovementModel;
    private $supplierModel;

    public function __construct()
    {
        $this->productModel = new Product();
        $this->stockMovementModel = new StockMovement();
        $this->supplierModel = new Supplier();
    }

    /**
     * List all products
     */
    public function index()
    {
        requireLogin();

        $page = isset($_GET['p']) ? (int) $_GET['p'] : 1;
        $search = isset($_GET['search']) ? sanitize($_GET['search']) : null;

        $products = $this->productModel->getAll($page, ITEMS_PER_PAGE, $search);
        $totalProducts = $this->productModel->getTotalCount($search);
        $totalPages = ceil($totalProducts / ITEMS_PER_PAGE);

        require_once __DIR__ . '/../views/products/index.php';
    }

    /**
     * View single product
     */
    public function view()
    {
        requireLogin();

        $id = isset($_GET['id']) ? (int) $_GET['id'] : 0;

        $product = $this->productModel->getById($id);

        if (!$product) {
            setFlashMessage('error', 'Product not found.');
            redirect('index.php?page=products');
        }

        // Get stock movement history
        $movements = $this->stockMovementModel->getByProduct($id, 50);

        require_once __DIR__ . '/../views/products/view.php';
    }

    /**
     * Show create product form
     */
    public function create()
    {
        requireRole('admin');

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->store();
            return;
        }

        $suppliers = $this->supplierModel->getAllForDropdown();
        $product = null; // For form reuse
        require_once __DIR__ . '/../views/products/form.php';
    }

    /**
     * Store new product
     */
    private function store()
    {
        requireRole('admin');

        // Validate input
        $errors = $this->validateProduct($_POST);

        if (!empty($errors)) {
            setFlashMessage('error', implode('<br>', $errors));
            redirect('index.php?page=products&action=create');
        }

        $data = [
            'sku' => strtoupper(sanitize($_POST['sku'])),
            'name' => sanitize($_POST['name']),
            'description' => sanitize($_POST['description'] ?? ''),
            'purchase_price' => (float) $_POST['purchase_price'],
            'selling_price' => (float) $_POST['selling_price'],
            'quantity' => (int) $_POST['quantity'],
            'minimum_quantity' => (int) $_POST['minimum_quantity'],
            'is_active' => isset($_POST['is_active']) ? 1 : 0,
            'supplier_id' => !empty($_POST['supplier_id']) ? (int) $_POST['supplier_id'] : null,
            'created_by' => getCurrentUserId()
        ];

        $productId = $this->productModel->create($data);

        if ($productId) {
            setFlashMessage('success', 'Product created successfully.');
            redirect('index.php?page=products&action=view&id=' . $productId);
        } else {
            setFlashMessage('error', 'Failed to create product.');
            redirect('index.php?page=products&action=create');
        }
    }

    /**
     * Show edit product form
     */
    public function edit()
    {
        requireRole('admin');

        $id = isset($_GET['id']) ? (int) $_GET['id'] : 0;
        $product = $this->productModel->getById($id);

        if (!$product) {
            setFlashMessage('error', 'Product not found.');
            redirect('index.php?page=products');
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->update($id);
            return;
        }

        $suppliers = $this->supplierModel->getAllForDropdown();
        require_once __DIR__ . '/../views/products/form.php';
    }

    /**
     * Update product
     */
    private function update($id)
    {
        requireRole('admin');

        // Validate input
        $errors = $this->validateProduct($_POST, $id);

        if (!empty($errors)) {
            setFlashMessage('error', implode('<br>', $errors));
            redirect('index.php?page=products&action=edit&id=' . $id);
        }

        $data = [
            'sku' => strtoupper(sanitize($_POST['sku'])),
            'name' => sanitize($_POST['name']),
            'description' => sanitize($_POST['description'] ?? ''),
            'purchase_price' => (float) $_POST['purchase_price'],
            'selling_price' => (float) $_POST['selling_price'],
            'minimum_quantity' => (int) $_POST['minimum_quantity'],
            'is_active' => isset($_POST['is_active']) ? 1 : 0,
            'supplier_id' => !empty($_POST['supplier_id']) ? (int) $_POST['supplier_id'] : null
        ];

        if ($this->productModel->update($id, $data)) {
            setFlashMessage('success', 'Product updated successfully.');
            redirect('index.php?page=products&action=view&id=' . $id);
        } else {
            setFlashMessage('error', 'Failed to update product.');
            redirect('index.php?page=products&action=edit&id=' . $id);
        }
    }

    /**
     * Delete product
     */
    public function delete()
    {
        requireRole('admin');

        $id = isset($_GET['id']) ? (int) $_GET['id'] : 0;

        if ($this->productModel->delete($id)) {
            setFlashMessage('success', 'Product deleted successfully.');
        } else {
            setFlashMessage('error', 'Failed to delete product.');
        }

        redirect('index.php?page=products');
    }

    /**
     * Export products to CSV
     */
    public function export()
    {
        requireLogin();

        $products = $this->productModel->getAll(1, 10000); // Get all products

        // Set headers for CSV download
        header('Content-Type: text/csv');
        header('Content-Disposition: attachment; filename="products_' . date('Y-m-d') . '.csv"');

        $output = fopen('php://output', 'w');

        // CSV headers
        fputcsv($output, ['SKU', 'Name', 'Description', 'Purchase Price', 'Selling Price', 'Quantity', 'Minimum Quantity', 'Status', 'Created At']);

        // CSV data
        foreach ($products as $product) {
            fputcsv($output, [
                $product['sku'],
                $product['name'],
                $product['description'],
                $product['purchase_price'],
                $product['selling_price'],
                $product['quantity'],
                $product['minimum_quantity'],
                $product['is_active'] ? 'Active' : 'Inactive',
                $product['created_at']
            ]);
        }

        fclose($output);
        exit();
    }

    /**
     * Validate product data
     * 
     * @param array $data Product data
     * @param int|null $id Product ID (for updates)
     * @return array Validation errors
     */
    private function validateProduct($data, $id = null)
    {
        $errors = [];

        // SKU validation
        if (empty($data['sku'])) {
            $errors[] = 'SKU is required.';
        } elseif (!isValidSKU($data['sku'])) {
            $errors[] = 'SKU must be alphanumeric with hyphens (3-50 characters).';
        } elseif ($this->productModel->skuExists($data['sku'], $id)) {
            $errors[] = 'SKU already exists.';
        }

        // Name validation
        if (empty($data['name'])) {
            $errors[] = 'Product name is required.';
        }

        // Price validation
        if (!isset($data['purchase_price']) || $data['purchase_price'] < 0) {
            $errors[] = 'Purchase price must be a positive number.';
        }

        if (!isset($data['selling_price']) || $data['selling_price'] < 0) {
            $errors[] = 'Selling price must be a positive number.';
        }

        // Quantity validation (only for new products)
        if ($id === null) {
            if (!isset($data['quantity']) || $data['quantity'] < 0) {
                $errors[] = 'Quantity must be a positive number.';
            }
        }

        // Minimum quantity validation
        if (!isset($data['minimum_quantity']) || $data['minimum_quantity'] < 0) {
            $errors[] = 'Minimum quantity must be a positive number.';
        }

        return $errors;
    }
}
