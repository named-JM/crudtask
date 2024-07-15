<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Include database connection
include "database_conn.php";

// Fetch user data from the database
$sql = "SELECT id, fullname, age, gender, email, picture FROM users WHERE is_deleted=0";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Welcome</title>
    <!-- datatable style cdn -->
    <link rel="stylesheet" href="//cdn.datatables.net/1.10.24/css/jquery.dataTables.min.css">
    <!-- jquery cdn  -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="//cdn.datatables.net/1.10.24/js/jquery.dataTables.min.js"></script>
    <!-- font awesome icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
    <!-- Tailwind CSS CDN -->
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">

    <script>
        $(document).ready(function(){
            $('#example').DataTable();
        });
    </script>
</head>
<style>
.logout-btn {
    display: inline-block;
    padding: 10px 20px;
    margin-bottom: 20px;
    background-color: #f44336;
    color: white;
    text-align: center;
    text-decoration: none;
    border-radius: 5px;
}
.logout-btn:hover {
    background-color: #d32f2f;
}

.dataTables_filter {
    margin-bottom: 20px;
}
</style>

<body class="bg-gray-100 p-8">
    <div class="flex items-center mb-8">
        <img src="<?php echo htmlspecialchars($_SESSION['user_picture']); ?>" alt="User Picture" class="w-12 h-12 rounded-full object-cover mr-4">
        <h1 class="text-2xl font-bold">Hi, <?php echo htmlspecialchars($_SESSION['user_name']); ?>! Welcome!</h1>
        <a href="logout.php" class="logout-btn mb-8">Logout</a>

    </div>
    
    <table id="example" class="display w-full bg-white rounded-lg shadow-lg">
        <thead>
            <tr class="text-left">
                <th>Full Name</th>
                <th>Age</th>
                <th>Gender</th>
                <th>Email</th>
                <th>Picture</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php
            if ($result->num_rows > 0) {
                // Output data of each row
                while($row = $result->fetch_assoc()) {
                    echo "<tr>";
                    echo "<td>" . htmlspecialchars($row['fullname']) . "</td>";
                    echo "<td>" . htmlspecialchars($row['age']) . "</td>";
                    echo "<td>" . htmlspecialchars($row['gender']) . "</td>";
                    echo "<td>" . htmlspecialchars($row['email']) . "</td>";
                    echo "<td><img src='" . htmlspecialchars($row['picture']) . "' alt='User Picture' class='w-12 h-12 rounded-full object-cover'></td>";
                    echo "<td class='flex space-x-2 text-center'>";
                    echo "<a href='edit.php?id=" . $row['id'] . "' class='text-center text-blue-500 hover:text-blue-700  text-xl'><i class='fa fa-edit'></i></a>";
                    echo "<a href='delete.php?id=" . $row['id'] . "' class='text-center text-red-500 hover:text-red-700 text-xl' ><i class='fa fa-trash'></i></a>";
                    echo "</td>";
                    echo "</tr>";
                }
            } else {
                echo "<tr><td colspan='6' class='text-center'>No data available</td></tr>";
            }
            $conn->close();
            ?>
        </tbody>
        <tfoot class="text-left">
            <tr>
                <th>Full Name</th>
                <th>Age</th>
                <th>Gender</th>
                <th>Email</th>
                <th>Picture</th>
                <th>Actions</th>
            </tr>
        </tfoot>
    </table>
</body>
</html>
