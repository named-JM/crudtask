<?php
include "database_conn.php";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $fullname = $_POST['fullname'];
    $email = $_POST['email'];
    $gender = $_POST['gender'];
    $age = $_POST['age'];
    $password = $_POST['password'];
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);

    // Handle file upload
    $picture = $_FILES['picture'];
    $target_dir = "uploads/";
    $target_file = $target_dir . basename($picture["name"]);

    // Move uploaded file to the target directory
    if (move_uploaded_file($picture["tmp_name"], $target_file)) {
        $sql = "INSERT INTO `users`(`fullname`, `email`, `gender`, `age`, `picture`, `password`) VALUES ('$fullname', '$email', '$gender', '$age', '$target_file', '$hashed_password')";

        if ($conn->query($sql) === TRUE) {
            header("Location: login.php");
            exit();
        } else {
            echo "ERROR: " . $sql . $conn->error;
        }
    } else {
        echo "Failed to upload the picture.";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registration Page</title>
    <!-- TAILWIND CDN -->
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
</head>
<body class="bg-gray-100 flex items-center justify-end min-h-screen mr-20">
<!-- <body class="bg-gray-100 "> -->
    <div class="w-1/2">
        <h2 class="text-3xl font-bold mb-4">Welcome to the CRUD Side!</h2>
        <p class="text-xl mb-8">Join us now</p>
    </div>
    <div class="bg-white p-8 rounded-lg shadow-lg w-full max-w-sm">
        <h2 class="text-2xl font-bold mb-6 text-center">Registration Form</h2>
        <!-- REGISTRATION FORM! -->
        <form action="register.php" method="POST" enctype="multipart/form-data">
            <!-- FULL NAME -->
            <div class="mb-4">
                <label class="block text-gray-700 mb-2" for="name">Full Name</label>
                <input class="w-full px-3 py-2 border rounded-md focus:outline-none focus:border-indigo-500" type="text" name="fullname" id="fullname" placeholder="Juan Dela Cruz" required>
            </div>
            <!-- EMAIL -->
            <div class="mb-4">
                <label class="block text-gray-700 mb-2" for="email">Email</label>
                <input class="w-full px-3 py-2 border rounded-md focus:outline-none focus:border-indigo-500" name="email" type="email" id="email" placeholder="jm@gmail.com" required>
            </div>
            <!-- GENDER -->
            <div class="mb-5 flex items-center">
                <label class="mr-4 text-gray-700" for="gender">Gender:</label>
                <div class="flex items-center">
                    <input class="mr-2" type="radio" id="male" name="gender" value="Male" required>
                    <label class="mr-4" for="male">Male</label>
                    <input class="mr-2" type="radio" id="female" name="gender" value="Female" required>
                    <label class="mr-4" for="female">Female</label>
                    <input class="mr-2" type="radio" id="other" name="gender" value="Other" required>
                    <label for="other">Other</label>
                </div>
            </div>
            <!-- AGE -->
            <div class="mb-3 flex items-center justify-between">
                <div>
                    <label class="block text-gray-700 mb-2" for="age">Age</label>
                    <input class="w-20 px-3 py-2 border rounded-md focus:outline-none focus:border-indigo-500" type="number" name="age" id="age" required>
                </div>
                <!-- UPLOAD PICTURE -->
                <div>
                    <label class="block text-gray-700 mb-2" for="picture">Upload Picture</label>
                    <input class="w-60 mx-3 mr-10 px-3 py-2" type="file" name="picture" id="picture" accept="image/*" required>
                </div>
            </div>
            <!-- PASSWORD -->
            <div class="mb-6">
                <label class="block text-gray-700 mb-2" for="password">Password</label>
                <input class="w-full px-3 py-2 border rounded-md focus:outline-none focus:border-indigo-500" type="password" name="password" id="password" required>
            </div>
            <!-- CONFIRM PASSWORD  -->
            <div class="mb-6">
                <label class="block text-gray-700 mb-2" for="password">Confirm Password</label>
                <input class="w-full px-3 py-2 border rounded-md focus:outline-none focus:border-indigo-500" type="password" id="password" required>
            </div>
            <!-- REGISTERED BUTTON -->
            <button class="w-full bg-gray-200 text-black py-2 rounded-full hover:bg-gray-400 transition duration-300" type="submit">Register</button>
        </form>
        <!-- LOGIN OPTION -->
        <p class="mt-4 text-center">Already have an account? <a class="text-indigo-500 hover:underline" href="login.php">Login</a></p>
    </div>
</body>
</html>
