<?php

$user = getCurrentUser();
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title><?= APP_NAME ?> - <?= ucfirst($page) ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
    <link rel="stylesheet" href="<?= BASE_URL ?>/public/css/style.css?v=1" />
</head>

<body>
    <!-- Navigation -->
    <nav class="navbar navbar-expand-lg navbar-light bg-white border-bottom">
        <div class="container">
            <a class="navbar-brand fw-bold" href="<?= BASE_URL ?>">
                <img src="/images/logo.svg" alt="<?= APP_NAME ?>" height="24" class="me-2" />
                <?= APP_NAME ?>
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="<?= BASE_URL ?>">Home</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="<?= BASE_URL ?>/?page=cart">
                            Cart <span class="cart-count" id="cartCount">0</span>
                        </a>
                    </li>
                    <?php if (isLoggedIn()): ?>
                        <li class="nav-item">
                            <a class="nav-link" href="<?= BASE_URL ?>/?page=profile">Profile</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="<?= BASE_URL ?>/?page=orders">Orders</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="<?= BASE_URL ?>/?action=logout">Logout (<?= htmlspecialchars($user['name'] ?? '') ?>)</a>
                        </li>
                    <?php else: ?>
                        <li class="nav-item">
                            <a class="nav-link" href="<?= BASE_URL ?>/?page=login">Login</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="<?= BASE_URL ?>/?page=register">Register</a>
                        </li>
                    <?php endif; ?>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Main Content -->
    <div class="container mt-5">
        <?php if (isset($data['error'])): ?>
            <div class="alert alert-danger">
                <?= htmlspecialchars($data['error']) ?>
            </div>
        <?php endif; ?>

        <?php
        // Include the page view (layout.php is in /views/ so we're already in the right directory)
        $viewFile = __DIR__ . '/' . $page . '.php';
        if (file_exists($viewFile)) {
            include $viewFile;
        } else {
            echo '<div class="alert alert-warning">View not found: ' . htmlspecialchars($page) . ' at ' . $viewFile . '</div>';
        }
        ?>
    </div>

    <!-- Footer -->
    <footer class="bg-light py-4 mt-5">
        <div class="container text-center text-muted">
            <p>&copy; 2026 <?= APP_NAME ?>. All rights reserved.</p>
        </div>
    </footer>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        window.API_URL = "<?= API_URL ?>";
        window.BASE_URL = "<?= BASE_URL ?>";
    </script>
    <script src="<?= BASE_URL ?>/public/js/app.js"></script>
    <?php if (file_exists(__DIR__ . '/../public/js/pages/' . $page . '.js')): ?>
        <script src="<?= BASE_URL ?>/public/js/pages/<?= $page ?>.js"></script>
    <?php endif; ?>
</body>

</html>