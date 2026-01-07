<?php
$pageTitle = 'Stock ' . $movementType;
require_once __DIR__ . '/../../includes/header.php';
?>

<div class="container">
    <div class="page-header">
        <h1>Stock
            <?php echo $movementType; ?>
        </h1>
        <a href="index.php?page=stock" class="btn btn-outline">← Back to Movements</a>
    </div>

    <div class="card">
        <div class="card-body">
            <form action="index.php?page=stock&action=<?php echo strtolower($movementType); ?>" method="POST"
                class="form" id="stockForm">
                <div class="form-group">
                    <label for="product_id">Product <span class="text-danger">*</span></label>
                    <select id="product_id" name="product_id" class="form-control" required>
                        <option value="">Select a product...</option>
                        <?php foreach ($products as $prod): ?>
                            <option value="<?php echo $prod['id']; ?>" data-quantity="<?php echo $prod['quantity']; ?>">
                                <?php echo htmlspecialchars($prod['sku'] . ' - ' . $prod['name']); ?>
                                (Stock:
                                <?php echo $prod['quantity']; ?>)
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="form-group">
                    <label for="quantity">Quantity <span class="text-danger">*</span></label>
                    <input type="number" id="quantity" name="quantity" class="form-control" min="1" required>
                    <small class="form-text" id="stockInfo"></small>
                </div>

                <div class="form-group">
                    <label for="note">Note</label>
                    <textarea id="note" name="note" class="form-control" rows="3"
                        placeholder="Optional note about this stock movement..."></textarea>
                </div>

                <div class="form-actions">
                    <button type="submit" class="btn btn-<?php echo $movementType === 'IN' ? 'success' : 'warning'; ?>">
                        <?php echo $movementType === 'IN' ? '+ Add Stock' : '- Remove Stock'; ?>
                    </button>
                    <a href="index.php?page=stock" class="btn btn-outline">Cancel</a>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    // Real-time stock validation
    document.addEventListener('DOMContentLoaded', function () {
        const productSelect = document.getElementById('product_id');
        const quantityInput = document.getElementById('quantity');
        const stockInfo = document.getElementById('stockInfo');
        const movementType = '<?php echo $movementType; ?>';

        productSelect.addEventListener('change', function () {
            const selectedOption = this.options[this.selectedIndex];
            const currentStock = parseInt(selectedOption.dataset.quantity) || 0;

            if (movementType === 'OUT') {
                quantityInput.max = currentStock;
                stockInfo.textContent = `Available stock: ${currentStock} units`;
                stockInfo.className = 'form-text text-info';
            } else {
                stockInfo.textContent = `Current stock: ${currentStock} units`;
                stockInfo.className = 'form-text text-muted';
            }
        });

        if (movementType === 'OUT') {
            quantityInput.addEventListener('input', function () {
                const selectedOption = productSelect.options[productSelect.selectedIndex];
                const currentStock = parseInt(selectedOption.dataset.quantity) || 0;
                const requestedQty = parseInt(this.value) || 0;

                if (requestedQty > currentStock) {
                    stockInfo.textContent = `⚠️ Insufficient stock! Available: ${currentStock} units`;
                    stockInfo.className = 'form-text text-danger';
                } else {
                    stockInfo.textContent = `Available stock: ${currentStock} units`;
                    stockInfo.className = 'form-text text-info';
                }
            });
        }
    });
</script>

<?php require_once __DIR__ . '/../../includes/footer.php'; ?>