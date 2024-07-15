<?php
session_start();
include "database_conn.php";
require './vendor/autoload.php'; // Include Composer's autoload

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// Initialize variables
$email = $emailErr = $otpSent = "";

function sanitize_input($data) {
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    return $data;
}

// Define multiple Gmail SMTP configurations
$smtpConfigs = array(
    array(
        'host' => 'smtp.gmail.com',
        'username' => 'joannacaguco@gmail.com',
        'password' => '************',
        'port' => 587,
        'encryption' => PHPMailer::ENCRYPTION_STARTTLS
    ),
    array(
        'host' => 'smtp.gmail.com',
        'username' => 'joannacaguco@gmail.com',
        'password' => '***************',
        'port' => 587,
        'encryption' => PHPMailer::ENCRYPTION_STARTTLS
    ),
    // Add more configurations as needed
);

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (empty($_POST["email"])) {
        $emailErr = "Email is required";
    } else {
        $email = sanitize_input($_POST["email"]);
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $emailErr = "Invalid email format";
        }
    }

    if (empty($emailErr)) {
        // Check if email exists in the database
        $sql = "SELECT id FROM users WHERE email = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows == 1) {
            $user = $result->fetch_assoc();
            $userId = $user['id'];

            // Generate OTP
            $otp = rand(100000, 999999);

            // Store OTP in session
            $_SESSION['otp'] = $otp;
            $_SESSION['user_id'] = $userId;
            $_SESSION['email'] = $email;

            // Choose a random SMTP configuration from $smtpConfigs
            $smtpConfig = $smtpConfigs[array_rand($smtpConfigs)];

            // Send OTP email
            $mail = new PHPMailer(true); // Enable exceptions

            try {
                //Server settings
                $mail->isSMTP();
                $mail->Host       = $smtpConfig['host'];
                $mail->SMTPAuth   = true;
                $mail->Username   = $smtpConfig['username'];
                $mail->Password   = $smtpConfig['password'];
                $mail->SMTPSecure = $smtpConfig['encryption'];
                $mail->Port       = $smtpConfig['port'];

                // Optional debugging
                $mail->SMTPDebug = 2; // Enable verbose debug output
                $mail->Debugoutput = 'html'; // Print debug output as HTML

                //Recipients
                $mail->setFrom($smtpConfig['username'], 'Mailer'); // Use the sender's email here
                $mail->addAddress($email);

                // Content
                $mail->isHTML(true);
                $mail->Subject = 'Your OTP for Password Reset';
                $mail->Body    = "Your OTP for password reset is: $otp";

                $mail->send();
                $otpSent = "OTP sent to your email.";
                header("Location: verify_otp.php");
                exit();
            } catch (Exception $e) {
                $emailErr = "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
            }
        } else {
            $emailErr = "No account found with that email.";
        }

        $stmt->close();
        $conn->close();
    }
}
?> 
