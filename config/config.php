<?php

/**
 * Database Configuration
 * Adjust DB_USER and DB_PASS to match your local MySQL credentials.
 */
define("DB_HOST", "127.0.0.1:3306");
define("DB_NAME", "fyp_cherating");
define("DB_USER", "root");
define("DB_PASS", "123456");

/**
 * Application Settings
 */
define("APP_NAME", "Cherating Guest House");
// define("APP_URL", "http://localhost/fyp_cherating"); // Adjust if needed
define('OPENAI_KEY', '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');
define("APP_URL", "http://localhost:8000/FYP/fyp_cherating"); // Adjust if needed

/**
 * Error Reporting
 * Show all errors during development. 
 * Turn off display_errors in production.
 */
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

/**
 * Default Timezone
 */
date_default_timezone_set("Asia/Kuala_Lumpur");
