<?php
session_start();
require_once('mysql_connect.php');

$error   = '';
$success = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name             = trim($_POST['name'] ?? '');
    $email            = trim($_POST['email'] ?? '');
    $phone            = trim($_POST['phone'] ?? '');
    $password         = $_POST['password'] ?? '';
    $password_confirm = $_POST['password_confirm'] ?? '';

    if (empty($name) || empty($email) || empty($phone) || empty($password)) {
        $error = 'All fields are required.';
    } elseif (strlen($password) < 8) {
        $error = 'Password must be at least 8 characters long.';
    } elseif ($password !== $password_confirm) {
        $error = 'Passwords do not match.';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = 'Please enter a valid email address.';
    } else {
        $email_safe = mysqli_real_escape_string($dbc, $email);
        $check      = mysqli_query($dbc, "SELECT customer_id FROM customers WHERE email = '$email_safe' LIMIT 1");

        if (mysqli_num_rows($check) > 0) {
            $error = 'Email address is already registered. Please login instead.';
        } else {
            $password_hash = password_hash($password, PASSWORD_BCRYPT);
            $name_safe     = mysqli_real_escape_string($dbc, $name);
            $phone_safe    = mysqli_real_escape_string($dbc, $phone);

            $insert = "INSERT INTO customers (full_name, email, phone, password_hash)
                       VALUES ('$name_safe', '$email_safe', '$phone_safe', '$password_hash')";

            if (mysqli_query($dbc, $insert)) {
                $success = true;
                $_SESSION['registration_success'] = true;
                $_SESSION['registration_email']   = $email;
            } else {
                $error = 'Registration failed. Please try again.';
            }
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
    <title>Create Account – CYRI DRIVE CO.</title>
    <meta name="description" content="Register a new CYRI DRIVE CO account and start booking your next ride.">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600;700;800&display=swap" rel="stylesheet">
    <style>
        *, *::before, *::after { margin: 0; padding: 0; box-sizing: border-box; }

        body {
            font-family: 'Poppins', sans-serif;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 24px 20px;
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
            width: 500px;
            height: 500px;
            background: radial-gradient(circle, rgba(255,30,30,0.15) 0%, transparent 70%);
            border-radius: 50%;
            bottom: -100px;
            left: -100px;
            z-index: 0;
            animation: pulse 5s ease-in-out infinite;
        }

        @keyframes pulse {
            0%, 100% { transform: scale(1); opacity: 0.7; }
            50%       { transform: scale(1.1); opacity: 1; }
        }

        @keyframes slideUp {
            from { opacity: 0; transform: translateY(30px); }
            to   { opacity: 1; transform: translateY(0); }
        }

        .wrapper {
            position: relative;
            z-index: 1;
            width: 100%;
            max-width: 480px;
            animation: slideUp 0.6s ease both;
        }

        .brand {
            text-align: center;
            margin-bottom: 22px;
        }

        .brand-name {
            font-size: 22px;
            font-weight: 800;
            color: white;
            letter-spacing: 2px;
            text-shadow: 0 0 20px rgba(255,30,30,0.7);
        }

        .brand-name span { color: #ff1e1e; }

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
            padding: 40px 38px;
            box-shadow: 0 30px 80px rgba(0, 0, 0, 0.6), 0 0 0 1px rgba(255,30,30,0.06);
        }

        .card-title { font-size: 24px; font-weight: 800; color: white; margin-bottom: 6px; }
        .card-sub   { font-size: 13px; color: rgba(255,255,255,0.4); margin-bottom: 28px; }

        .success-card {
            text-align: center;
            padding: 30px 0;
        }

        .success-card .icon   { font-size: 64px; margin-bottom: 16px; }
        .success-card h2      { font-size: 22px; font-weight: 800; color: white; margin-bottom: 8px; }
        .success-card p       { font-size: 14px; color: rgba(255,255,255,0.55); line-height: 1.7; }
        .success-card a       { color: #ff5555; font-weight: 600; text-decoration: none; }
        .success-card a:hover { text-decoration: underline; }

        .alert-error {
            display: flex;
            align-items: center;
            gap: 10px;
            background: rgba(255, 30, 30, 0.12);
            border: 1px solid rgba(255, 30, 30, 0.35);
            color: #ff8080;
            padding: 12px 16px;
            border-radius: 10px;
            margin-bottom: 20px;
            font-size: 13.5px;
            font-weight: 500;
        }

        .form-row {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 14px;
        }

        .form-group { margin-bottom: 16px; }

        label {
            display: block;
            font-size: 12px;
            font-weight: 600;
            color: rgba(255,255,255,0.5);
            text-transform: uppercase;
            letter-spacing: 0.8px;
            margin-bottom: 6px;
        }

        input {
            width: 100%;
            padding: 12px 15px;
            background: rgba(255,255,255,0.06);
            border: 1px solid rgba(255,255,255,0.12);
            border-radius: 10px;
            font-size: 14px;
            font-family: 'Poppins', sans-serif;
            color: white;
            transition: 0.25s;
        }

        input::placeholder { color: rgba(255,255,255,0.22); }

        input:focus {
            outline: none;
            border-color: rgba(255, 30, 30, 0.7);
            background: rgba(255,255,255,0.09);
            box-shadow: 0 0 0 3px rgba(255, 30, 30, 0.12);
        }

        .hint {
            margin-top: 5px;
            font-size: 11px;
            color: rgba(255,255,255,0.28);
        }

        .password-req {
            background: rgba(255,30,30,0.06);
            border: 1px solid rgba(255,30,30,0.15);
            border-radius: 8px;
            padding: 10px 14px;
            font-size: 12px;
            color: rgba(255,255,255,0.4);
            line-height: 1.8;
            margin-bottom: 16px;
        }

        .password-req strong { color: rgba(255,255,255,0.6); }

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
            box-shadow: 0 6px 20px rgba(255, 30, 30, 0.35);
            letter-spacing: 0.3px;
        }

        .btn-submit:hover {
            transform: translateY(-2px);
            box-shadow: 0 12px 30px rgba(255, 30, 30, 0.5);
        }

        .divider {
            display: flex;
            align-items: center;
            gap: 12px;
            margin: 22px 0;
        }

        .divider::before, .divider::after {
            content: '';
            flex: 1;
            height: 1px;
            background: rgba(255,255,255,0.1);
        }

        .divider span { font-size: 12px; color: rgba(255,255,255,0.3); white-space: nowrap; }

        .footer-links {
            text-align: center;
            font-size: 13.5px;
            color: rgba(255,255,255,0.4);
        }

        .footer-links a {
            color: #ff5555;
            text-decoration: none;
            font-weight: 600;
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
            .card { padding: 30px 20px; }
            .form-row { grid-template-columns: 1fr; }
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
        <?php if ($success): ?>
            <div class="success-card">
                <div class="icon">🎉</div>
                <h2>Account Created!</h2>
                <p>Welcome to CYRI DRIVE CO.<br>
                   You can now login and start booking.<br><br>
                   <a href="customer_login.php">Click here to Login →</a>
                </p>
            </div>
            <script>
                setTimeout(() => { window.location.href = 'customer_login.php'; }, 2500);
            </script>
        <?php else: ?>
            <div class="card-title">Create Account 🚗</div>
            <div class="card-sub">Join CYRI DRIVE CO. and start booking your next ride</div>

            <?php if ($error): ?>
                <div class="alert-error">
                    <span>⛔</span>
                    <span><?= htmlspecialchars($error) ?></span>
                </div>
            <?php endif; ?>

            <form action="" method="POST" autocomplete="on">
                <div class="form-row">
                    <div class="form-group">
                        <label for="name">Full Name</label>
                        <input type="text" id="name" name="name"
                               placeholder="Juan Dela Cruz"
                               value="<?= htmlspecialchars($_POST['name'] ?? '') ?>"
                               required>
                    </div>
                    <div class="form-group">
                        <label for="phone">Phone Number</label>
                        <input type="tel" id="phone" name="phone"
                               placeholder="09XX-XXX-XXXX"
                               value="<?= htmlspecialchars($_POST['phone'] ?? '') ?>"
                               required>
                    </div>
                </div>

                <div class="form-group">
                    <label for="email">Email Address</label>
                    <input type="email" id="email" name="email"
                           placeholder="your@email.com"
                           value="<?= htmlspecialchars($_POST['email'] ?? '') ?>"
                           required>
                </div>

                <div class="form-group">
                    <label for="password">Password</label>
                    <input type="password" id="password" name="password"
                           placeholder="At least 8 characters" required>
                </div>

                <div class="form-group">
                    <label for="password_confirm">Confirm Password</label>
                    <input type="password" id="password_confirm" name="password_confirm"
                           placeholder="Re-enter your password" required>
                </div>

                <div class="password-req">
                    <strong>Password Requirements:</strong><br>
                    • At least 8 characters &nbsp;•&nbsp; Mix of letters &amp; numbers recommended
                </div>

                <button type="submit" class="btn-submit">Create My Account →</button>
            </form>

            <div class="divider"><span>Already a member?</span></div>

            <div class="footer-links">
                <a href="customer_login.php">Sign in to your account</a>
            </div>
        <?php endif; ?>
    </div>

    <div class="back-link">
        <a href="index.html">← Back to Main Page</a>
    </div>
</div>

</body>
</html>
