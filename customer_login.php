<?php
session_start();
require_once('mysql_connect.php');

$error = '';
$login_attempted = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $login_attempted = true;
    $email    = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    if (empty($email) || empty($password)) {
        $error = 'Email and password are required.';
    } else {
        $email_safe = mysqli_real_escape_string($dbc, $email);
        $query  = "SELECT customer_id, full_name, password_hash FROM customers WHERE email = '$email_safe' LIMIT 1";
        $result = mysqli_query($dbc, $query);

        if ($result && mysqli_num_rows($result) > 0) {
            $customer = mysqli_fetch_assoc($result);
            if (password_verify($password, $customer['password_hash'])) {
                $_SESSION['customer_id']    = $customer['customer_id'];
                $_SESSION['customer_email'] = $email;
                $_SESSION['customer_name']  = $customer['full_name'];
                mysqli_close($dbc);
                header('Location: index.html');
                exit;
            } else {
                $error = 'Invalid email or password.';
            }
        } else {
            $error = 'Invalid email or password.';
        }
    }
}

mysqli_close($dbc);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Customer Login – CYRI DRIVE CO.</title>
    <meta name="description" content="Login to your CYRI DRIVE CO account to book your next ride.">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600;700;800&display=swap" rel="stylesheet">
    <style>
        *, *::before, *::after { margin: 0; padding: 0; box-sizing: border-box; }

        body {
            font-family: 'Poppins', sans-serif;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
            background: url('https://images.unsplash.com/photo-1503376780353-7e6692767b70') no-repeat center center / cover;
            position: relative;
        }

        body::before {
            content: '';
            position: fixed;
            inset: 0;
            background: rgba(0, 0, 0, 0.80);
            z-index: 0;
        }

        body::after {
            content: '';
            position: fixed;
            width: 420px;
            height: 420px;
            background: radial-gradient(circle, rgba(255,30,30,0.18) 0%, transparent 70%);
            border-radius: 50%;
            top: -80px;
            right: -80px;
            z-index: 0;
            animation: pulse 4s ease-in-out infinite;
        }

        @keyframes pulse {
            0%, 100% { transform: scale(1); opacity: 0.7; }
            50%       { transform: scale(1.15); opacity: 1; }
        }

        @keyframes slideUp {
            from { opacity: 0; transform: translateY(30px); }
            to   { opacity: 1; transform: translateY(0); }
        }

        .wrapper {
            position: relative;
            z-index: 1;
            width: 100%;
            max-width: 440px;
            animation: slideUp 0.6s ease both;
        }

        .brand {
            text-align: center;
            margin-bottom: 24px;
        }

        .brand-name {
            font-size: 22px;
            font-weight: 800;
            color: white;
            letter-spacing: 2px;
            text-shadow: 0 0 20px rgba(255,30,30,0.7);
        }

        .brand-name span {
            color: #ff1e1e;
        }

        .brand-tagline {
            font-size: 12px;
            color: rgba(255,255,255,0.5);
            letter-spacing: 1px;
            margin-top: 3px;
        }

        .card {
            background: rgba(15, 15, 20, 0.85);
            backdrop-filter: blur(20px);
            border: 1px solid rgba(255, 255, 255, 0.08);
            border-radius: 24px;
            padding: 44px 40px;
            box-shadow: 0 30px 80px rgba(0, 0, 0, 0.6), 0 0 0 1px rgba(255,30,30,0.06);
        }

        .card-title {
            font-size: 26px;
            font-weight: 800;
            color: white;
            margin-bottom: 6px;
        }

        .card-sub {
            font-size: 13px;
            color: rgba(255,255,255,0.45);
            margin-bottom: 30px;
        }

        .alert-error {
            display: flex;
            align-items: center;
            gap: 10px;
            background: rgba(255, 30, 30, 0.12);
            border: 1px solid rgba(255, 30, 30, 0.35);
            color: #ff8080;
            padding: 12px 16px;
            border-radius: 10px;
            margin-bottom: 22px;
            font-size: 13.5px;
            font-weight: 500;
        }

        .alert-error .icon { font-size: 18px; flex-shrink: 0; }

        .form-group {
            margin-bottom: 18px;
        }

        label {
            display: block;
            font-size: 12.5px;
            font-weight: 600;
            color: rgba(255,255,255,0.55);
            text-transform: uppercase;
            letter-spacing: 0.8px;
            margin-bottom: 7px;
        }

        input {
            width: 100%;
            padding: 13px 16px;
            background: rgba(255,255,255,0.06);
            border: 1px solid rgba(255,255,255,0.12);
            border-radius: 10px;
            font-size: 14.5px;
            font-family: 'Poppins', sans-serif;
            color: white;
            transition: 0.25s;
        }

        input::placeholder { color: rgba(255,255,255,0.25); }

        input:focus {
            outline: none;
            border-color: rgba(255, 30, 30, 0.7);
            background: rgba(255,255,255,0.09);
            box-shadow: 0 0 0 3px rgba(255, 30, 30, 0.12);
        }

        .btn-submit {
            width: 100%;
            padding: 14px;
            background: linear-gradient(45deg, #ff1e1e, #c40000);
            color: white;
            border: none;
            border-radius: 10px;
            font-size: 15px;
            font-weight: 700;
            cursor: pointer;
            transition: 0.3s;
            font-family: 'Poppins', sans-serif;
            margin-top: 6px;
            letter-spacing: 0.4px;
            box-shadow: 0 6px 20px rgba(255, 30, 30, 0.35);
        }

        .btn-submit:hover {
            transform: translateY(-2px);
            box-shadow: 0 12px 30px rgba(255, 30, 30, 0.5);
        }

        .btn-submit:active { transform: translateY(0); }

        .divider {
            display: flex;
            align-items: center;
            gap: 12px;
            margin: 24px 0;
        }

        .divider::before, .divider::after {
            content: '';
            flex: 1;
            height: 1px;
            background: rgba(255,255,255,0.1);
        }

        .divider span {
            font-size: 12px;
            color: rgba(255,255,255,0.3);
            white-space: nowrap;
        }

        .footer-links {
            text-align: center;
            font-size: 13.5px;
            color: rgba(255,255,255,0.4);
        }

        .footer-links a {
            color: #ff5555;
            text-decoration: none;
            font-weight: 600;
            transition: 0.2s;
        }

        .footer-links a:hover { color: #ff1e1e; text-decoration: underline; }

        .back-link {
            text-align: center;
            margin-top: 20px;
        }

        .back-link a {
            font-size: 13px;
            color: rgba(255,255,255,0.3);
            text-decoration: none;
            transition: 0.2s;
        }

        .back-link a:hover { color: rgba(255,255,255,0.6); }

        @media (max-width: 480px) {
            .card { padding: 32px 24px; }
        }
    </style>
</head>
<body>

<div class="wrapper">
    <div class="brand">
        <div class="brand-name">CYRI <span>DRIVE</span> CO.</div>
        <div class="brand-tagline">Drive in Comfort · Travel in Style</div>
    </div>

    <div class="card">
        <div class="card-title">Welcome back 👋</div>
        <div class="card-sub">Sign in to your account to continue</div>

        <?php if ($error && $login_attempted): ?>
            <div class="alert-error">
                <span class="icon">⛔</span>
                <span><?= htmlspecialchars($error) ?></span>
            </div>
        <?php endif; ?>

        <form action="" method="POST" autocomplete="on">
            <div class="form-group">
                <label for="email">Email Address</label>
                <input type="email" id="email" name="email"
                       placeholder="your@email.com"
                       value="<?= htmlspecialchars($_POST['email'] ?? '') ?>"
                       required autofocus>
            </div>

            <div class="form-group">
                <label for="password">Password</label>
                <input type="password" id="password" name="password"
                       placeholder="Enter your password"
                       required>
            </div>

            <button type="submit" class="btn-submit">Sign In →</button>
        </form>

        <div class="divider"><span>New to CYRI DRIVE?</span></div>

        <div class="footer-links">
            Don't have an account? <a href="customer_register.php">Create one free</a>
        </div>
    </div>

    <div class="back-link">
        <a href="index.html">← Back to Main Page</a>
    </div>
</div>

</body>
</html>
