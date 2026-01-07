<?php
$pageTitle = 'Manage Users';
require_once __DIR__ . '/../../includes/header.php';
?>

<div class="container">
    <div class="page-header">
        <h1>Manage Users</h1>
        <div class="page-actions">
            <a href="index.php?page=users&action=create" class="btn btn-primary">+ Add New User</a>
        </div>
    </div>

    <div class="card">
        <div class="card-body">
            <?php if (!empty($users)): ?>
                <div class="table-responsive">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Username</th>
                                <th>Full Name</th>
                                <th>Email</th>
                                <th>Role</th>
                                <th>Status</th>
                                <th>Created At</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($users as $u): ?>
                                <tr>
                                    <td><code><?php echo htmlspecialchars($u['username']); ?></code></td>
                                    <td>
                                        <?php echo htmlspecialchars($u['full_name']); ?>
                                    </td>
                                    <td>
                                        <?php echo htmlspecialchars($u['email']); ?>
                                    </td>
                                    <td>
                                        <span class="badge badge-<?php
                                        echo $u['role'] === 'admin' ? 'danger' : ($u['role'] === 'staff' ? 'primary' : 'secondary');
                                        ?>">
                                            <?php echo ucfirst($u['role']); ?>
                                        </span>
                                    </td>
                                    <td>
                                        <span class="badge badge-<?php echo $u['is_active'] ? 'success' : 'outline'; ?>">
                                            <?php echo $u['is_active'] ? 'Active' : 'Inactive'; ?>
                                        </span>
                                    </td>
                                    <td>
                                        <?php echo formatDate($u['created_at'], 'M d, Y'); ?>
                                    </td>
                                    <td>
                                        <a href="index.php?page=users&action=edit&id=<?php echo $u['id']; ?>"
                                            class="btn btn-sm btn-outline">Edit</a>
                                        <?php if ($u['id'] !== getCurrentUserId()): ?>
                                            <a href="index.php?page=users&action=delete&id=<?php echo $u['id']; ?>"
                                                class="btn btn-sm btn-danger"
                                                onclick="return confirm('Are you sure you want to delete this user?')">Delete</a>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>

                <?php echo generatePagination($page, $totalPages, 'index.php?page=users'); ?>

            <?php else: ?>
                <p class="text-muted">No users found.</p>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../../includes/footer.php'; ?>