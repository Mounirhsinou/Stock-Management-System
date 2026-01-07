<?php
/**
 * Supplier Controller
 * 
 * Handles supplier management operations
 */

require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../models/Supplier.php';
require_once __DIR__ . '/../includes/helpers.php';

class SupplierController
{
    private $supplierModel;

    public function __construct()
    {
        $this->supplierModel = new Supplier();
    }

    /**
     * List all suppliers
     */
    public function index()
    {
        requireLogin();

        $page = isset($_GET['p']) ? (int) $_GET['p'] : 1;
        $suppliers = $this->supplierModel->getAll($page, ITEMS_PER_PAGE);
        $totalSuppliers = $this->supplierModel->getTotalCount();
        $totalPages = ceil($totalSuppliers / ITEMS_PER_PAGE);

        require_once __DIR__ . '/../views/suppliers/index.php';
    }

    /**
     * Show create supplier form
     */
    public function create()
    {
        requireRole(['admin', 'staff']);

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->store();
            return;
        }

        $supplier = null;
        require_once __DIR__ . '/../views/suppliers/form.php';
    }

    /**
     * Store new supplier
     */
    private function store()
    {
        requireRole(['admin', 'staff']);

        $errors = $this->validateSupplier($_POST);

        if (!empty($errors)) {
            setFlashMessage('error', implode('<br>', $errors));
            redirect('index.php?page=suppliers&action=create');
        }

        $data = [
            'name' => sanitize($_POST['name']),
            'contact_name' => sanitize($_POST['contact_name'] ?? ''),
            'email' => sanitize($_POST['email'] ?? ''),
            'phone' => sanitize($_POST['phone'] ?? ''),
            'address' => sanitize($_POST['address'] ?? ''),
            'is_active' => isset($_POST['is_active']) ? 1 : 0
        ];

        if ($this->supplierModel->create($data)) {
            setFlashMessage('success', 'Supplier created successfully.');
            redirect('index.php?page=suppliers');
        } else {
            setFlashMessage('error', 'Failed to create supplier.');
            redirect('index.php?page=suppliers&action=create');
        }
    }

    /**
     * Show edit supplier form
     */
    public function edit()
    {
        requireRole(['admin', 'staff']);

        $id = isset($_GET['id']) ? (int) $_GET['id'] : 0;
        $supplier = $this->supplierModel->getById($id);

        if (!$supplier) {
            setFlashMessage('error', 'Supplier not found.');
            redirect('index.php?page=suppliers');
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->update($id);
            return;
        }

        require_once __DIR__ . '/../views/suppliers/form.php';
    }

    /**
     * Update supplier
     */
    private function update($id)
    {
        requireRole(['admin', 'staff']);

        $errors = $this->validateSupplier($_POST, $id);

        if (!empty($errors)) {
            setFlashMessage('error', implode('<br>', $errors));
            redirect('index.php?page=suppliers&action=edit&id=' . $id);
        }

        $data = [
            'name' => sanitize($_POST['name']),
            'contact_name' => sanitize($_POST['contact_name'] ?? ''),
            'email' => sanitize($_POST['email'] ?? ''),
            'phone' => sanitize($_POST['phone'] ?? ''),
            'address' => sanitize($_POST['address'] ?? ''),
            'is_active' => isset($_POST['is_active']) ? 1 : 0
        ];

        if ($this->supplierModel->update($id, $data)) {
            setFlashMessage('success', 'Supplier updated successfully.');
            redirect('index.php?page=suppliers');
        } else {
            setFlashMessage('error', 'Failed to update supplier.');
            redirect('index.php?page=suppliers&action=edit&id=' . $id);
        }
    }

    /**
     * Delete supplier
     */
    public function delete()
    {
        requireRole('admin');

        $id = isset($_GET['id']) ? (int) $_GET['id'] : 0;

        if ($this->supplierModel->delete($id)) {
            setFlashMessage('success', 'Supplier deleted successfully.');
        } else {
            setFlashMessage('error', 'Failed to delete supplier. It might be linked to products.');
        }

        redirect('index.php?page=suppliers');
    }

    /**
     * Validate supplier data
     */
    private function validateSupplier($data, $id = null)
    {
        $errors = [];

        if (empty($data['name'])) {
            $errors[] = 'Supplier name is required.';
        }

        if (!empty($data['email']) && !filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
            $errors[] = 'Invalid email format.';
        }

        return $errors;
    }
}
