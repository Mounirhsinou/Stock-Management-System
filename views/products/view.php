<?php
$pageTitle = $product['name'];
require_once __DIR__ . '/../../includes/header.php';
?>

<div class="container">
    <div class="page-header">
        <h1>
            <?php echo htmlspecialchars($product['name']); ?>
        </h1>
        <div class="page-actions">
            <a href="index.php?page=products" class="btn btn-outline">← Back</a>
            <?php if (hasRole(['admin', 'staff'])): ?>
                <a href="index.php?page=stock&action=in" class="btn btn-success">+ Stock IN</a>
                <a href="index.php?page=stock&action=out" class="btn btn-warning">- Stock OUT</a>
            <?php endif; ?>
            <?php if (hasRole('admin')): ?>
                <a href="index.php?page=products&action=edit&id=<?php echo $product['id']; ?>"
                    class="btn btn-primary">Edit</a>
            <?php endif; ?>
        </div>
    </div>

    <!-- Product Details -->
    <div class="card">
        <div class="card-header">
            <h2>Product Information</h2>
            <span class="badge <?php echo $product['is_active'] ? 'badge-success' : 'badge-secondary'; ?>">
                <?php echo $product['is_active'] ? 'Active' : 'Inactive'; ?>
            </span>
        </div>
        <div class="card-body">
            <div class="detail-grid">
                <div class="detail-item">
                    <label>SKU</label>
                    <p><code><?php echo htmlspecialchars($product['sku']); ?></code></p>
                </div>

                <div class="detail-item">
                    <label>Current Stock</label>
                    <p>
                        <span
                            class="badge badge-lg <?php echo isLowStock($product['quantity'], $product['minimum_quantity']) ? 'badge-danger' : 'badge-success'; ?>">
                            <?php echo number_format($product['quantity']); ?> units
                        </span>
                        <?php if (isLowStock($product['quantity'], $product['minimum_quantity'])): ?>
                            <span class="text-danger">⚠️ Low Stock Alert</span>
                        <?php endif; ?>
                    </p>
                </div>

                <div class="detail-item">
                    <label>Purchase Price</label>
                    <p>
                        <?php echo formatCurrency($product['purchase_price']); ?>
                    </p>
                </div>

                <div class="detail-item">
                    <label>Selling Price</label>
                    <p>
                        <?php echo formatCurrency($product['selling_price']); ?>
                    </p>
                </div>

                <div class="detail-item">
                    <label>Profit Margin</label>
                    <p>
                        <?php
                        $margin = $product['selling_price'] - $product['purchase_price'];
                        $marginPercent = $product['purchase_price'] > 0 ? ($margin / $product['purchase_price']) * 100 : 0;
                        echo formatCurrency($margin) . ' (' . number_format($marginPercent, 2) . '%)';
                        ?>
                    </p>
                </div>

                <div class="detail-item">
                    <label>Minimum Quantity</label>
                    <p>
                        <?php echo number_format($product['minimum_quantity']); ?> units
                    </p>
                </div>

                <div class="detail-item">
                    <label>Created By</label>
                    <p>
                        <?php echo htmlspecialchars($product['created_by_name'] ?? 'N/A'); ?>
                    </p>
                </div>

                <div class="detail-item">
                    <label>Created At</label>
                    <p>
                        <?php echo formatDate($product['created_at'], 'M d, Y H:i'); ?>
                    </p>
                </div>
            </div>

            <?php if ($product['description']): ?>
                <div class="detail-item">
                    <label>Description</label>
                    <p>
                        <?php echo nl2br(htmlspecialchars($product['description'])); ?>
                    </p>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- Stock Movement History -->
    <div class="card">
        <div class="card-header">
            <h2>Stock Movement History</h2>
        </div>
        <div class="card-body">
            <?php if (!empty($movements)): ?>
                <div class="table-responsive">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Date</th>
                                <th>Type</th>
                                <th>Quantity</th>
                                <th>By</th>
                                <th>Note</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($movements as $movement): ?>
                                <tr>
                                    <td>
                                        <?php echo formatDate($movement['created_at'], 'M d, Y H:i'); ?>
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
                <p class="text-muted">No stock movements recorded yet.</p>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../../includes/footer.php'; ?>