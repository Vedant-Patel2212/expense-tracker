<?php
session_start();
require_once 'db_connect.php';

if (!isset($_SESSION['admin']) || $_SESSION['admin'] !== true) {
    header('Location: login.php');
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'], $_POST['user_id'])) {
    $user_id = (int) $_POST['user_id'];
    $reason = isset($_POST['reason']) ? $_POST['reason'] : '';

    if ($_POST['action'] === 'block') {
        $check_query = "SELECT id FROM user_status WHERE user_id = $user_id";
        $check_result = mysqli_query($conn, $check_query);

        if (mysqli_num_rows($check_result) > 0) {
            $query = "UPDATE user_status SET is_blocked = 1, blocked_at = CURRENT_TIMESTAMP, reason = '$reason' WHERE user_id = $user_id";
        } else {
            $query = "INSERT INTO user_status (user_id, is_blocked, reason) VALUES ($user_id, 1, '$reason')";
        }

        mysqli_query($conn, $query);
        $success_message = "User has been blocked successfully.";
    } elseif ($_POST['action'] === 'unblock') {
        $query = "UPDATE user_status SET is_blocked = 0, reason = NULL WHERE user_id = $user_id";
        mysqli_query($conn, $query);
        $success_message = "User has been unblocked successfully.";
    }
}

$query = "SELECT u.id, u.username, u.email, COALESCE(us.is_blocked, 0) AS is_blocked, us.reason, us.blocked_at 
          FROM users u 
          LEFT JOIN user_status us ON u.id = us.user_id
          ORDER BY u.username";
$result = mysqli_query($conn, $query);
$users = mysqli_fetch_all($result, MYSQLI_ASSOC);
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Users - Admin Dashboard</title>
    <link rel="stylesheet" href="expense_tracker/mycss.css">
    <style>
        .user-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        .user-table th, .user-table td {
            padding: 12px 15px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }
        .user-table th {
            background-color: #f8f9fa;
            font-weight: 600;
        }
        .user-table tr:hover {
            background-color: #f1f1f1;
        }
        .btn-block, .btn-unblock {
            padding: 6px 12px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 14px;
            color: white;
        }
        .btn-block {
            background-color: #dc3545;
        }
        .btn-unblock {
            background-color: #28a745;
        }
        .status {
            padding: 4px 8px;
            border-radius: 4px;
            font-size: 12px;
            font-weight: 600;
        }
        .status-active {
            background-color: #d4edda;
            color: #155724;
        }
        .status-blocked {
            background-color: #f8d7da;
            color: #721c24;
        }
        .modal {
            display: none;
            position: fixed;
            z-index: 1000;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0,0,0,0.5);
        }
        .modal-content {
            background-color: white;
            margin: 10% auto;
            padding: 20px;
            border-radius: 5px;
            width: 50%;
            max-width: 500px;
        }
        .close {
            color: #aaa;
            float: right;
            font-size: 28px;
            font-weight: bold;
            cursor: pointer;
        }
        .close:hover {
            color: black;
        }
        .alert {
            padding: 15px;
            margin-bottom: 20px;
            border-radius: 4px;
        }
        .alert-success {
            background-color: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }
        .reason-text {
            font-size: 0.9em;
            color: #721c24;
            margin-top: 5px;
        }
    </style>
</head>
<body>

    <h1>Manage Users</h1>

    <?php if (isset($success_message)): ?>
        <div class="alert alert-success"><?php echo $success_message; ?></div>
    <?php endif; ?>

    <table class="user-table">
        <thead>
            <tr>
                <th>Username</th>
                <th>Email</th>
                <th>Status</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($users as $user): ?>
                <tr>
                    <td><?php echo htmlspecialchars($user['username']); ?></td>
                    <td><?php echo htmlspecialchars($user['email']); ?></td>
                    <td>
                        <?php if ($user['is_blocked']): ?>
                            <span class="status status-blocked">Blocked</span>
                            <?php if ($user['reason']): ?>
                                <div class="reason-text">Reason: <?php echo htmlspecialchars($user['reason']); ?></div>
                            <?php endif; ?>
                        <?php else: ?>
                            <span class="status status-active">Active</span>
                        <?php endif; ?>
                    </td>
                    <td>
                        <?php if ($user['is_blocked']): ?>
                            <form method="POST" style="display: inline;">
                                <input type="hidden" name="action" value="unblock">
                                <input type="hidden" name="user_id" value="<?php echo $user['id']; ?>">
                                <button type="submit" class="btn-unblock">Unblock</button>
                            </form>
                        <?php else: ?>
                            <button class="btn-block" onclick="openBlockModal(<?php echo $user['id']; ?>, '<?php echo htmlspecialchars($user['username']); ?>')">Block</button>
                        <?php endif; ?>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
    <div id="blockModal" class="modal">
        <div class="modal-content">
            <span class="close" onclick="closeBlockModal()">&times;</span>
            <h2>Block User</h2>
            <p>Are you sure you want to block <span id="blockUserName"></span>?</p>
            <form method="POST">
                <input type="hidden" name="action" value="block">
                <input type="hidden" id="blockUserId" name="user_id">
                <textarea id="reason" name="reason" rows="4" placeholder="Enter reason for blocking..."></textarea>
                <button type="submit">Block User</button>
            </form>
        </div>
    </div>

    <script>
        function openBlockModal(userId, userName) {
            document.getElementById('blockModal').style.display = 'block';
            document.getElementById('blockUserId').value = userId;
            document.getElementById('blockUserName').textContent = userName;
        }

        function closeBlockModal() {
            document.getElementById('blockModal').style.display = 'none';
        }
    </script>

</body>
</html>
