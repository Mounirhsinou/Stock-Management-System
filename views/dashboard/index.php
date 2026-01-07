<?php
$pageTitle = 'Dashboard';
require_once __DIR__ . '/../../includes/header.php';
?>

<div class="container">
    <div class="page-header">
        <h1>Dashboard</h1>
        <div class="page-actions">
            <?php if (hasRole(['admin', 'staff'])): ?>
                <a href="index.php?page=stock&action=in" class="btn btn-success">+ Stock IN</a>
                <a href="index.php?page=stock&action=out" class="btn btn-warning">- Stock OUT</a>
            <?php endif; ?>
            <?php if (hasRole('admin')): ?>
                <a href="index.php?page=products&action=create" class="btn btn-primary">+ Add Product</a>
            <?php endif; ?>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="stats-grid">
        <div class="stat-card">
            <div class="stat-icon stat-icon-primary">
                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M20 7h-9M14 17H5M20 17h-3M5 7h2"></path>
                    <circle cx="10" cy="7" r="2"></circle>
                    <circle cx="17" cy="17" r="2"></circle>
                </svg>
            </div>
            <div class="stat-content">
                <h3>
                    <?php echo number_format($totalProducts); ?>
                </h3>
                <p>Total Products</p>
            </div>
        </div>

        <div class="stat-card <?php echo $lowStockCount > 0 ? 'stat-card-warning' : ''; ?>">
            <div class="stat-icon stat-icon-warning">
                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z">
                    </path>
                    <line x1="12" y1="9" x2="12" y2="13"></line>
                    <line x1="12" y1="17" x2="12.01" y2="17"></line>
                </svg>
            </div>
            <div class="stat-content">
                <h3>
                    <?php echo number_format($lowStockCount); ?>
                </h3>
                <p>Low Stock Alerts</p>
            </div>
        </div>

        <div class="stat-card">
            <div class="stat-icon stat-icon-success">
                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <polyline points="22 12 18 12 15 21 9 3 6 12 2 12"></polyline>
                </svg>
            </div>
            <div class="stat-content">
                <h3>
                    <?php echo number_format($movementStats['total_movements'] ?? 0); ?>
                </h3>
                <p>Total Movements</p>
            </div>
        </div>

        <div class="stat-card">
            <div class="stat-icon stat-icon-info">
                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path
                        d="M21 16V8a2 2 0 00-1-1.73l-7-4a2 2 0 00-2 0l-7 4A2 2 0 003 8v8a2 2 0 001 1.73l7 4a2 2 0 002 0l7-4A2 2 0 0021 16z">
                    </path>
                    <polyline points="3.27 6.96 12 12.01 20.73 6.96"></polyline>
                    <line x1="12" y1="22.08" x2="12" y2="12"></line>
                </svg>
            </div>
            <div class="stat-content">
                <h3>
                    <?php echo number_format($movementStats['products_affected'] ?? 0); ?>
                </h3>
                <p>Products with Activity</p>
            </div>
        </div>
    </div>

    <!-- Low Stock Alerts -->
    <?php if (!empty($lowStockProducts)): ?>
        <div class="card card-warning">
            <div class="card-header">
                <h2>⚠️ Low Stock Alerts</h2>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>SKU</th>
                                <th>Product Name</th>
                                <th>Current Stock</th>
                                <th>Minimum Stock</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($lowStockProducts as $product): ?>
                                <tr>
                                    <td><code><?php echo htmlspecialchars($product['sku']); ?></code></td>
                                    <td>
                                        <?php echo htmlspecialchars($product['name']); ?>
                                    </td>
                                    <td><span class="badge badge-danger">
                                            <?php echo $product['quantity']; ?>
                                        </span></td>
                                    <td>
                                        <?php echo $product['minimum_quantity']; ?>
                                    </td>
                                    <td>
                                        <a href="index.php?page=products&action=view&id=<?php echo $product['id']; ?>"
                                            class="btn btn-sm btn-primary">View</a>
                                        <?php if (hasRole(['admin', 'staff'])): ?>
                                            <a href="index.php?page=stock&action=in" class="btn btn-sm btn-success">Add Stock</a>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    <?php endif; ?>

    <!-- Recent Stock Movements -->
    <div class="card">
        <div class="card-header">
            <h2>Recent Stock Movements</h2>
            <a href="index.php?page=stock" class="btn btn-sm btn-outline">View All</a>
        </div>
        <div class="card-body">
            <?php if (!empty($recentMovements)): ?>
                <div class="table-responsive">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Date</th>
                                <th>Product</th>
                                <th>Type</th>
                                <th>Quantity</th>
                                <th>By</th>
                                <th>Note</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($recentMovements as $movement): ?>
                                <tr>
                                    <td>
                                        <?php echo formatDate($movement['created_at'], 'M d, Y H:i'); ?>
                                    </td>
                                    <td>
                                        <a href="index.php?page=products&action=view&id=<?php echo $movement['product_id']; ?>">
                                            <?php echo htmlspecialchars($movement['product_name']); ?>
                                        </a>
                                        <br><small class="text-muted">
                                            <?php echo htmlspecialchars($movement['product_sku']); ?>
                                        </small>
                                    </td>
                                    <td>
                                        <span
                                            class="badge badge-<?php echo $movement['movement_type'] === 'IN' ? 'success' : 'warning'; ?>">
                                            <?php echo $movement['movement_type']; ?>
                                        </span>
                                    </td>
                                    <td>
                                        <?php echo number_format($movement['quantity']); ?>
                                    </td>
                                    <td>
                                        <?php echo htmlspecialchars($movement['created_by_name'] ?? 'N/A'); ?>
                                    </td>
                                    <td>
                                        <?php echo htmlspecialchars($movement['note'] ?? '-'); ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php else: ?>
                <p class="text-muted">No stock movements yet.</p>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../../includes/footer.php'; ?>