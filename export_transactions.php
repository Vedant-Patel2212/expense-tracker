<?php
session_start();
require_once 'db_connect.php';

if (!isset($_SESSION['userId'])) {
    header('Location: index.html');
    exit();
}

$user_id = $_SESSION['userId'];
$result = mysqli_query($conn, "SELECT username FROM users WHERE id = $user_id");
$user = mysqli_fetch_assoc($result);
$username = $user['username'] ?? '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $start_date = $_POST['start_date'];
    $end_date = $_POST['end_date'];

    $myquery = "SELECT 'Income' AS type, amount, description, date, category FROM income 
              WHERE user_id = $user_id AND date BETWEEN '$start_date' AND '$end_date'
              UNION ALL
              SELECT 'Expense' AS type, amount, description, date, category FROM expenses 
              WHERE user_id = $user_id AND date BETWEEN '$start_date' AND '$end_date'
              ORDER BY date DESC";

    $result = mysqli_query($conn, $myquery);

    header('Content-Type: text/csv');
    header('Content-Disposition: attachment; filename="transactions_' . date('Y-m-d') . '.csv"');

    $output = fopen('php://output', 'w');
    fputcsv($output, ['Type', 'Amount', 'Description', 'Date', 'Category']);

    while ($row = mysqli_fetch_assoc($result)) {
        fputcsv($output, $row);
    }

    fclose($output);
    exit();
}
?>

<!DOCTYPE html>
<head>
    <title>Export Transactions - Expense Tracker</title>
    <link rel="stylesheet" href="mycss.css">
    <link rel="icon" href="logo.png" type="image/x-icon"> 
</head>
<body>
    <div class="sidebar">
        <div class="profile-section">
            <div class="user-name"><?php echo htmlspecialchars($username); ?></div>
        </div>
        <ul class="nav-menu">
            <li class="nav-item">
                <a href="dashboard.php" class="nav-link">Dashboard</a>
            </li>
            <li class="nav-item">
                <a href="add_income.php" class="nav-link">Add Income</a>
            </li>
            <li class="nav-item">
                <a href="add_expense.php" class="nav-link">Add Expense</a>
            </li>
            <li class="nav-item">
                <a href="export_transactions.php" class="nav-link active">Export Transactions</a>
            </li>
            <li class="nav-item">
                <a href="set_budget.php" class="nav-link">Set Budget</a>
            </li>
            <li class="nav-item">
                <a href="Review_aboutus.php" class="nav-link">
                    About & Feedback
                </a>
            </li>
        </ul>
        <a href="logout.php" class="sign-out">Sign Out</a>
    </div>

    <div class="main-content">
        <div class="content-wrapper">
            <h1>Export Transactions</h1>
            <form action="export_transactions.php" method="POST" class="export-form">
                <div class="form-group">
                    <label for="start_date">Start Date</label>
                    <input type="date" id="start_date" name="start_date" required>
                </div>
                <div class="form-group">
                    <label for="end_date">End Date</label>
                    <input type="date" id="end_date" name="end_date" required>
                </div>
                <button type="submit" class="btn-submit">Export Transactions</button>
            </form>
            <div class="export-info">
                <h2>Export Information</h2>
                <p>This feature allows you to export your transactions as a CSV file.</p>
                <ul>
                    <li>Type (Income or Expense)</li>
                    <li>Amount</li>
                    <li>Description</li>
                    <li>Date</li>
                    <li>Category</li>
                </ul>
                <p>Select a date range to export transactions from that date.</p>
            </div>
        </div>
    </div>
</body>
</html>
