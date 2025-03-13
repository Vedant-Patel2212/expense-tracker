<?php
session_start();
require_once 'db_connect.php';

if (!isset($_SESSION['adminEmail'])) {
    header('Location: login.php');
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['user_id'])) {
    $user_id = $_POST['user_id'];

    if (isset($_POST['block'])) {
        $query = "INSERT INTO user_status (user_id, is_blocked) VALUES ($user_id, 1) 
                  ON DUPLICATE KEY UPDATE is_blocked = 1";
        mysqli_query($conn, $query);
    } elseif (isset($_POST['unblock'])) {
        $query = "UPDATE user_status SET is_blocked = 0 WHERE user_id = $user_id";
        mysqli_query($conn, $query);
    }
    header("Location: admin_dashboard.php");
    exit();
}
?>