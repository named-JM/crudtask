<?php
session_start();
include "database_conn.php";

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
        if ($otp != $_SESSION['delete_otp']) {
            $otpErr = "Invalid OTP";
        } else {
            // OTP is valid, delete the user account
            $userId = $_SESSION['delete_user_id'];
            $sql = "UPDATE users SET is_deleted = 1 WHERE id = ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("i", $userId);
            $stmt->execute();

            if ($stmt->affected_rows > 0) {
                echo "Account deleted successfully.";
                // Redirect to dashboard or any other page
                header("Location: dashboard.php");
                exit();
            } else {
                $otpErr = "Failed to delete account.";
            }
            $stmt->close();
            $conn->close();
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Delete Confirmation</title>
    <script src="https://cdn.tailwindcss.com"></script>
     <script src="https://cdn.jsdelivr.net/npm/sweetalert2@10"></script>
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
            background-color: red;
            color: white;
            border-radius: 0.375rem;
        }

        .input-with-button button:hover {
            background-color: #ff8111; /* hover:bg-purple-600 */
        }
    </style>
</head>
<body class="bg-gray-200 flex items-center justify-center h-screen">
    <div class="w-full max-w-sm">
        <h2 class="text-1xl text-left font-bold mb-6 text-gray-800">You are <span class="text-red-500">deleting</span> an user.<br>Please check your email for the confirmation</h2>
        <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
            <div class="mb-6">
                <label class="block text-gray-700 text-sm font-bold mb-2" for="otp">Confirming Deletion</label>
                <div class="input-with-button">
                    <input class="border rounded w-full py-5 px-3 text-gray-700 mb-2" id="otp" name="otp" type="text" placeholder="Enter verification code" value="<?php echo htmlspecialchars($otp); ?>" required>
                    <button type="submit">Enter</button>
                </div>
                <span class="text-sm text-red-500 mb-4 block"><?php echo $otpErr; ?></span>
            </div>
        </form>
    </div>
</body>
</html>
