<?php

mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

// ─── Railway Production Credentials ────────────────────────────────────────
// These match the variables set in Railway's MySQL service variables panel.
// On Railway:  DB_HOST, DB_PASS, DB_NAME, DB_PORT are set; DB_ROOT is the user.
// Locally on XAMPP: none of these are set, so the local defaults apply.

if (getenv('RAILWAY_ENVIRONMENT') !== false || getenv('DB_HOST') === 'mysql.railway.internal') {
    // ── Running on Railway ──
    DEFINE('DB_HOST', 'mysql.railway.internal');
    DEFINE('DB_USER', 'root');
    DEFINE('DB_PASS', 'WcPxaesYmUdQjBcmizQuGWYhOyutIRzn');
    DEFINE('DB_NAME', 'railway');
    DEFINE('DB_PORT', '3306');
} else {
    // ── Running locally on XAMPP ──
    DEFINE('DB_HOST', getenv('DB_HOST') ?: '127.0.0.1');
    DEFINE('DB_USER', getenv('DB_USER') ?: 'root');
    DEFINE('DB_PASS', getenv('DB_PASS') ?: '');
    DEFINE('DB_NAME', getenv('DB_NAME') ?: 'cyri_drive_co');
    DEFINE('DB_PORT', getenv('DB_PORT') ?: '3306');
}

try {
    $dbc = mysqli_connect(DB_HOST, DB_USER, DB_PASS, DB_NAME, (int) DB_PORT);
} catch (mysqli_sql_exception $e) {
    error_log('Database connection error: ' . $e->getMessage());
    die('Database connection error. Please check configuration.');
}

mysqli_set_charset($dbc, 'utf8mb4');
