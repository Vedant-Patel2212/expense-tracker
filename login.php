<?php
session_start();
require_once 'db_connect.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = $_POST['email'];
    $password = $_POST['password'];
    $query = "SELECT email, password FROM admin WHERE email = '$email'";
    $result = mysqli_query($conn, $query);

    if ($result && mysqli_num_rows($result) == 1) {
        $admin = mysqli_fetch_assoc($result);
        if ($password == $admin['password']) {
            $_SESSION['admin'] = true;
            $_SESSION['email'] = $admin['email'];
            $update = "UPDATE admin SET last_login = CURRENT_TIMESTAMP WHERE email = '$email'";
            mysqli_query($conn, $update);
            
            header("Location: admin_dashboard.php");
            exit();
        }
    }
    $query = "SELECT id, email, password FROM users WHERE email = '$email'";
    $result = mysqli_query($conn, $query);

    if ($result && mysqli_num_rows($result) == 1) {
        $user = mysqli_fetch_assoc($result);
        if ($password == $user['password']) {
            $query = "SELECT is_blocked, reason FROM user_status WHERE user_id = " . $user['id'] . " AND is_blocked = 1";
            $blocked_result = mysqli_query($conn, $query);
            
            if ($blocked_result && mysqli_num_rows($blocked_result) > 0) {
                $blocked_info = mysqli_fetch_assoc($blocked_result);
                $block_reason = $blocked_info['reason'] ? $blocked_info['reason'] : "No reason provided";
                $error = "Your account has been blocked. Reason: " . htmlspecialchars($block_reason);
            } else {
                $_SESSION['userId'] = $user['id'];
                $_SESSION['email'] = $user['email'];
                header("Location: dashboard.php");
                exit();
            }
        } else {
            $error = "Invalid email or password";
        }
    } else {
        $error = "Invalid email or password";
    }
}
?>
