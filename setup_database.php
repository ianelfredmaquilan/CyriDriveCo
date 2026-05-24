<?php
require_once('mysql_connect.php');

$errors = [];
$success = [];

$sql_statements = [
    "ALTER TABLE customers ADD COLUMN password_hash VARCHAR(255)",
    "ALTER TABLE customers ADD COLUMN email_verified BOOLEAN DEFAULT 0",
    "ALTER TABLE customers ADD UNIQUE INDEX unique_email (email)",
    "ALTER TABLE bookings ADD COLUMN start_date DATE",
    "ALTER TABLE bookings ADD COLUMN end_date DATE",
    "ALTER TABLE bookings ADD COLUMN pickup_time TIME DEFAULT '09:00:00'",
    "ALTER TABLE bookings ADD COLUMN return_time TIME DEFAULT '17:00:00'",
    "CREATE TABLE IF NOT EXISTS admins (
        admin_id INT PRIMARY KEY AUTO_INCREMENT,
        username VARCHAR(50) NOT NULL UNIQUE,
        password_hash VARCHAR(255) NOT NULL,
        email VARCHAR(100),
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )"
];

foreach ($sql_statements as $sql) {
    try {
        if (mysqli_query($dbc, $sql)) {
            $success[] = $sql;
        }
    } catch (mysqli_sql_exception $e) {
        $error = $e->getMessage();
        if (strpos($error, 'Duplicate column') !== false || strpos($error, 'already exists') !== false || strpos($error, 'Duplicate key') !== false) {
            $success[] = $sql . " (already exists - skipped)";
        } else {
            $errors[] = $sql . " - Error: " . $error;
        }
    }
}

try {
    $check_admin = mysqli_query($dbc, "SELECT COUNT(*) as admin_count FROM admins");
    if ($check_admin) {
        $row = mysqli_fetch_assoc($check_admin);
        if ($row['admin_count'] == 0) {
            $default_hash = password_hash('admin123', PASSWORD_BCRYPT);
            if (mysqli_query($dbc, "INSERT INTO admins (username, password_hash, email) VALUES ('admin', '$default_hash', 'admin@cyridrive.com')")) {
                $success[] = "Default Admin User Created (Username: admin | Password: admin123)";
            }
        } else {
            $success[] = "Admin User Check: Active (Admin users already exist)";
        }
    }
} catch (mysqli_sql_exception $e) {
    $errors[] = "Admin setup failed: " . $e->getMessage();
}

mysqli_close($dbc);
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Database Update - CYRI DRIVE CO</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Poppins', sans-serif; background: #f0f0f0; padding: 40px 20px; }
        .container { max-width: 800px; margin: 0 auto; background: white; padding: 40px; border-radius: 12px; box-shadow: 0 5px 20px rgba(0,0,0,0.1); }
        h1 { color: #111; margin-bottom: 20px; }
        .success { background: #d4edda; color: #155724; padding: 12px 15px; border-radius: 8px; margin-bottom: 10px; border-left: 4px solid #28a745; }
        .error { background: #f8d7da; color: #721c24; padding: 12px 15px; border-radius: 8px; margin-bottom: 10px; border-left: 4px solid #f5c6cb; }
        .info { background: #e7f3ff; color: #1565c0; padding: 15px; border-radius: 8px; margin-top: 20px; border-left: 4px solid #2196F3; }
        button { padding: 12px 24px; background: linear-gradient(45deg, #ff1e1e, #c40000); color: white; border: none; border-radius: 8px; font-weight: 600; cursor: pointer; }
        button:hover { transform: scale(1.05); }
    </style>
</head>
<body>

<div class="container">
    <h1>✅ Database Update Tool</h1>

    <?php if (empty($errors)): ?>
        <div class="success">
            <strong>🎉 Success!</strong> All database columns have been updated (or already existed).
        </div>
    <?php else: ?>
        <div class="error">
            <strong>❌ Some errors occurred:</strong>
        </div>
        <?php foreach ($errors as $error): ?>
            <div class="error" style="margin-top: 5px;"><?= htmlspecialchars($error) ?></div>
        <?php endforeach; ?>
    <?php endif; ?>

    <h3 style="margin-top: 30px; margin-bottom: 15px;">Executed Commands:</h3>
    <?php foreach ($success as $cmd): ?>
        <div class="success"><?= htmlspecialchars($cmd) ?></div>
    <?php endforeach; ?>

    <div class="info">
        <strong>✅ Next Steps:</strong><br>
        1. If all commands show success, refresh your browser (Ctrl+F5)<br>
        2. Try registering a new account at: <a href="customer_register.php" style="color: #1565c0; text-decoration: none;"><strong>customer_register.php</strong></a><br>
        3. Login and test booking with dates
    </div>

    <button onclick="window.location.href='index.html'" style="margin-top: 20px;">← Back to Main Page</button>
</div>

</body>
</html>
