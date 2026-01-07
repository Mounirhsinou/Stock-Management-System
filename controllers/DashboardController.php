<?php
/**
 * Dashboard Controller
 * 
 * Handles dashboard display and statistics
 */

require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../models/Product.php';
require_once __DIR__ . '/../models/StockMovement.php';
require_once __DIR__ . '/../includes/helpers.php';

class DashboardController
{
    private $productModel;
    private $stockMovementModel;

    public function __construct()
    {
        $this->productModel = new Product();
        $this->stockMovementModel = new StockMovement();
    }

    /**
     * Display dashboard
     */
    public function index()
    {
        requireLogin();

        // Get statistics
        $totalProducts = $this->productModel->getTotalCount();
        $lowStockCount = $this->productModel->getLowStockCount();
        $lowStockProducts = $this->productModel->getLowStock();
        $recentMovements = $this->stockMovementModel->getRecent(10);
        $movementStats = $this->stockMovementModel->getStatistics();

        // Load view
        require_once __DIR__ . '/../views/dashboard/index.php';
    }
}
