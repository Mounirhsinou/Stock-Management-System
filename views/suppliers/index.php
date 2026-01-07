<?php
$pageTitle = 'Suppliers';
require_once __DIR__ . '/../../includes/header.php';
?>

<div class="container">
    <div class="page-header">
        <h1>Suppliers</h1>
        <div class="page-actions">
            <?php if (hasRole(['admin', 'staff'])): ?>
                <a href="index.php?page=suppliers&action=create" class="btn btn-primary">+ Add New Supplier</a>
            <?php endif; ?>
        </div>
    </div>

    <div class="card">
        <div class="card-body">
            <?php if (!empty($suppliers)): ?>
                <div class="table-responsive">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Name</th>
                                <th>Contact Person</th>
                                <th>Email</th>
                                <th>Phone</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($suppliers as $s): ?>
                                <tr>
                                    <td><strong>
                                            <?php echo htmlspecialchars($s['name']); ?>
                                        </strong></td>
                                    <td>
                                        <?php echo htmlspecialchars($s['contact_name'] ?: '-'); ?>
                                    </td>
                                    <td>
                                        <?php echo htmlspecialchars($s['email'] ?: '-'); ?>
                                    </td>
                                    <td>
                                        <?php echo htmlspecialchars($s['phone'] ?: '-'); ?>
                                    </td>
                                    <td>
                                        <span class="badge badge-<?php echo $s['is_active'] ? 'success' : 'outline'; ?>">
                                            <?php echo $s['is_active'] ? 'Active' : 'Inactive'; ?>
                                        </span>
                                    </td>
                                    <td>
                                        <a href="index.php?page=suppliers&action=edit&id=<?php echo $s['id']; ?>"
                                            class="btn btn-sm btn-outline">Edit</a>
                                        <?php if (hasRole('admin')): ?>
                                            <a href="index.php?page=suppliers&action=delete&id=<?php echo $s['id']; ?>"
                                                class="btn btn-sm btn-danger"
                                                onclick="return confirm('Are you sure you want to delete this supplier?')">Delete</a>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>

                <?php echo generatePagination($page, $totalPages, 'index.php?page=suppliers'); ?>

            <?php else: ?>
                <p class="text-muted">No suppliers found.</p>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../../includes/footer.php'; ?>