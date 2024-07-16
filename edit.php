<?php
include "database_conn.php";
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

if (isset($_GET['id'])) {
    $id = $_GET['id'];
    
    // Fetch user data from the database
    $query = "SELECT * FROM users WHERE id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows == 1) {
        $user = $result->fetch_assoc();
    } else {
        header("Location: dashboard.php");
        exit();
    }
} else {
    header("Location: dashboard.php");
    exit();
}

// Update user data
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $fullname = $_POST['fullname'];
    $age = $_POST['age'];
    $gender = $_POST['gender'];
    $email = $_POST['email'];
    $picture = $user['picture']; // Default to current picture

    // Handle file upload
    if (isset($_FILES['picture']) && $_FILES['picture']['error'] == UPLOAD_ERR_OK) {
        $uploadDir = 'uploads/';
        $uploadFile = $uploadDir . basename($_FILES['picture']['name']);
        
        if (move_uploaded_file($_FILES['picture']['tmp_name'], $uploadFile)) {
            $picture = $uploadFile;
        } else {
            $_SESSION['error'] = "Failed to upload picture.";
        }
    }

    $updateQuery = "UPDATE users SET fullname = ?, age = ?, gender = ?, email = ?, picture = ? WHERE id = ?";
    $updateStmt = $conn->prepare($updateQuery);
    $updateStmt->bind_param("sisssi", $fullname, $age, $gender, $email, $picture, $id);

    if ($updateStmt->execute()) {
        $_SESSION['message'] = "User updated successfully.";
        header("Location: dashboard.php");
        exit();
    } else {
        $_SESSION['error'] = "Failed to update user.";
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit User</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
</head>
<body class="bg-gray-300 flex items-center justify-center min-h-screen">
    <div class="bg-white p-8 rounded-lg shadow-lg w-full max-w-md">
        <h2 class="text-2xl font-bold mb-6 text-center">Edit User</h2>
        <form action="edit.php?id=<?php echo $id; ?>" method="POST" enctype="multipart/form-data">
            <?php
            if (isset($_SESSION['error'])) {
                echo '<div class="mb-4 text-red-500">' . $_SESSION['error'] . '</div>';
                unset($_SESSION['error']);
            }
            ?>
            <div class="mb-4">
                <label class="block text-gray-700 mb-2" for="fullname">Full Name</label>
                <input class="w-full px-3 py-2 border rounded-md focus:outline-none focus:border-indigo-500" type="text" name="fullname" id="fullname" value="<?php echo htmlspecialchars($user['fullname']); ?>" required>
            </div>
            <div class="mb-4">
                <label class="block text-gray-700 mb-2" for="age">Age</label>
                <input class="w-full px-3 py-2 border rounded-md focus:outline-none focus:border-indigo-500" type="number" name="age" id="age" value="<?php echo htmlspecialchars($user['age']); ?>" required>
            </div>
            <div class="mb-5 flex items-center">
                <label class="mr-4 text-gray-700" for="gender">Gender:</label>
                <div class="flex items-center">
                    <input class="mr-2" type="radio" id="male" name="gender" value="Male" <?php if($user['gender'] == 'Male') echo 'checked'; ?> required>
                    <label class="mr-4" for="male">Male</label>
                    <input class="mr-2" type="radio" id="female" name="gender" value="Female" <?php if($user['gender'] == 'Female') echo 'checked'; ?> required>
                    <label class="mr-4" for="female">Female</label>
                    <input class="mr-2" type="radio" id="other" name="gender" value="Other" <?php if($user['gender'] == 'Other') echo 'checked'; ?> required>
                    <label for="other">Other</label>
                </div>
            </div>
            <div class="mb-4">
                <label class="block text-gray-700 mb-2" for="email">Email</label>
                <input class="w-full px-3 py-2 border rounded-md focus:outline-none focus:border-indigo-500" type="email" name="email" id="email" value="<?php echo htmlspecialchars($user['email']); ?>" required>
            </div>
            <div class="mb-4">
                <label class="block text-gray-700 mb-2" for="picture">Picture</label>
                <input class="w-full px-3 py-2 border rounded-md focus:outline-none focus:border-indigo-500" type="file" name="picture" id="picture">
            </div>
            <button class="w-full bg-yellow-300 text-black py-2 rounded-full hover:bg-yellow-500 transition duration-300" type="submit">Update</button>
        </form>
    </div>
</body>
</html>
