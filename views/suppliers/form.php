<?php
$isEdit = isset($supplier) && $supplier !== null;
$pageTitle = $isEdit ? 'Edit Supplier' : 'Add New Supplier';
require_once __DIR__ . '/../../includes/header.php';
?>

<div class="container">
    <div class="page-header">
        <h1><?php echo $pageTitle; ?></h1>
        <a href="index.php?page=suppliers" class="btn btn-outline">← Back to Suppliers</a>
    </div>

    <div class="card">
        <div class="card-body">
            <form action="index.php?page=suppliers&action=<?php echo $isEdit ? 'edit&id=' . $supplier['id'] : 'create'; ?>" method="POST" class="form">
                <div class="form-group">
                    <label for="name">Supplier Name <span class="text-danger">*</span></label>
                    <input type="text" id="name" name="name" class="form-control" 
                           value="<?php echo $isEdit ? htmlspecialchars($supplier['name']) : ''; ?>" required>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label for="contact_name">Contact Person</label>
                        <input type="text" id="contact_name" name="contact_name" class="form-control" 
                               value="<?php echo $isEdit ? htmlspecialchars($supplier['contact_name']) : ''; ?>">
                    </div>
                    <div class="form-group">
                        <label for="email">Email Address</label>
                        <input type="email" id="email" name="email" class="form-control" 
                               value="<?php echo $isEdit ? htmlspecialchars($supplier['email']) : ''; ?>">
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label for="phone">Phone Number</label>
                        <input type="text" id="phone" name="phone" class="form-control" 
                               value="<?php echo $isEdit ? htmlspecialchars($supplier['phone']) : ''; ?>">
                    </div>
                    <div class="form-group">
                        <div class="form-check" style="margin-top: 2rem;">
                            <input type="checkbox" id="is_active" name="is_active" class="form-check-input" 
                                   <?php echo !$isEdit || $supplier['is_active'] ? 'checked' : ''; ?>>
                            <label for="is_active" class="form-check-label">Active Supplier</label>
                        </div>
                    </div>
                </div>

                <div class="form-group">
                    <label for="address">Address</label>
                    <textarea id="address" name="address" class="form-control" rows="3"><?php echo $isEdit ? htmlspecialchars($supplier['address']) : ''; ?></textarea>
                </div>

                <div class="form-actions">
                    <button type="submit" class="btn btn-primary">
                        <?php echo $isEdit ? 'Update Supplier' : 'Create Supplier'; ?>
                    </button>
                    <a href="index.php?page=suppliers" class="btn btn-outline">Cancel</a>
                </div>
            </form>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../../includes/footer.php'; ?>
