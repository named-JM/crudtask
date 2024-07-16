<?php
session_start();
include "database_conn.php";
require './vendor/autoload.php';

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
        'username' => 'noreplyfakeprimeo@gmail.com',
        'password' => 'nedt ydzx skju oxmf',
        'port' => 587,
        'encryption' => PHPMailer::ENCRYPTION_STARTTLS
    ),
    array(
        'host' => 'smtp.gmail.com',
        'username' => 'noreplyfakeprimeo@gmail.com',
        'password' => 'nedt ydzx skju oxmf',
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
                $mail->setFrom($smtpConfig['username'], 'Fake'); // Use the sender's email here
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

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Forgot Password</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<style>
    .input-with-button {
            position: relative;
            display: flex;
            align-items: center;
            
        }

        .input-with-button input {
            padding-right: 5rem; /* Adjust padding to make space for the button */
        }

        .input-with-button button {
            position: absolute;
            right: 0.5rem; /* Position button within input */
            top: 45%;
            transform: translateY(-50%);
            padding: 0.5rem 0.5rem;
            background-color: #bdbdbd;
            color: white;
            height: 43px;
        }

        .input-with-button button:hover {
            background-color: #757575; /* hover:bg-purple-600 */
        }
</style>
<body class="bg-f-200 flex items-center justify-center h-screen">
    <div class="w-full max-w-sm">
        <h2 class="text-2xl text-center font-bold mb-6 text-gray-800">Forgot Password.</h2>
        <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
            <div class="mb-6">
                <label class="block text-gray-700 text-sm font-bold mb-2" for="email">Please enter your email for OTP Code.</label>
                <div class="input-with-button">
                    <input class="border rounded w-full py-5 px-3 text-gray-700 mb-2" id="email" name="email" type="email" placeholder="Email" value="<?php echo htmlspecialchars($email); ?>" required>
                    <button class="bg-yellow-700 text-white py-2 px-4 rounded hover:bg-purple-600 focus:outline-none" type="submit">Send</button>
                </div>
                <span class="text-sm text-red-500 mb-4 block"><?php echo $emailErr; ?></span>
                <span class="text-sm text-green-500 mb-4 block"><?php echo $otpSent; ?></span>
            </div>
            <!-- <div class="flex justify-center">
                <button class="bg-yellow-700 text-white py-2 px-4 rounded hover:bg-purple-600 focus:outline-none" type="submit">Send OTP</button>
            </div> -->
        </form>
    </div>
</body>
</html>
