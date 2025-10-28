<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>404 Not Found - <?= APP_NAME ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
    <div class="container text-center py-5">
        <h1 class="display-1 text-warning fw-bold">404</h1>
        <p class="lead">The page you requested was not found.</p>
        <?php if (!empty($uri)): ?>
            <p class="text-muted">Requested URI: <code><?= htmlspecialchars($uri) ?></code></p>
        <?php endif; ?>
        <a href="<?= APP_URL ?>" class="btn btn-primary mt-3">Go Back Home</a>
    </div>
</body>
</html>