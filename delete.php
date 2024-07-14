<?php
// Include database connection
include "database_conn.php";

// Get the user ID from the query string
$id = $_GET['id'];

// Update the `is_deleted` column to 1
$sql = "UPDATE users SET is_deleted = 1 WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $id);

if ($stmt->execute()) {
    header("Location: dashboard.php");
    exit();
} else {
    echo "Error: " . $sql . "<br>" . $conn->error;
}

$stmt->close();
$conn->close();
?>
