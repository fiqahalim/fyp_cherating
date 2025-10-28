<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>500 Internal Server Error - <?= APP_NAME ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
    <div class="container text-center py-5">
        <h1 class="display-1 text-danger fw-bold">500</h1>
        <p class="lead">Something went wrong on our side. Please try again later.</p>

        <?php if (ini_get('display_errors')): ?>
            <div class="alert alert-danger text-start mt-4">
                <strong>Error Message:</strong><br>
                <?= htmlspecialchars($e->getMessage()) ?><br><br>
                <strong>File:</strong> <?= $e->getFile() ?> (line <?= $e->getLine() ?>)
            </div>
        <?php endif; ?>

        <a href="<?= APP_URL ?>" class="btn btn-primary mt-3">Go Back Home</a>
    </div>
</body>
</html>