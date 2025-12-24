<?php

// Start the session before anything else
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// 1) Config & helpers (always load first)
require_once __DIR__ . '/config/config.php';
require_once __DIR__ . '/helpers/functions.php';
require_once __DIR__ . '/helpers/ChatbotHelper.php';
require_once __DIR__ . '/vendor/autoload.php';

// 2) Global error & exception handler
set_error_handler(function ($severity, $message, $file, $line) {
    if (!(error_reporting() & $severity)) {
        return;
    }
    throw new ErrorException($message, 0, $severity, $file, $line);
});

set_exception_handler(function ($e) {
    http_response_code(500);

    // Simple but clear error page
    echo "<!DOCTYPE html>
    <html lang='en'>
    <head>
        <meta charset='UTF-8'>
        <title>Error - " . APP_NAME . "</title>
        <link href='https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css' rel='stylesheet'>
    </head>
    <body class='bg-light'>
        <div class='container py-5'>
            <div class='alert alert-danger shadow'>
                <h4 class='alert-heading'>An error occurred</h4>
                <p><strong>Message:</strong> " . htmlspecialchars($e->getMessage()) . "</p>
                <p><strong>File:</strong> " . htmlspecialchars($e->getFile()) . "</p>
                <p><strong>Line:</strong> " . $e->getLine() . "</p>
            </div>
        </div>
    </body>
    </html>";
});

// 3) Core classes
require_once __DIR__ . '/core/Router.php';
require_once __DIR__ . '/core/Database.php';
require_once __DIR__ . '/core/Auth.php';
require_once __DIR__ . '/core/Controller.php';
require_once __DIR__ . '/core/Model.php';
require_once __DIR__ . '/core/Flash.php';

// 4) Autoload controllers & models
spl_autoload_register(function ($class) {
    $paths = [
        __DIR__ . "/controllers/$class.php",
        __DIR__ . "/models/$class.php",
    ];
    foreach ($paths as $path) {
        if (is_file($path)) {
            require_once $path;
            return;
        }
    }
});

// 5) Load route definitions
require_once __DIR__ . '/routes/web.php';

// 6) Dispatch the current request
Route::dispatch();