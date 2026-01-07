<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>
        <?php echo isset($pageTitle) ? $pageTitle . ' - ' : ''; ?>
        <?php echo APP_NAME; ?>
    </title>
    <link rel="stylesheet" href="assets/css/style.css">
</head>

<body>
    <?php if (isLoggedIn()): ?>
        <nav class="navbar">
            <div class="container">
                <div class="nav-brand">
                    <h2>
                        <?php echo APP_NAME; ?>
                    </h2>
                </div>
                <ul class="nav-menu">
                    <li><a href="index.php?page=dashboard"
                            class="<?php echo (isset($_GET['page']) && $_GET['page'] === 'dashboard') ? 'active' : ''; ?>">Dashboard</a>
                    </li>
                    <li><a href="index.php?page=products"
                            class="<?php echo (isset($_GET['page']) && $_GET['page'] === 'products') ? 'active' : ''; ?>">Products</a>
                    </li>
                    <li><a href="index.php?page=stock"
                            class="<?php echo (isset($_GET['page']) && $_GET['page'] === 'stock') ? 'active' : ''; ?>">Stock
                            Movements</a></li>
                    <li><a href="index.php?page=suppliers"
                            class="<?php echo (isset($_GET['page']) && $_GET['page'] === 'suppliers') ? 'active' : ''; ?>">Suppliers</a>
                    </li>
                    <?php if (hasRole('admin')): ?>
                        <li><a href="index.php?page=users"
                                class="<?php echo (isset($_GET['page']) && $_GET['page'] === 'users') ? 'active' : ''; ?>">Users</a>
                        </li>
                    <?php endif; ?>
                </ul>
                <div class="nav-user">
                    <span class="user-info">
                        <strong>
                            <?php echo htmlspecialchars($_SESSION['full_name']); ?>
                        </strong>
                        <small>(
                            <?php echo ucfirst($_SESSION['user_role']); ?>)
                        </small>
                    </span>
                    <a href="index.php?page=logout" class="btn btn-sm btn-outline">Logout</a>
                </div>
            </div>
        </nav>
    <?php endif; ?>

    <main class="main-content">
        <?php
        // Display flash messages
        $flashMessage = getFlashMessage();
        if ($flashMessage):
            ?>
            <div class="container">
                <div class="alert alert-<?php echo $flashMessage['type']; ?>">
                    <?php echo $flashMessage['message']; ?>
                </div>
            </div>
        <?php endif; ?>