<?php
$pageTitle = 'Products';
require_once __DIR__ . '/../../includes/header.php';
?>

<div class="container">
    <div class="page-header">
        <h1>Products</h1>
        <div class="page-actions">
            <a href="index.php?page=products&action=export" class="btn btn-outline">Export CSV</a>
            <?php if (hasRole('admin')): ?>
                <a href="index.php?page=products&action=create" class="btn btn-primary">+ Add Product</a>
            <?php endif; ?>
        </div>
    </div>

    <!-- Search Form -->
    <div class="card">
        <div class="card-body">
            <form action="index.php" method="GET" class="search-form">
                <input type="hidden" name="page" value="products">
                <div class="search-group">
                    <input type="text" name="search" class="form-control"
                        placeholder="Search by name, SKU, or description..."
                        value="<?php echo htmlspecialchars($search ?? ''); ?>">
                    <button type="submit" class="btn btn-primary">Search</button>
                    <?php if ($search): ?>
                        <a href="index.php?page=products" class="btn btn-outline">Clear</a>
                    <?php endif; ?>
                </div>
            </form>
        </div>
    </div>

    <!-- Products Table -->
    <div class="card">
        <div class="card-body">
            <?php if (!empty($products)): ?>
                <div class="table-responsive">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>SKU</th>
                                <th>Name</th>
                                <th>Purchase Price</th>
                                <th>Selling Price</th>
                                <th>Stock</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($products as $product): ?>
                                <tr
                                    class="<?php echo isLowStock($product['quantity'], $product['minimum_quantity']) ? 'row-warning' : ''; ?>">
                                    <td><code><?php echo htmlspecialchars($product['sku']); ?></code></td>
                                    <td>
                                        <strong>
                                            <?php echo htmlspecialchars($product['name']); ?>
                                        </strong>
                                        <?php if (isLowStock($product['quantity'], $product['minimum_quantity'])): ?>
                                            <span class="badge badge-danger">Low Stock</span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <?php echo formatCurrency($product['purchase_price']); ?>
                                    </td>
                                    <td>
                                        <?php echo formatCurrency($product['selling_price']); ?>
                                    </td>
                                    <td>
                                        <span
                                            class="badge <?php echo isLowStock($product['quantity'], $product['minimum_quantity']) ? 'badge-danger' : 'badge-success'; ?>">
                                            <?php echo number_format($product['quantity']); ?>
                                        </span>
                                    </td>
                                    <td>
                                        <span
                                            class="badge <?php echo $product['is_active'] ? 'badge-success' : 'badge-secondary'; ?>">
                                            <?php echo $product['is_active'] ? 'Active' : 'Inactive'; ?>
                                        </span>
                                    </td>
                                    <td>
                                        <a href="index.php?page=products&action=view&id=<?php echo $product['id']; ?>"
                                            class="btn btn-sm btn-primary">View</a>
                                        <?php if (hasRole('admin')): ?>
                                            <a href="index.php?page=products&action=edit&id=<?php echo $product['id']; ?>"
                                                class="btn btn-sm btn-outline">Edit</a>
                                            <a href="index.php?page=products&action=delete&id=<?php echo $product['id']; ?>"
                                                class="btn btn-sm btn-danger"
                                                onclick="return confirm('Are you sure you want to delete this product?');">Delete</a>
                                        <?php endif; ?>
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
                        $baseUrl = 'index.php?page=products';
                        if ($search) {
                            $baseUrl .= '&search=' . urlencode($search);
                        }
                        echo generatePagination($page, $totalPages, $baseUrl);
                        ?>
                    </div>
                <?php endif; ?>

            <?php else: ?>
                <p class="text-muted">No products found.
                    <?php if (hasRole('admin')): ?><a href="index.php?page=products&action=create">Add your first
                            product</a>
                    <?php endif; ?>
                </p>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../../includes/footer.php'; ?>