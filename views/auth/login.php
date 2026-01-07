<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login -
        <?php echo APP_NAME; ?>
    </title>
    <link rel="stylesheet" href="assets/css/style.css">
</head>

<body class="auth-page">
    <div class="auth-container">
        <div class="auth-card">
            <div class="auth-header">
                <h1>
                    <?php echo APP_NAME; ?>
                </h1>
                <p>Sign in to manage your inventory</p>
            </div>

            <?php
            $flashMessage = getFlashMessage();
            if ($flashMessage):
                ?>
                <div class="alert alert-<?php echo $flashMessage['type']; ?>">
                    <?php echo $flashMessage['message']; ?>
                </div>
            <?php endif; ?>

            <form action="index.php?page=login" method="POST" class="auth-form">
                <div class="form-group">
                    <label for="username">Username or Email</label>
                    <input type="text" id="username" name="username" class="form-control" required autofocus>
                </div>

                <div class="form-group">
                    <label for="password">Password</label>
                    <input type="password" id="password" name="password" class="form-control" required>
                </div>

                <button type="submit" class="btn btn-primary btn-block">Sign In</button>
            </form>

            <div class="auth-footer">
                <p class="demo-accounts">
                    <strong>Demo Accounts:</strong><br>
                    Admin: <code>admin / admin123</code><br>
                    Staff: <code>staff / staff123</code><br>
                    Viewer: <code>viewer / viewer123</code>
                </p>
            </div>
        </div>
    </div>
</body>

</html>