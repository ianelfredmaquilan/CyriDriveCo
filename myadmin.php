<?php
session_start();
if (!isset($_SESSION['admin_id'])) {
    header('Location: admin_login.php');
    exit;
}
require_once('mysql_connect.php');

$message = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_booking_id'])) {
    $delete_booking_id = intval($_POST['delete_booking_id']);

    if ($delete_booking_id <= 0) {
        $error = 'Invalid booking selected.';
    } else {
        $booking_id_safe = mysqli_real_escape_string($dbc, $delete_booking_id);
        mysqli_begin_transaction($dbc);

        mysqli_query($dbc, "DELETE FROM payments WHERE booking_id = '$booking_id_safe'");
        $delete_booking = mysqli_query($dbc, "DELETE FROM bookings WHERE booking_id = '$booking_id_safe'");

        if ($delete_booking) {
            mysqli_commit($dbc);
            $message = 'Booking #' . str_pad($delete_booking_id, 5, '0', STR_PAD_LEFT) . ' deleted successfully.';
        } else {
            mysqli_rollback($dbc);
            $error = 'Unable to delete the booking. Please try again.';
        }
    }
}

$bookings_query = "SELECT 
    b.booking_id,
    c.full_name as customer_name,
    c.phone,
    c.email,
    v.vehicle_name,
    b.number_of_days,
    b.price_per_day,
    b.total_amount,
    b.booking_status,
    p.payment_status,
    p.payment_method
FROM bookings b
LEFT JOIN customers c ON b.customer_id = c.customer_id
LEFT JOIN vehicles v ON b.vehicle_id = v.vehicle_id
LEFT JOIN payments p ON b.booking_id = p.booking_id
ORDER BY b.booking_id DESC";

$bookings_result = mysqli_query($dbc, $bookings_query);
$bookings = [];

if ($bookings_result) {
    while ($row = mysqli_fetch_assoc($bookings_result)) {
        $bookings[] = $row;
    }
}

$rentals_query = "SELECT id AS rental_id, customer_name, car, price_per_day, days, total_price, booking_date FROM rentals ORDER BY booking_date DESC";
$rentals_result = mysqli_query($dbc, $rentals_query);
$rentals = [];

if ($rentals_result) {
    while ($row = mysqli_fetch_assoc($rentals_result)) {
        $rentals[] = $row;
    }
}

$customers_query = "SELECT * FROM customers ORDER BY customer_id DESC";
$customers_result = mysqli_query($dbc, $customers_query);
$customers = [];

if ($customers_result) {
    while ($row = mysqli_fetch_assoc($customers_result)) {
        $customers[] = $row;
    }
}

$vehicles_query = "SELECT * FROM vehicles ORDER BY vehicle_id DESC";
$vehicles_result = mysqli_query($dbc, $vehicles_query);
$vehicles = [];

if ($vehicles_result) {
    while ($row = mysqli_fetch_assoc($vehicles_result)) {
        $vehicles[] = $row;
    }
}

$stats_query = "SELECT 
    COUNT(DISTINCT b.booking_id) as total_bookings,
    COUNT(DISTINCT c.customer_id) as total_customers,
    COUNT(DISTINCT v.vehicle_id) as total_vehicles,
    SUM(b.total_amount) as total_revenue,
    AVG(b.total_amount) as avg_booking_value
FROM bookings b
LEFT JOIN customers c ON b.customer_id = c.customer_id
LEFT JOIN vehicles v ON b.vehicle_id = v.vehicle_id";

$stats_result = mysqli_query($dbc, $stats_query);
$stats = mysqli_fetch_assoc($stats_result);

mysqli_close($dbc);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - CYRI DRIVE CO</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600;700;800&display=swap" rel="stylesheet">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Poppins', sans-serif;
            background: linear-gradient(180deg, #111 0%, #080808 45%, #1f1f1f 100%);
            min-height: 100vh;
            padding: 20px;
            color: white;
        }

        .container {
            max-width: 1400px;
            margin: 0 auto;
        }

        .header {
            background: rgba(20, 20, 20, 0.92);
            padding: 30px;
            border-radius: 15px;
            margin-bottom: 30px;
            box-shadow: 0 10px 40px rgba(0,0,0,0.5);
            border: 1px solid rgba(255,255,255,0.08);
        }

        .header h1 {
            color: #ffb3b3;
            font-size: 32px;
            margin-bottom: 10px;
        }

        .header p {
            color: #d9d9d9;
            font-size: 14px;
        }

        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }

        .stat-card {
            background: rgba(255,255,255,0.06);
            padding: 25px;
            border-radius: 12px;
            box-shadow: 0 5px 20px rgba(0,0,0,0.25);
            text-align: center;
            border-left: 5px solid #ff1e1e;
            backdrop-filter: blur(4px);
        }

        .stat-card h3 {
            color: #f0f0f0;
            font-size: 13px;
            text-transform: uppercase;
            margin-bottom: 10px;
            letter-spacing: 1px;
        }

        .stat-card .value {
            color: #ffffff;
            font-size: 32px;
            font-weight: 800;
        }

        .section {
            background: rgba(15, 15, 15, 0.94);
            padding: 30px;
            border-radius: 15px;
            margin-bottom: 30px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.5);
            border: 1px solid rgba(255,255,255,0.08);
        }

        .section h2 {
            color: #ffb3b3;
            font-size: 22px;
            margin-bottom: 20px;
            border-bottom: 3px solid #ff1e1e;
            padding-bottom: 15px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 15px;
        }

        th {
            background: rgba(255,255,255,0.06);
            color: #f5f5f5;
            padding: 15px;
            text-align: left;
            font-weight: 700;
            border-bottom: 2px solid rgba(255,255,255,0.12);
            font-size: 13px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        td {
            padding: 15px;
            border-bottom: 1px solid rgba(255,255,255,0.08);
            color: #e5e7eb;
            font-size: 14px;
        }

        tr:hover {
            background: rgba(255,255,255,0.06);
        }

        .status {
            display: inline-block;
            padding: 6px 12px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 600;
        }

        .status.confirmed {
            background: #d4edda;
            color: #155724;
        }

        .status.paid {
            background: #d4edda;
            color: #155724;
        }

        .status.pending {
            background: #fff3cd;
            color: #856404;
        }

        .status.available {
            background: #d4edda;
            color: #155724;
        }

        .status.unavailable {
            background: #f8d7da;
            color: #721c24;
        }

        .back-btn {
            display: inline-block;
            margin-bottom: 20px;
            padding: 12px 25px;
            background: linear-gradient(45deg,#ff1e1e,#c40000);
            color: white;
            text-decoration: none;
            border-radius: 8px;
            font-weight: 600;
            transition: 0.3s;
            border: none;
            cursor: pointer;
            font-size: 14px;
        }

        .back-btn:hover {
            background: linear-gradient(45deg,#c40000,#ff1e1e);
            transform: translateY(-2px);
        }

        .action-row {
            display: inline-flex;
            flex-wrap: wrap;
            gap: 8px;
            align-items: center;
        }

        .action-btn {
            padding: 6px 12px;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            font-size: 11px;
            transition: 0.2s;
            font-weight: 700;
        }

        .copy-btn {
            background: rgba(255, 30, 30, 0.18);
            color: #fff;
        }

        .copy-btn:hover {
            background: rgba(255, 30, 30, 0.28);
        }

        .danger-btn {
            background: #a70000;
            color: white;
        }

        .danger-btn:hover {
            background: #d11f1f;
        }

        .alert-success,
        .alert-error {
            padding: 14px 18px;
            border-radius: 12px;
            margin-bottom: 24px;
            font-size: 14px;
            line-height: 1.5;
            box-shadow: 0 8px 24px rgba(0,0,0,0.18);
        }

        .alert-success {
            background: rgba(45, 120, 60, 0.18);
            border: 1px solid rgba(45, 120, 60, 0.35);
            color: #d9f0d9;
        }

        .alert-error {
            background: rgba(160, 25, 25, 0.18);
            border: 1px solid rgba(255, 60, 60, 0.35);
            color: #ffd6d6;
        }

        .no-data {
            text-align: center;
            color: #bbb;
            padding: 40px 20px;
            font-size: 15px;
        }

        .price {
            color: #ff8a7a;
            font-weight: 700;
        }
    </style>
</head>
<body>
    <div class="container">
        <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:20px; flex-wrap:wrap; gap:10px;">
            <a href="index.html" class="back-btn">← Back to Main</a>
            <div style="font-size:14px; color:#aaa;">
                Logged in as: <strong style="color:#ffb3b3;"><?= htmlspecialchars($_SESSION['admin_username']) ?></strong>
                <a href="admin_logout.php" class="back-btn" style="margin-left:15px; display:inline-block;">Logout</a>
            </div>
        </div>

        <div class="header">
            <h1>📊 Admin Dashboard</h1>
            <p>Manage and monitor all bookings, customers, and vehicles</p>
        </div>

        <?php if ($message): ?>
            <div class="alert-success"><?= htmlspecialchars($message) ?></div>
        <?php endif; ?>

        <?php if ($error): ?>
            <div class="alert-error"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>

        <div class="stats-grid">
            <div class="stat-card">
                <h3>Total Bookings</h3>
                <div class="value"><?= $stats['total_bookings'] ?? 0 ?></div>
            </div>
            <div class="stat-card">
                <h3>Total Customers</h3>
                <div class="value"><?= $stats['total_customers'] ?? 0 ?></div>
            </div>
            <div class="stat-card">
                <h3>Total Vehicles</h3>
                <div class="value"><?= $stats['total_vehicles'] ?? 0 ?></div>
            </div>
            <div class="stat-card">
                <h3>Total Revenue</h3>
                <div class="value price">₱<?= number_format($stats['total_revenue'] ?? 0) ?></div>
            </div>
            <div class="stat-card">
                <h3>Avg. Booking</h3>
                <div class="value price">₱<?= number_format($stats['avg_booking_value'] ?? 0) ?></div>
            </div>
        </div>

        <div class="section">
            <h2>📋 All Bookings History</h2>
            <?php if (count($bookings) > 0): ?>
                <table>
                    <thead>
                        <tr>
                            <th>Booking ID</th>
                            <th>Customer Name</th>
                            <th>Vehicle</th>
                            <th>Days</th>
                            <th>Total Amount</th>
                            <th>Status</th>
                            <th>Payment</th>
                            <th>Date</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($bookings as $booking): ?>
                            <tr>
                                <td>#<?= str_pad($booking['booking_id'], 5, '0', STR_PAD_LEFT) ?></td>
                                <td><?= htmlspecialchars($booking['customer_name']) ?></td>
                                <td><?= htmlspecialchars($booking['vehicle_name']) ?></td>
                                <td><?= $booking['number_of_days'] ?></td>
                                <td class="price">₱<?= number_format($booking['total_amount']) ?></td>
                                <td><span class="status confirmed"><?= $booking['booking_status'] ?></span></td>
                                <td><span class="status <?= strtolower($booking['payment_status'] ?? 'pending') ?>"><?= $booking['payment_status'] ?? 'Pending' ?></span></td>
                                <td><?= isset($booking['created_at']) && $booking['created_at'] ? date('M d, Y', strtotime($booking['created_at'])) : 'N/A' ?></td>
                                <td>
                                    <div class="action-row">
                                        <button type="button" class="action-btn copy-btn" onclick="copyBookingSummary('<?= str_pad($booking['booking_id'], 5, '0', STR_PAD_LEFT) ?>', '<?= addslashes($booking['customer_name']) ?>', '<?= addslashes($booking['vehicle_name']) ?>', '<?= $booking['number_of_days'] ?>', '<?= number_format($booking['total_amount']) ?>', '<?= addslashes($booking['payment_method'] ?? 'N/A') ?>', '<?= addslashes($booking['phone'] ?? '') ?>')">📋 Copy</button>
                                        <form method="POST" style="display:inline-block; margin:0;">
                                            <input type="hidden" name="delete_booking_id" value="<?= $booking['booking_id'] ?>">
                                            <button type="submit" class="action-btn danger-btn" onclick="return confirm('Delete booking #<?= str_pad($booking['booking_id'], 5, '0', STR_PAD_LEFT) ?>? This cannot be undone.')">🗑️ Delete</button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php else: ?>
                <div class="no-data">No bookings found in the database.</div>
            <?php endif; ?>
        </div>

        <div class="section">
            <h2>🚗 Rental Records</h2>
            <?php if (count($rentals) > 0): ?>
                <table>
                    <thead>
                        <tr>
                            <th>Rental ID</th>
                            <th>Customer Name</th>
                            <th>Car</th>
                            <th>Days</th>
                            <th>Price/Day</th>
                            <th>Total Price</th>
                            <th>Booking Date</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($rentals as $rental): ?>
                            <tr>
                                <td>#<?= str_pad($rental['rental_id'], 5, '0', STR_PAD_LEFT) ?></td>
                                <td><?= htmlspecialchars($rental['customer_name']) ?></td>
                                <td><?= htmlspecialchars($rental['car']) ?></td>
                                <td><?= $rental['days'] ?></td>
                                <td class="price">₱<?= number_format($rental['price_per_day']) ?></td>
                                <td class="price">₱<?= number_format($rental['total_price']) ?></td>
                                <td><?= date('M d, Y H:i', strtotime($rental['booking_date'])) ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php else: ?>
                <div class="no-data">No rental records found.</div>
            <?php endif; ?>
        </div>

        <div class="section">
            <h2>👥 All Customers</h2>
            <?php if (count($customers) > 0): ?>
                <table>
                    <thead>
                        <tr>
                            <th>Customer ID</th>
                            <th>Full Name</th>
                            <th>Phone</th>
                            <th>Email</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($customers as $customer): ?>
                            <tr>
                                <td>#<?= $customer['customer_id'] ?></td>
                                <td><?= htmlspecialchars($customer['full_name']) ?></td>
                                <td><?= htmlspecialchars($customer['phone']) ?></td>
                                <td><?= htmlspecialchars($customer['email']) ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php else: ?>
                <div class="no-data">No customers found.</div>
            <?php endif; ?>
        </div>

        <div class="section">
            <h2>🚙 Vehicle Fleet</h2>
            <?php if (count($vehicles) > 0): ?>
                <table>
                    <thead>
                        <tr>
                            <th>Vehicle ID</th>
                            <th>Vehicle Name</th>
                            <th>Price/Day</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($vehicles as $vehicle): ?>
                            <tr>
                                <td>#<?= $vehicle['vehicle_id'] ?></td>
                                <td><?= htmlspecialchars($vehicle['vehicle_name']) ?></td>
                                <td class="price">₱<?= number_format($vehicle['price_per_day']) ?></td>
                                <td>
                                    <span class="status <?= ($vehicle['is_available'] ? 'available' : 'unavailable') ?>">
                                        <?= ($vehicle['is_available'] ? 'Available' : 'Unavailable') ?>
                                    </span>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php else: ?>
                <div class="no-data">No vehicles found.</div>
            <?php endif; ?>
        </div>

        <div style="display:flex; justify-content:space-between; align-items:center; flex-wrap:wrap; gap:10px; margin-top:40px;">
            <a href="index.html" class="back-btn">← Back to Main</a>
            <a href="admin_logout.php" class="back-btn">Logout</a>
        </div>
    </div>

    <script>
    function copyBookingSummary(ref, name, car, days, total, method, phone) {
        const text = `🚗 CYRI DRIVE CO. Booking Summary 🚗\n---------------------------------------\nBooking Reference: #${ref}\nCustomer Name: ${name}\nPhone: ${phone}\nSelected Car: ${car}\nNumber of Days: ${days}\nTotal Payment: ₱${total}\nPayment Method: ${method}\nStatus: Confirmed ✓\n---------------------------------------\nThank you for choosing CYRI DRIVE CO!`;
        
        navigator.clipboard.writeText(text).then(() => {
            alert("Booking summary copied to clipboard! You can now paste and send it to your client.");
        }).catch(err => {
            alert("Failed to copy. Please write down the details manually.");
        });
    }
    </script>
</body>
</html>
