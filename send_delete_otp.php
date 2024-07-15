<?php
session_start();
include "database_conn.php";
require './vendor/autoload.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// Initialize variables
$email = $otpSent = "";

// Function to sanitize input
function sanitize_input($data) {
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    return $data;
}

if (isset($_GET['id'])) {
    $userId = sanitize_input($_GET['id']);

    // Fetch user email
    $sql = "SELECT email FROM users WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $userId);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows == 1) {
        $user = $result->fetch_assoc();
        $email = $user['email'];

        // Generate OTP
        $otp = rand(100000, 999999);

        // Store OTP in session
        $_SESSION['delete_otp'] = $otp;
        $_SESSION['delete_user_id'] = $userId;

        // Send OTP email
        $mail = new PHPMailer(true); // Enable exceptions

        try {
            //Server settings
            $mail->isSMTP();
            $mail->Host       = 'smtp.gmail.com';
            $mail->SMTPAuth   = true;
            $mail->Username   = '*********************';
            $mail->Password   = '*********************';
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port       = 587;

            // Recipients
            $mail->setFrom('valladorjennylyn@gmail.com', 'Primo'); // Use your sender's email here
            $mail->addAddress($email);

            // Content
            $mail->isHTML(true);
            $mail->Subject = 'Your OTP for Account Deletion';
            $mail->Body    = "Your OTP for account deletion is: $otp";

            $mail->send();
            $otpSent = "OTP sent to your email.";
            header("Location: verify_delete_otp.php");
            exit();
        } catch (Exception $e) {
            echo "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
        }
    } else {
        echo "No account found.";
    }

    $stmt->close();
    $conn->close();
}
?>
