<?php
$pageTitle = isset($product) && $product ? 'Edit Product' : 'Add Product';
require_once __DIR__ . '/../../includes/header.php';
?>

<div class="container">
    <div class="page-header">
        <h1>
            <?php echo $pageTitle; ?>
        </h1>
        <a href="index.php?page=products" class="btn btn-outline">← Back to Products</a>
    </div>

    <div class="card">
        <div class="card-body">
            <form
                action="<?php echo isset($product) && $product ? 'index.php?page=products&action=edit&id=' . $product['id'] : 'index.php?page=products&action=create'; ?>"
                method="POST" class="form">
                <div class="form-row">
                    <div class="form-group col-md-6">
                        <label for="sku">SKU <span class="text-danger">*</span></label>
                        <input type="text" id="sku" name="sku" class="form-control"
                            value="<?php echo isset($product) ? htmlspecialchars($product['sku']) : ''; ?>" required
                            pattern="[A-Z0-9\-]{3,50}" title="Alphanumeric with hyphens, 3-50 characters">
                        <small class="form-text">Unique product identifier (e.g., SKU-001)</small>
                    </div>

                    <div class="form-group col-md-6">
                        <label for="name">Product Name <span class="text-danger">*</span></label>
                        <input type="text" id="name" name="name" class="form-control"
                            value="<?php echo isset($product) ? htmlspecialchars($product['name']) : ''; ?>" required>
                    </div>
                </div>

                <div class="form-group">
                    <label for="description">Description</label>
                    <textarea id="description" name="description" class="form-control"
                        rows="3"><?php echo isset($product) ? htmlspecialchars($product['description']) : ''; ?></textarea>
                </div>

                <div class="form-row">
                    <div class="form-group col-md-6">
                        <label for="purchase_price">Purchase Price <span class="text-danger">*</span></label>
                        <input type="number" id="purchase_price" name="purchase_price" class="form-control" step="0.01"
                            min="0" value="<?php echo isset($product) ? $product['purchase_price'] : '0.00'; ?>"
                            required>
                    </div>

                    <div class="form-group col-md-6">
                        <label for="selling_price">Selling Price <span class="text-danger">*</span></label>
                        <input type="number" id="selling_price" name="selling_price" class="form-control" step="0.01"
                            min="0" value="<?php echo isset($product) ? $product['selling_price'] : '0.00'; ?>"
                            required>
                    </div>
                </div>

                <div class="form-row">
                    <?php if (!isset($product)): ?>
                        <div class="form-group col-md-4">
                            <label for="quantity">Initial Quantity <span class="text-danger">*</span></label>
                            <input type="number" id="quantity" name="quantity" class="form-control" min="0" value="0"
                                required>
                            <small class="form-text">Starting stock quantity</small>
                        </div>
                    <?php endif; ?>

                    <div class="form-group col-md-<?php echo !isset($product) ? '4' : '6'; ?>">
                        <label for="minimum_quantity">Minimum Quantity <span class="text-danger">*</span></label>
                        <input type="number" id="minimum_quantity" name="minimum_quantity" class="form-control" min="0"
                            value="<?php echo isset($product) ? $product['minimum_quantity'] : '10'; ?>" required>
                        <small class="form-text">Low stock alert threshold</small>
                    </div>

                    <div class="form-group col-md-<?php echo !isset($product) ? '4' : '6'; ?>">
                        <label for="supplier_id">Supplier</label>
                        <select id="supplier_id" name="supplier_id" class="form-control">
                            <option value="">Select a supplier...</option>
                            <?php foreach ($suppliers as $s): ?>
                                <option value="<?php echo $s['id']; ?>" <?php echo (isset($product) && $product['supplier_id'] == $s['id']) ? 'selected' : ''; ?>>
                                    <?php echo htmlspecialchars($s['name']); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>

                <div class="form-group">
                    <div class="form-check">
                        <input type="checkbox" id="is_active" name="is_active" class="form-check-input" <?php echo (!isset($product) || $product['is_active']) ? 'checked' : ''; ?>>
                        <label for="is_active" class="form-check-label">Active</label>
                    </div>
                </div>

                <div class="form-actions">
                    <button type="submit" class="btn btn-primary">
                        <?php echo isset($product) && $product ? 'Update Product' : 'Create Product'; ?>
                    </button>
                    <a href="index.php?page=products" class="btn btn-outline">Cancel</a>
                </div>
            </form>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../../includes/footer.php'; ?>