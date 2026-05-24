<?php

define('EMAIL_ENABLED', false);

define('SMTP_HOST',  'smtp.gmail.com');
define('SMTP_PORT',  587);
define('SMTP_USER',  'your-email@gmail.com');
define('SMTP_PASS',  'your-app-password');
define('FROM_EMAIL', 'your-email@gmail.com');
define('FROM_NAME',  'CYRI DRIVE CO');

function send_booking_confirmation($customer_name, $customer_email, $vehicle_name, $number_of_days, $price_per_day, $total_amount, $payment_method, $booking_id, $start_date, $end_date, $pickup_time, $return_time) {
    if (!EMAIL_ENABLED || empty($customer_email)) {
        return false;
    }

    $subject = "Booking Confirmation - CYRI DRIVE CO (Ref #" . str_pad($booking_id, 5, '0', STR_PAD_LEFT) . ")";

    $booking_ref = str_pad($booking_id, 5, '0', STR_PAD_LEFT);
    $formatted_total = number_format($total_amount);
    $formatted_price = number_format($price_per_day);
    $start_formatted = date('F d, Y', strtotime($start_date));
    $end_formatted = date('F d, Y', strtotime($end_date));

    $message = "
<!DOCTYPE html>
<html>
<head>
    <meta charset='UTF-8'>
    <style>
        body { font-family: Arial, sans-serif; color: #333; }
        .container { max-width: 600px; margin: 0 auto; padding: 20px; background: #f9f9f9; border-radius: 8px; }
        .header { background: linear-gradient(45deg, #ff1e1e, #c40000); color: white; padding: 20px; border-radius: 8px 8px 0 0; text-align: center; }
        .content { background: white; padding: 20px; border-radius: 0 0 8px 8px; }
        .receipt { background: #f0f0f0; padding: 15px; border-left: 4px solid #ff1e1e; margin: 20px 0; border-radius: 4px; }
        .receipt p { margin: 8px 0; }
        .footer { text-align: center; color: #999; font-size: 12px; margin-top: 20px; }
        strong { color: #111; }
        .highlight { color: #ff1e1e; font-weight: bold; font-size: 18px; }
    </style>
</head>
<body>
    <div class='container'>
        <div class='header'>
            <h1>✅ Booking Confirmed!</h1>
        </div>
        <div class='content'>
            <p>Dear <strong>" . htmlspecialchars($customer_name) . "</strong>,</p>
            <p>Thank you for booking with CYRI DRIVE CO! Your reservation has been confirmed and saved to our system.</p>

            <div class='receipt'>
                <p><strong>Booking Reference:</strong> #" . $booking_ref . "</p>
                <p><strong>Vehicle:</strong> " . htmlspecialchars($vehicle_name) . "</p>
                <p><strong>Rental Dates:</strong> " . $start_formatted . " to " . $end_formatted . "</p>
                <p><strong>Rental Duration:</strong> " . $number_of_days . " day(s)</p>
                <p><strong>Pickup Time:</strong> " . htmlspecialchars($pickup_time) . "</p>
                <p><strong>Return Time:</strong> " . htmlspecialchars($return_time) . "</p>
                <p><strong>Price Per Day:</strong> ₱" . $formatted_price . "</p>
                <p><strong>Total Payment:</strong> <span class='highlight'>₱" . $formatted_total . "</span></p>
                <p><strong>Payment Method:</strong> " . htmlspecialchars($payment_method) . "</p>
            </div>

            <p>Please keep your booking reference number safe for future correspondence.</p>
            <p>If you have any questions or need to modify your booking, please contact us as soon as possible.</p>

            <p>Best regards,<br><strong>CYRI DRIVE CO Team</strong></p>

            <div class='footer'>
                <p>This is an automated message. Please do not reply to this email.</p>
                <p>© 2026 CYRI DRIVE CO. All rights reserved.</p>
            </div>
        </div>
    </div>
</body>
</html>
    ";

    return send_mail($customer_email, $subject, $message);
}

function send_mail($to, $subject, $html_message) {
    if (!EMAIL_ENABLED) {
        return false;
    }

    $headers  = "MIME-Version: 1.0\r\n";
    $headers .= "Content-type: text/html; charset=UTF-8\r\n";
    $headers .= "From: " . FROM_NAME . " <" . FROM_EMAIL . ">\r\n";
    $headers .= "Reply-To: " . FROM_EMAIL . "\r\n";

    try {
        $smtp_connection = @fsockopen(SMTP_HOST, SMTP_PORT, $errno, $errstr, 15);

        if (!$smtp_connection) {
            error_log("CYRI Email: SMTP connection failed — $errstr ($errno)");
            return false;
        }

        fgets($smtp_connection, 1024);

        fwrite($smtp_connection, "EHLO localhost\r\n");
        fgets($smtp_connection, 1024);

        fwrite($smtp_connection, "STARTTLS\r\n");
        fgets($smtp_connection, 1024);

        stream_socket_enable_crypto($smtp_connection, true, STREAM_CRYPTO_METHOD_TLS_CLIENT);

        fwrite($smtp_connection, "EHLO localhost\r\n");
        fgets($smtp_connection, 1024);

        fwrite($smtp_connection, "AUTH LOGIN\r\n");
        fgets($smtp_connection, 1024);

        fwrite($smtp_connection, base64_encode(SMTP_USER) . "\r\n");
        fgets($smtp_connection, 1024);

        fwrite($smtp_connection, base64_encode(SMTP_PASS) . "\r\n");
        $auth_response = fgets($smtp_connection, 1024);

        if (strpos($auth_response, '235') === false) {
            error_log("CYRI Email: Authentication failed — " . trim($auth_response));
            fclose($smtp_connection);
            return false;
        }

        fwrite($smtp_connection, "MAIL FROM: <" . FROM_EMAIL . ">\r\n");
        fgets($smtp_connection, 1024);

        fwrite($smtp_connection, "RCPT TO: <$to>\r\n");
        fgets($smtp_connection, 1024);

        fwrite($smtp_connection, "DATA\r\n");
        fgets($smtp_connection, 1024);

        $body  = "Subject: $subject\r\n";
        $body .= $headers;
        $body .= "\r\n";
        $body .= $html_message . "\r\n";
        $body .= ".\r\n";

        fwrite($smtp_connection, $body);
        $send_response = fgets($smtp_connection, 1024);

        fwrite($smtp_connection, "QUIT\r\n");
        fclose($smtp_connection);

        if (strpos($send_response, '250') === false) {
            error_log("CYRI Email: Send failed — " . trim($send_response));
            return false;
        }

        return true;

    } catch (Exception $e) {
        error_log("CYRI Email error: " . $e->getMessage());
        return false;
    }
}
