<?php
session_start();
require_once('mysql_connect.php');

$error = '';
$login_attempted = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $login_attempted = true;
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';

    if (empty($username) || empty($password)) {
        $error = 'Username and password are required.';
    } else {
        $username_safe = mysqli_real_escape_string($dbc, $username);
        $query  = "SELECT admin_id, username, password_hash FROM admins WHERE username = '$username_safe' LIMIT 1";
        $result = mysqli_query($dbc, $query);

        if ($result && mysqli_num_rows($result) > 0) {
            $admin = mysqli_fetch_assoc($result);
            if (password_verify($password, $admin['password_hash'])) {
                $_SESSION['admin_id']       = $admin['admin_id'];
                $_SESSION['admin_username'] = $admin['username'];
                mysqli_close($dbc);
                header('Location: myadmin.php');
                exit;
            } else {
                $error = 'Invalid username or password.';
            }
        } else {
            $error = 'Invalid username or password.';
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
    <title>Admin Login – CYRI DRIVE CO.</title>
    <meta name="description" content="CYRI DRIVE CO. Admin Dashboard access.">
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
            background: linear-gradient(135deg, #0a0a0f 0%, #12121a 50%, #1a0505 100%);
            position: relative;
            overflow: hidden;
        }

        body::before {
            content: '';
            position: fixed;
            inset: 0;
            background-image:
                linear-gradient(rgba(255,30,30,0.04) 1px, transparent 1px),
                linear-gradient(90deg, rgba(255,30,30,0.04) 1px, transparent 1px);
            background-size: 40px 40px;
            z-index: 0;
        }

        .glow-top-right {
            position: fixed;
            top: -120px;
            right: -120px;
            width: 500px;
            height: 500px;
            background: radial-gradient(circle, rgba(255,30,30,0.22) 0%, transparent 65%);
            border-radius: 50%;
            z-index: 0;
            animation: breathe 5s ease-in-out infinite;
        }

        .glow-bottom-left {
            position: fixed;
            bottom: -150px;
            left: -150px;
            width: 450px;
            height: 450px;
            background: radial-gradient(circle, rgba(180,0,0,0.15) 0%, transparent 65%);
            border-radius: 50%;
            z-index: 0;
            animation: breathe 5s ease-in-out infinite reverse;
        }

        @keyframes breathe {
            0%, 100% { transform: scale(1); opacity: 0.8; }
            50%       { transform: scale(1.12); opacity: 1; }
        }

        @keyframes slideUp {
            from { opacity: 0; transform: translateY(28px); }
            to   { opacity: 1; transform: translateY(0); }
        }

        .wrapper {
            position: relative;
            z-index: 1;
            width: 100%;
            max-width: 420px;
            animation: slideUp 0.55s ease both;
        }

        .admin-badge {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
            margin-bottom: 28px;
        }

        .badge-icon {
            width: 48px;
            height: 48px;
            background: linear-gradient(135deg, #ff1e1e, #7a0000);
            border-radius: 14px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 22px;
            box-shadow: 0 8px 24px rgba(255,30,30,0.4);
        }

        .badge-text .title {
            font-size: 18px;
            font-weight: 800;
            color: white;
            letter-spacing: 1.5px;
            line-height: 1;
        }

        .badge-text .sub {
            font-size: 11px;
            color: rgba(255,30,30,0.7);
            letter-spacing: 2px;
            text-transform: uppercase;
            margin-top: 3px;
        }

        .card {
            background: rgba(10, 10, 15, 0.90);
            backdrop-filter: blur(24px);
            border: 1px solid rgba(255, 30, 30, 0.15);
            border-radius: 22px;
            padding: 42px 38px;
            box-shadow:
                0 30px 80px rgba(0, 0, 0, 0.7),
                0 0 0 1px rgba(255,30,30,0.08),
                inset 0 1px 0 rgba(255,255,255,0.04);
        }

        .card-title {
            font-size: 24px;
            font-weight: 800;
            color: white;
            margin-bottom: 5px;
        }

        .card-sub {
            font-size: 13px;
            color: rgba(255,255,255,0.35);
            margin-bottom: 30px;
        }

        .access-bar {
            display: flex;
            align-items: center;
            gap: 8px;
            background: rgba(255,30,30,0.08);
            border: 1px solid rgba(255,30,30,0.2);
            border-radius: 8px;
            padding: 9px 14px;
            margin-bottom: 24px;
            font-size: 12px;
            color: rgba(255,80,80,0.85);
            font-weight: 600;
        }

        .access-bar .dot {
            width: 7px;
            height: 7px;
            border-radius: 50%;
            background: #ff1e1e;
            box-shadow: 0 0 6px rgba(255,30,30,0.8);
            animation: blink 1.5s ease-in-out infinite;
            flex-shrink: 0;
        }

        @keyframes blink {
            0%, 100% { opacity: 1; }
            50%       { opacity: 0.2; }
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
        }

        .form-group { margin-bottom: 18px; }

        label {
            display: block;
            font-size: 11.5px;
            font-weight: 600;
            color: rgba(255,255,255,0.45);
            text-transform: uppercase;
            letter-spacing: 0.9px;
            margin-bottom: 7px;
        }

        input {
            width: 100%;
            padding: 13px 16px;
            background: rgba(255,255,255,0.05);
            border: 1px solid rgba(255,255,255,0.10);
            border-radius: 10px;
            font-size: 14.5px;
            font-family: 'Poppins', sans-serif;
            color: white;
            transition: 0.25s;
        }

        input::placeholder { color: rgba(255,255,255,0.2); }

        input:focus {
            outline: none;
            border-color: rgba(255, 30, 30, 0.6);
            background: rgba(255,30,30,0.06);
            box-shadow: 0 0 0 3px rgba(255, 30, 30, 0.10);
        }

        .btn-submit {
            width: 100%;
            padding: 14px;
            background: linear-gradient(45deg, #ff1e1e, #900000);
            color: white;
            border: none;
            border-radius: 10px;
            font-size: 15px;
            font-weight: 700;
            cursor: pointer;
            transition: 0.3s;
            font-family: 'Poppins', sans-serif;
            box-shadow: 0 6px 22px rgba(255, 30, 30, 0.4);
            margin-top: 6px;
            letter-spacing: 0.4px;
        }

        .btn-submit:hover {
            transform: translateY(-2px);
            box-shadow: 0 12px 32px rgba(255, 30, 30, 0.55);
        }

        .btn-submit:active { transform: translateY(0); }

        .divider {
            height: 1px;
            background: rgba(255,255,255,0.07);
            margin: 28px 0 20px;
        }

        .footer-note {
            text-align: center;
            font-size: 12px;
            color: rgba(255,255,255,0.25);
            line-height: 1.7;
        }

        .back-link {
            text-align: center;
            margin-top: 22px;
        }

        .back-link a {
            font-size: 13px;
            color: rgba(255,255,255,0.28);
            text-decoration: none;
            transition: 0.2s;
        }

        .back-link a:hover { color: rgba(255,255,255,0.6); }

        @media (max-width: 480px) {
            .card { padding: 30px 22px; }
        }
    </style>
</head>
<body>

<div class="glow-top-right"></div>
<div class="glow-bottom-left"></div>

<div class="wrapper">
    <div class="admin-badge">
        <div class="badge-icon">🔐</div>
        <div class="badge-text">
            <div class="title">CYRI DRIVE CO.</div>
            <div class="sub">Admin Portal</div>
        </div>
    </div>

    <div class="card">
        <div class="card-title">Dashboard Access</div>
        <div class="card-sub">Authorized personnel only</div>

        <div class="access-bar">
            <div class="dot"></div>
            <span>RESTRICTED — Authorized Access Only</span>
        </div>

        <?php if ($error && $login_attempted): ?>
            <div class="alert-error">
                <span>⛔</span>
                <span><?= htmlspecialchars($error) ?></span>
            </div>
        <?php endif; ?>

        <form action="" method="POST" autocomplete="off">
            <div class="form-group">
                <label for="username">Admin Username</label>
                <input type="text" id="username" name="username"
                       placeholder="Enter your username"
                       value="<?= htmlspecialchars($_POST['username'] ?? '') ?>"
                       required autofocus autocomplete="off">
            </div>

            <div class="form-group">
                <label for="password">Password</label>
                <input type="password" id="password" name="password"
                       placeholder="Enter your password"
                       required autocomplete="off">
            </div>

            <button type="submit" class="btn-submit">Access Dashboard →</button>
        </form>

        <div class="divider"></div>

        <div class="footer-note">
            For account setup, contact your<br>system administrator.
        </div>
    </div>

    <div class="back-link">
        <a href="index.html">← Back to Main Page</a>
    </div>
</div>

</body>
</html>
