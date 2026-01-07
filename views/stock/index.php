<?php
$pageTitle = 'Stock Movements';
require_once __DIR__ . '/../../includes/header.php';
?>

<div class="container">
    <div class="page-header">
        <h1>Stock Movements</h1>
        <div class="page-actions">
            <?php if (hasRole(['admin', 'staff'])): ?>
                <a href="index.php?page=stock&action=in" class="btn btn-success">+ Stock IN</a>
                <a href="index.php?page=stock&action=out" class="btn btn-warning">- Stock OUT</a>
            <?php endif; ?>
        </div>
    </div>

    <!-- Filter Form -->
    <div class="card">
        <div class="card-body">
            <form action="index.php" method="GET" class="search-form">
                <input type="hidden" name="page" value="stock">
                <div class="search-group">
                    <select name="product_id" class="form-control">
                        <option value="">All Products</option>
                        <?php foreach ($products as $prod): ?>
                            <option value="<?php echo $prod['id']; ?>" <?php echo (isset($productId) && $productId == $prod['id']) ? 'selected' : ''; ?>>
                                <?php echo htmlspecialchars($prod['sku'] . ' - ' . $prod['name']); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                    <button type="submit" class="btn btn-primary">Filter</button>
                    <?php if (isset($productId)): ?>
                        <a href="index.php?page=stock" class="btn btn-outline">Clear</a>
                    <?php endif; ?>
                </div>
            </form>
        </div>
    </div>

    <!-- Movements Table -->
    <div class="card">
        <div class="card-body">
            <?php if (!empty($movements)): ?>
                <div class="table-responsive">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Date</th>
                                <th>Product</th>
                                <th>Type</th>
                                <th>Quantity</th>
                                <th>By</th>
                                <th>Note</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($movements as $movement): ?>
                                <tr>
                                    <td>#
                                        <?php echo $movement['id']; ?>
                                    </td>
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

                <!-- Pagination -->
                <?php if ($totalPages > 1): ?>
                    <div class="pagination-wrapper">
                        <?php
                        $baseUrl = 'index.php?page=stock';
                        if (isset($productId)) {
                            $baseUrl .= '&product_id=' . $productId;
                        }
                        echo generatePagination($page, $totalPages, $baseUrl);
                        ?>
                    </div>
                <?php endif; ?>

            <?php else: ?>
                <p class="text-muted">No stock movements found.</p>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../../includes/footer.php'; ?>