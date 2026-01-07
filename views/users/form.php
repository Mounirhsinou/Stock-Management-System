<?php
$isEdit = isset($user) && $user !== null;
$pageTitle = $isEdit ? 'Edit User' : 'Add New User';
require_once __DIR__ . '/../../includes/header.php';
?>

<div class="container">
    <div class="page-header">
        <h1><?php echo $pageTitle; ?></h1>
        <a href="index.php?page=users" class="btn btn-outline">← Back to Users</a>
    </div>

    <div class="card">
        <div class="card-body">
            <form action="index.php?page=users&action=<?php echo $isEdit ? 'edit&id=' . $user['id'] : 'create'; ?>" method="POST" class="form">
                <div class="form-row">
                    <div class="form-group">
                        <label for="username">Username <span class="text-danger">*</span></label>
                        <input type="text" id="username" name="username" class="form-control" 
                               value="<?php echo $isEdit ? htmlspecialchars($user['username']) : ''; ?>" required>
                    </div>
                    <div class="form-group">
                        <label for="email">Email Address <span class="text-danger">*</span></label>
                        <input type="email" id="email" name="email" class="form-control" 
                               value="<?php echo $isEdit ? htmlspecialchars($user['email']) : ''; ?>" required>
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label for="full_name">Full Name <span class="text-danger">*</span></label>
                        <input type="text" id="full_name" name="full_name" class="form-control" 
                               value="<?php echo $isEdit ? htmlspecialchars($user['full_name']) : ''; ?>" required>
                    </div>
                    <div class="form-group">
                        <label for="role">User Role <span class="text-danger">*</span></label>
                        <select id="role" name="role" class="form-control" required>
                            <option value="viewer" <?php echo $isEdit && $user['role'] === 'viewer' ? 'selected' : ''; ?>>Viewer (Read-only)</option>
                            <option value="staff" <?php echo $isEdit && $user['role'] === 'staff' ? 'selected' : ''; ?>>Staff (Stock Management)</option>
                            <option value="admin" <?php echo $isEdit && $user['role'] === 'admin' ? 'selected' : ''; ?>>Admin (Full Access)</option>
                        </select>
                    </div>
                </div>

                <div class="form-group">
                    <label for="password">Password <?php echo !$isEdit ? '<span class="text-danger">*</span>' : '<small class="text-muted">(Leave blank to keep current)</small>'; ?></label>
                    <input type="password" id="password" name="password" class="form-control" <?php echo !$isEdit ? 'required' : ''; ?>>
                    <?php if (!$isEdit): ?>
                        <small class="form-text">Minimum 6 characters.</small>
                    <?php endif; ?>
                </div>

                <div class="form-group">
                    <div class="form-check">
                        <input type="checkbox" id="is_active" name="is_active" class="form-check-input" 
                               <?php echo !$isEdit || $user['is_active'] ? 'checked' : ''; ?>>
                        <label for="is_active" class="form-check-label">Active Account</label>
                    </div>
                    <small class="form-text">Inactive users cannot log in to the system.</small>
                </div>

                <div class="form-actions">
                    <button type="submit" class="btn btn-primary">
                        <?php echo $isEdit ? 'Update User' : 'Create User'; ?>
                    </button>
                    <a href="index.php?page=users" class="btn btn-outline">Cancel</a>
                </div>
            </form>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../../includes/footer.php'; ?>
