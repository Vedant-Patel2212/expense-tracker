<?php
include 'db_connect.php';

$username = $_POST['username'] ?? '';
$email = $_POST['email'] ?? '';
$password = $_POST['password'] ?? '';
$confirm_password = $_POST['confirm_password'] ?? '';

if (!$username || !$email || !$password || !$confirm_password) {
    exit("All fields are required.");
}

if ($password !== $confirm_password) {
    exit("Passwords do not match.");
}

if (mysqli_query($conn, "INSERT INTO users (username, email, password) VALUES ('$username', '$email', '$password')")) {
    header("Location: index.php");
    exit();
} else {
    exit("Error creating account.");
}

mysqli_close($conn);
?>
