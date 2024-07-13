<?php
include "database_conn.php";
session_start(); // Start the session to use $_SESSION

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST['email'];
    $password = $_POST['password'];

    // Fetching hashed password
    $query = "SELECT * FROM `users` WHERE email='$email'";
    $result = $conn->query($query);

    if ($result && $result->num_rows == 1) {
        $user = $result->fetch_assoc();
        $hashed_password = $user['password'];

        // Verifying password
        if (password_verify($password, $hashed_password)) {
            header("Location: dashboard.php");
            exit();
        } else {
            $_SESSION['error'] = "Password is incorrect.";
        }
    } else {
        $_SESSION['error'] = "User not found.";
    }

    $conn->close();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Page</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
</head>
<body class="bg-gray-100 flex items-center justify-center min-h-screen">
    <div class="bg-white p-8 rounded-lg shadow-lg w-full max-w-sm">
        <h2 class="text-2xl font-bold mb-6 text-center">Login</h2>
        <form action="login.php" method="POST">
            <?php
            if (isset($_SESSION['error'])) {
                echo '<div class="mb-4 text-red-500">' . $_SESSION['error'] . '</div>';
                unset($_SESSION['error']); // Clear the error after displaying it
            }
            ?>
            <div class="mb-4">
                <label class="block text-gray-700 mb-2" for="email">Email</label>
                <input class="w-full px-3 py-2 border rounded-md focus:outline-none focus:border-indigo-500" type="email" name="email" id="email" required>
            </div>
            <div class="mb-6">
                <label class="block text-gray-700 mb-2" for="password">Password</label>
                <input class="w-full px-3 py-2 border rounded-md focus:outline-none focus:border-indigo-500" type="password" name="password" id="password" required>
            </div>
            <button class="w-full bg-gray-200 text-black py-2 rounded-full hover:bg-gray-400 transition duration-300" type="submit">Login</button>
        </form>
        <p class="mt-4 text-center">Don't have an account? <a class="text-indigo-500 hover:underline" href="register.php">Register</a></p>
    </div>
</body>
</html>
