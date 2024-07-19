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
            $mail->Username   = 'noreplyfakeprimeo@gmail.com';
            $mail->Password   = 'nedt ydzx skju oxmf';
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port       = 587;

            // Recipients
            $mail->setFrom('noreplyfakeprimeo@gmail.com', 'Fake'); // Use your sender's email here
            $mail->addAddress($email);

            // Content
            $mail->isHTML(true);
            $mail->Subject = 'Your OTP for Account Deletion';

            // Styled email body
            $mail->Body = "
                <html>
                <body style='font-family: Arial, sans-serif; background-color: #f4f4f4; padding: 20px;'>
                    <div style='max-width: 1000px; margin: 0 auto; background-color: #ffffff; border: 1px solid #dddddd; border-radius: 5px;'>
                        <div style='background-color: #FFBF00; padding: 10px; text-align: center; color: #ffffff; border-top-left-radius: 5px; border-top-right-radius: 5px;'>
                            <h1>Verification Code for Account Deletion</h1>
                        </div>
                        <div style='padding: 20px; text-align: center;'>
                            <img src='https://i.pinimg.com/736x/56/8a/f2/568af2bf861bcebacb310dc24840b4e3.jpg' alt='Logo' style='margin-bottom: 20px; width: 50px; height:50px;'>
                            <p style='font-size: 16px; color: #333333;'>You are <span style='color: red;'>deleting</span> an account. If this is not you, please disregard this message.</p>
                            <p style='font-size: 18px; color: #333333;'>Your Verification Code for account deletion is:</p>
                            <h2 style='font-size: 36px; color: #FFBF00;'>$otp</h2>
                            <p style='font-size: 16px; color: #333333;'>Please enter this OTP to confirm your account deletion.</p>
                        </div>
                    </div>
                </body>
                </html>
            ";

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
