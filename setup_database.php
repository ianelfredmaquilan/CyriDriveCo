<?php
require_once('mysql_connect.php');

$errors = [];
$success = [];

// Create tables
$tables_queries = [
    "vehicles" => "CREATE TABLE IF NOT EXISTS `vehicles` (
        `vehicle_id` int(11) NOT NULL AUTO_INCREMENT,
        `vehicle_name` varchar(100) NOT NULL,
        `category` varchar(50) NOT NULL,
        `price_per_day` decimal(10,2) NOT NULL,
        `engine` varchar(100) DEFAULT NULL,
        `transmission` varchar(30) DEFAULT NULL,
        `seats` int(11) DEFAULT NULL,
        `fuel_type` varchar(30) DEFAULT NULL,
        `image_url` text DEFAULT NULL,
        `is_available` tinyint(1) DEFAULT 1,
        PRIMARY KEY (`vehicle_id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4",

    "customers" => "CREATE TABLE IF NOT EXISTS `customers` (
        `customer_id` int(11) NOT NULL AUTO_INCREMENT,
        `full_name` varchar(100) NOT NULL,
        `email` varchar(100) NOT NULL,
        `phone` varchar(20) NOT NULL,
        `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
        `password_hash` varchar(255) DEFAULT NULL,
        `email_verified` boolean DEFAULT 0,
        PRIMARY KEY (`customer_id`),
        UNIQUE KEY `unique_email` (`email`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4",

    "bookings" => "CREATE TABLE IF NOT EXISTS `bookings` (
        `booking_id` int(11) NOT NULL AUTO_INCREMENT,
        `customer_id` int(11) NOT NULL,
        `vehicle_id` int(11) NOT NULL,
        `booking_date` timestamp NOT NULL DEFAULT current_timestamp(),
        `number_of_days` int(11) NOT NULL,
        `price_per_day` decimal(10,2) NOT NULL,
        `total_amount` decimal(10,2) NOT NULL,
        `booking_status` varchar(20) DEFAULT 'Pending',
        `start_date` date DEFAULT NULL,
        `end_date` date DEFAULT NULL,
        `pickup_time` time DEFAULT '09:00:00',
        `return_time` time DEFAULT '17:00:00',
        PRIMARY KEY (`booking_id`),
        KEY `customer_id` (`customer_id`),
        KEY `vehicle_id` (`vehicle_id`),
        CONSTRAINT `bookings_ibfk_1` FOREIGN KEY (`customer_id`) REFERENCES `customers` (`customer_id`),
        CONSTRAINT `bookings_ibfk_2` FOREIGN KEY (`vehicle_id`) REFERENCES `vehicles` (`vehicle_id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4",

    "payments" => "CREATE TABLE IF NOT EXISTS `payments` (
        `payment_id` int(11) NOT NULL AUTO_INCREMENT,
        `booking_id` int(11) NOT NULL,
        `payment_method` varchar(50) DEFAULT NULL,
        `amount_paid` decimal(10,2) NOT NULL,
        `payment_status` varchar(20) DEFAULT 'Unpaid',
        `payment_date` timestamp NOT NULL DEFAULT current_timestamp(),
        PRIMARY KEY (`payment_id`),
        KEY `booking_id` (`booking_id`),
        CONSTRAINT `payments_ibfk_1` FOREIGN KEY (`booking_id`) REFERENCES `bookings` (`booking_id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4",

    "rentals" => "CREATE TABLE IF NOT EXISTS `rentals` (
        `id` int(11) NOT NULL AUTO_INCREMENT,
        `customer_name` varchar(100) NOT NULL,
        `car` varchar(100) NOT NULL,
        `price_per_day` decimal(10,2) NOT NULL,
        `days` int(11) NOT NULL,
        `total_price` decimal(10,2) NOT NULL,
        `booking_date` datetime NOT NULL,
        PRIMARY KEY (`id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4",

    "admins" => "CREATE TABLE IF NOT EXISTS `admins` (
        `admin_id` INT PRIMARY KEY AUTO_INCREMENT,
        `username` VARCHAR(50) NOT NULL UNIQUE,
        `password_hash` VARCHAR(255) NOT NULL,
        `email` VARCHAR(100),
        `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4"
];

// Execute table creations
foreach ($tables_queries as $name => $sql) {
    try {
        if (mysqli_query($dbc, $sql)) {
            $success[] = "Table '$name' created or already exists.";
        }
    } catch (mysqli_sql_exception $e) {
        $errors[] = "Failed creating table '$name': " . $e->getMessage();
    }
}

// Seed vehicles table
try {
    $check_vehicles = mysqli_query($dbc, "SELECT COUNT(*) as vehicle_count FROM vehicles");
    $row = mysqli_fetch_assoc($check_vehicles);
    if ($row['vehicle_count'] == 0) {
        $vehicles = [
            [1, 'Toyota Vios', 'Sedan', '1500.00', '1.3L', 'Automatic', 5, 'Gasoline', 'https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcQFuHcqPbXyNern0jXSoaVSs8wlqSkIR1Q_dQ&s', 1],
            [2, 'Mitsubishi Mirage', 'Sedan', '1400.00', '1.2L', 'Manual', 5, 'Gasoline', 'https://hips.hearstapps.com/hmg-prod/images/2024-mitsubishi-mirage-g4-103-6508a36ae3654.jpg?crop=0.792xw:1.00xh;0.106xw,0&resize=1200:*', 1],
            [3, 'Nissan Sentra', 'Sedan', '1700.00', '1.6L', 'Automatic', 5, 'Gasoline', 'https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcQjQ1oxexejkwAMwV6GtMUxDaEmqWvKY9J__Q&s', 1],
            [4, 'Suzuki Desire', 'Sedan', '1300.00', '1.2L', 'Manual', 5, 'Gasoline', 'https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcRr9N6kWfaAcNbgjNbMI6wNM1HOt8PqYJK-8A&s', 1],
            [5, 'Toyota Fortuner', 'SUV', '3000.00', '2.4L Diesel', 'Automatic', 7, 'Diesel', 'https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcTMKz0kZB5jUAQ2Bk1ikjznxdi7MutM2TFEdg&s', 1],
            [6, 'Ford Everest', 'SUV', '3200.00', '2.0L Diesel', 'Automatic', 7, 'Diesel', 'https://d1hv7ee95zft1i.cloudfront.net/custom/car-model-photo/mobile/gallery/ford-everest-6757da49ce3ec.jpg', 1],
            [7, 'Nissan Terra', 'SUV', '3100.00', '2.5L Diesel', 'Automatic', 7, 'Diesel', 'https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcRM4AI5zjpuYEB9rSERSM3bfRGl-p_33pBxmw&s', 1],
            [8, 'Mitsubishi Montero', 'SUV', '3000.00', '2.4L Diesel', 'Automatic', 7, 'Diesel', 'https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcRD3tICpKmo9iO6ipFxgqOHzmoTSSIMVhR3JA&s', 1],
            [9, 'Toyota Hilux', 'Pickup Truck', '2800.00', '2.4L Diesel', 'Manual', 5, 'Diesel', 'https://imgcdn.zigwheels.ph/large/gallery/exterior/30/813/toyota-hilux-front-angle-low-view-236132.jpg', 1],
            [10, 'Nissan Navara', 'Pickup Truck', '2900.00', '2.5L Diesel', 'Automatic', 5, 'Diesel', 'https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcQReZ68dyjhtpOjSi6CTiNS_c7aaBuMoyyydg&s', 1],
            [11, 'Ford Raptor', 'Pickup Truck', '4000.00', '2.0L Bi-Turbo', 'Automatic', 5, 'Diesel', 'https://d1hv7ee95zft1i.cloudfront.net/custom/car-model-photo/standard/ford-ranger-raptor-671aea0c12e9f.jpg', 1],
            [12, 'Mitsubishi Strada', 'Pickup Truck', '2700.00', '2.4L Diesel', 'Manual', 5, 'Diesel', 'https://blogger.googleusercontent.com/img/b/R29vZ2xl/AVvXsEghgrkqbrsU2QKM_aM_rj0waJRrWlC0Pd4HQSoQ38ivioE51P5UPJ4Pr_gHaV_asBuE3nso5GtwuRQESlntZ-pChOHUnTBT-fJmajKeebZnwj54lIlCDkVwdh3IMKZQG6UN2mf-689JBVrL/s1100/2020_mitsubishi_strada_athlete_4WD_00.jpg', 1],
            [13, 'Toyota Hiace', 'Van', '3500.00', '2.8L Diesel', 'Manual', 15, 'Diesel', 'https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcQSmAqf7DNLwXUIGrCiEobIxANGHqO-zyCQHg&s', 1],
            [14, 'Nissan Urvan', 'Van', '3400.00', '2.5L Diesel', 'Manual', 15, 'Diesel', 'https://d1hv7ee95zft1i.cloudfront.net/custom/car-model-photo/gallery/2023-nissan-urvan-nv350-cargo-65a0ba4c9619e.jpg', 1],
            [15, 'Mercedes Benz V Class', 'Van', '5000.00', '2.0L Turbo', 'Automatic', 7, 'Diesel', 'https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcQO1-bqF3MAziDsWmrAYreePxd5mnLHRg0aYg&s', 1],
            [16, 'Ford Transit', 'Van', '3600.00', '2.2L Diesel', 'Manual', 15, 'Diesel', 'https://i0.wp.com/travelupdate.ph/wp-content/uploads/2019/12/All-New-Ford-Transit-3_LO.jpg?resize=600%2C390&ssl=1', 1],
        ];

        foreach ($vehicles as $v) {
            $stmt = mysqli_prepare($dbc, "INSERT INTO vehicles (vehicle_id, vehicle_name, category, price_per_day, engine, transmission, seats, fuel_type, image_url, is_available) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
            mysqli_stmt_bind_param($stmt, "issdssissi", $v[0], $v[1], $v[2], $v[3], $v[4], $v[5], $v[6], $v[7], $v[8], $v[9]);
            mysqli_stmt_execute($stmt);
        }
        $success[] = "Seeded vehicles table with default fleet data.";
    } else {
        $success[] = "Vehicles table already has data. Skipping seed.";
    }
} catch (mysqli_sql_exception $e) {
    $errors[] = "Failed seeding vehicles: " . $e->getMessage();
}

// Seed admin account
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
