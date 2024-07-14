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

    <script>
        $(document).ready(function(){
            $('#example').DataTable();
        });
    </script>
</head>
<body>
    <h1>Successfully Data Datatable Dashboard</h1>
    <table id="example" class="display" style="width:100%">
        <thead>
            <tr>
                <th>Full Name</th>
                <th>Age</th>
                <th>Gender</th>
                <th>Email</th>
                <th>Picture</th>
                <th></th>
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
                    echo "<td><img src='" . htmlspecialchars($row['picture']) . "' alt='User Picture' style='width:50px; height:50px;'></td>";
                    echo "<td>";
                    // echo "<a href ='edit.php?id=" . $row['id'] . "' class='edit-btn mr-5'><i class='fa fa-edit'</i></a>";
                    echo "<a href='edit.php?id=" . $row['id'] . "' class='edit-btn'><i class='fa fa-edit'></i></a>";
                    echo "<a href ='delete.php?id=" . $row['id'] . "' class='delete-btn'><i class='fa fa-trash'></i></a>";
                    echo "</td>";
                    echo "</tr>";
                }
            } else {
                echo "<tr><td colspan='5'>No data available</td></tr>";
            }
            $conn->close();
            ?>
        </tbody>
        <tfoot>
            <tr>
                <th>Full Name</th>
                <th>Age</th>
                <th>Gender</th>
                <th>Email</th>
                <th>Picture</th>
                <th></th>
            </tr>
        </tfoot>
    </table>
</body>
</html>
