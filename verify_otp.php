<?php
session_start();

// Initialize variables
$otpErr = $otp = "";

// Function to sanitize input
function sanitize_input($data) {
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    return $data;
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (empty($_POST["otp"])) {
        $otpErr = "OTP is required";
    } else {
        $otp = sanitize_input($_POST["otp"]);
        if ($otp != $_SESSION['otp']) {
            $otpErr = "Invalid OTP";
        } else {
            // OTP is valid, redirect to reset password page
            header("Location: reset_password.php");
            exit();
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verify OTP</title>
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
            right: 0.3rem; /* Position button within input */
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
        <h2 class="text-2xl text-center font-bold mb-6 text-gray-800">Verify OTP</h2>
        <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
            <div class="mb-6">
                <label class="block text-gray-700 text-sm font-bold mb-2" for="otp">Please check your email for OTP Code</label>
                <div class="input-with-button">
                <input class="border rounded w-full py-5 px-3 text-gray-700 mb-2" id="otp" name="otp" type="text" placeholder="Enter OTP" value="<?php echo htmlspecialchars($otp); ?>" required>
                <button class="bg-purple-700 text-white py-2 px-4 rounded hover:bg-purple-600 focus:outline-none" type="submit">Verify</button>
                </div>
                
                <span class="text-sm text-red-500 mb-4 block"><?php echo $otpErr; ?></span>
            </div>
            <!-- <div class="flex justify-center">
                <button class="bg-purple-700 text-white py-2 px-4 rounded hover:bg-purple-600 focus:outline-none" type="submit">Verify OTP</button>
            </div> -->
        </form>
    </div>
</body>
</html>