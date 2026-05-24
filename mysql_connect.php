<?php

mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

// Read Railway environment variables first, fall back to local XAMPP defaults.
// Railway variable names: DB_HOST, DB_PASS, DB_NAME, DB_PORT, DB_ROOT (username)
DEFINE('DB_HOST', getenv('DB_HOST') ?: getenv('MYSQLHOST') ?: '127.0.0.1');
DEFINE('DB_USER', getenv('DB_ROOT') ?: getenv('DB_USER') ?: getenv('MYSQLUSER') ?: 'root');
DEFINE('DB_PASS', getenv('DB_PASS') ?: getenv('DB_PASSWORD') ?: getenv('MYSQLPASSWORD') ?: '');
DEFINE('DB_NAME', getenv('DB_NAME') ?: getenv('MYSQLDATABASE') ?: 'cyri_drive_co');
DEFINE('DB_PORT', getenv('DB_PORT') ?: getenv('MYSQLPORT') ?: '3306');

try {
    $dbc = mysqli_connect(DB_HOST, DB_USER, DB_PASS, DB_NAME, DB_PORT);
} catch (mysqli_sql_exception $e) {
    error_log('Database connection error: ' . $e->getMessage());
    die('Database connection error. Please check configuration.');
}

mysqli_set_charset($dbc, 'utf8mb4');
