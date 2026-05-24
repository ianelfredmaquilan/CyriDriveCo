<?php
session_start();

if (!isset($_SESSION['customer_id'])) {
    header('Location: customer_login.php');
    exit;
}

require_once('mysql_connect.php');
require_once('config_email.php');

$name  = mysqli_real_escape_string($dbc, trim($_SESSION['customer_name']));
$phone = mysqli_real_escape_string($dbc, trim($_POST['phone']));
$email = mysqli_real_escape_string($dbc, trim($_POST['email']));
$car   = mysqli_real_escape_string($dbc, $_POST['car']);
$price = (float) $_POST['price'];
$start_date = $_POST['start_date'];
$end_date = $_POST['end_date'];
$pickup_time = $_POST['pickup_time'] ?? '09:00';
$return_time = $_POST['return_time'] ?? '17:00';

$error   = '';
$success = false;
$booking_id = null;
$email_sent = false;
$date_conflict = false;

if ($start_date && $end_date) {
    $start = new DateTime($start_date);
    $end = new DateTime($end_date);
    $interval = $start->diff($end);
    $days = $interval->days;

    if ($days <= 0) {
        $error = 'End date must be after start date.';
    }
} else {
    $error = 'Start and end dates are required.';
}

if (empty($error)) {
    $vehicle_result = mysqli_query($dbc, "SELECT vehicle_id FROM vehicles WHERE vehicle_name='$car' LIMIT 1");
    if (mysqli_num_rows($vehicle_result) > 0) {
        $vehicle = mysqli_fetch_assoc($vehicle_result);
        $vehicle_id = $vehicle['vehicle_id'];
    } else {
        mysqli_query($dbc, "INSERT INTO vehicles (vehicle_name, price_per_day, is_available) VALUES ('$car', '$price', 1)");
        $vehicle_id = mysqli_insert_id($dbc);
    }

    $conflict_query = "SELECT COUNT(*) as conflict_count FROM bookings
                       WHERE vehicle_id = '$vehicle_id'
                       AND booking_status = 'Confirmed'
                       AND (
                           (start_date <= '$end_date' AND end_date >= '$start_date')
                       )";
    $conflict_result = mysqli_query($dbc, $conflict_query);
    $conflict_row = mysqli_fetch_assoc($conflict_result);

    if ($conflict_row['conflict_count'] > 0) {
        $date_conflict = true;
    }

    $total = $price * ($days + 1);

    $booking_query = "INSERT INTO bookings (customer_id, vehicle_id, number_of_days, price_per_day, total_amount, booking_status, start_date, end_date, pickup_time, return_time)
                      VALUES ('{$_SESSION['customer_id']}', '$vehicle_id', '$days', '$price', '$total', 'Confirmed', '$start_date', '$end_date', '$pickup_time', '$return_time')";
    $booking_result = mysqli_query($dbc, $booking_query);

    if ($booking_result) {
        $booking_id = mysqli_insert_id($dbc);

        mysqli_query($dbc, "INSERT INTO payments (booking_id, payment_method, amount_paid, payment_status)
                            VALUES ('$booking_id', '{$_POST['payment_method']}', '$total', 'Paid')");

        mysqli_query($dbc, "INSERT INTO rentals (customer_name, car, price_per_day, days, total_price, booking_date)
                            VALUES ('$name', '$car', '$price', '$days', '$total', NOW())");

        $success = true;

        if (!empty($email)) {
            $email_sent = send_booking_confirmation($name, $email, $car, $days, $price, $total, $_POST['payment_method'], $booking_id, $start_date, $end_date, $pickup_time, $return_time);
        }
    } else {
        $error = mysqli_error($dbc);
    }
}

mysqli_close($dbc);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Booking Confirmation - CYRI DRIVE CO.</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600;700;800&display=swap" rel="stylesheet">
    <style>
        * { margin:0; padding:0; box-sizing:border-box; }
        body {
            font-family:'Poppins',sans-serif;
            min-height:100vh;
            display:flex;
            justify-content:center;
            align-items:center;
            background:url('https://images.unsplash.com/photo-1503376780353-7e6692767b70') no-repeat center center/cover;
            color:white;
        }
        body::before {
            content:"";
            position:fixed;
            inset:0;
            background:rgba(0,0,0,0.75);
            z-index:0;
        }
        .box {
            position:relative;
            z-index:1;
            background:rgba(255,255,255,0.97);
            color:black;
            padding:40px 30px;
            border-radius:20px;
            width:90%;
            max-width:620px;
            text-align:center;
            box-shadow:0 10px 30px rgba(0,0,0,0.5);
        }
        .icon { font-size:70px; margin-bottom:15px; }
        h2 { font-size:30px; font-weight:800; margin-bottom:10px; }
        p { font-size:15px; color:#555; margin-bottom:10px; }
        .receipt {
            margin:20px 0;
            font-size:16px;
            line-height:2.1;
            color:#111;
            text-align:left;
            background:#f9f9f9;
            padding:18px 22px;
            border-radius:12px;
            border-left:4px solid #ff1e1e;
        }
        .badge {
            display:inline-block;
            background:#e6ffe6;
            color:#1a7a1a;
            border-radius:20px;
            padding:3px 14px;
            font-size:13px;
            font-weight:600;
            margin-left:8px;
        }
        .booking-id {
            font-size:13px;
            color:#888;
            margin-top:5px;
        }
        a {
            display:inline-block;
            margin-top:15px;
            padding:14px 30px;
            background:linear-gradient(45deg,#ff1e1e,#b80000);
            color:white;
            text-decoration:none;
            border-radius:12px;
            font-weight:700;
            font-size:16px;
            transition:0.3s;
        }
        a:hover { transform:scale(1.04); box-shadow:0 0 18px rgba(255,0,0,0.4); }
        .error { color:#cc0000; font-size:15px; margin-top:10px; background:#fff0f0; padding:10px; border-radius:8px; }
        .warning { color:#856404; background:#fff3cd; padding:12px; border-radius:8px; margin-bottom:15px; border-left:4px solid #ffc107; }
        .email-status { margin-top:15px; padding:10px; border-radius:8px; font-size:14px; }
        .email-sent { background:#d4edda; color:#155724; }
        .email-failed { background:#f8d7da; color:#721c24; }
    </style>
</head>
<body>
<div class="box">
    <?php if ($success): ?>
        <div class="icon">✅</div>
        <h2>Booking Confirmed!</h2>
        <p>Your reservation has been saved to our database.</p>
        <?php if ($date_conflict): ?>
            <div class="warning">
                ⚠ <strong>Note:</strong> Other bookings overlap with your selected dates. Your booking is still confirmed.
            </div>
        <?php endif; ?>
        <?php if ($email_sent): ?>
            <div class="email-status email-sent">
                ✓ Confirmation email sent to <strong><?= htmlspecialchars($email) ?></strong>
            </div>
        <?php elseif (!empty($email)): ?>
            <div class="email-status email-failed">
                ⚠ We could not send the confirmation email. Please save your details.
            </div>
        <?php endif; ?>
        <?php if ($booking_id): ?>
            <p class="booking-id">Booking Reference #<?= str_pad($booking_id, 5, '0', STR_PAD_LEFT) ?></p>
        <?php endif; ?>
        <div class="receipt">
            <b>Customer Name:</b> <?= htmlspecialchars($name) ?><br>
            <?php if ($phone): ?><b>Phone:</b> <?= htmlspecialchars($phone) ?><br><?php endif; ?>
            <?php if ($email): ?><b>Email:</b> <?= htmlspecialchars($email) ?><br><?php endif; ?>
            <b>Selected Car:</b> <?= htmlspecialchars($car) ?><br>
            <b>Start Date:</b> <?= date('M d, Y', strtotime($start_date)) ?><br>
            <b>End Date:</b> <?= date('M d, Y', strtotime($end_date)) ?><br>
            <b>Pickup Time:</b> <?= htmlspecialchars($pickup_time) ?><br>
            <b>Return Time:</b> <?= htmlspecialchars($return_time) ?><br>
            <b>Price Per Day:</b> ₱<?= number_format($price) ?><br>
            <b>Number of Days:</b> <?= $days ?><br>
            <b>Total Payment:</b> ₱<?= number_format($total) ?><br>
            <b>Payment Method:</b> <?= htmlspecialchars($_POST['payment_method']) ?><br>
            <b>Status:</b> <span class="badge">Confirmed ✓</span>
        </div>
        
        <button onclick="copySummary()" style="display:inline-block; margin-bottom:15px; padding:12px 20px; background:#ffc107; color:#212529; border:none; border-radius:10px; font-weight:700; cursor:pointer; font-size:14.5px; transition:0.3s; width: 100%;">📋 Copy Booking Summary (for SMS/Messenger)</button>
        
        <script>
        function copySummary() {
            const text = `🚗 CYRI DRIVE CO. Booking Confirmation 🚗\n---------------------------------------\nBooking Reference: #<?= str_pad($booking_id, 5, '0', STR_PAD_LEFT) ?>\nCustomer Name: <?= htmlspecialchars($name) ?>\n<?php if ($phone): ?>Phone: <?= htmlspecialchars($phone) ?>\n<?php endif; ?>Selected Car: <?= htmlspecialchars($car) ?>\nStart Date: <?= date('M d, Y', strtotime($start_date)) ?>\nEnd Date: <?= date('M d, Y', strtotime($end_date)) ?>\nPickup Time: <?= htmlspecialchars($pickup_time) ?>\nReturn Time: <?= htmlspecialchars($return_time) ?>\nNumber of Days: <?= $days ?>\nPrice Per Day: ₱<?= number_format($price) ?>\nTotal Payment: ₱<?= number_format($total) ?>\nPayment Method: <?= htmlspecialchars($_POST['payment_method']) ?>\nStatus: Confirmed ✓\n---------------------------------------\nThank you for choosing CYRI DRIVE CO!`;
            
            navigator.clipboard.writeText(text).then(() => {
                alert("Booking summary copied to clipboard! You can now paste and send it to your client.");
            }).catch(err => {
                alert("Failed to copy automatically. Please copy the receipt on the screen.");
            });
        }
        </script>
        
        <a href="index.html">Book Another Car</a>
    <?php else: ?>
        <div class="icon">❌</div>
        <h2>Booking Failed</h2>
        <p>Something went wrong while saving your booking.</p>
        <p class="error"><?= htmlspecialchars($error) ?></p>
        <a href="javascript:history.back()">Go Back</a>
    <?php endif; ?>
</div>
</body>
</html>
