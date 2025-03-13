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

$success_message = $error_message = '';
$result = mysqli_query($conn, "SELECT monthly_budget, yearly_budget FROM budget WHERE user_id = $user_id");
$budget = mysqli_fetch_assoc($result);

$monthly_budget = $budget['monthly_budget'] ?? '';
$yearly_budget = $budget['yearly_budget'] ?? '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $monthly_budget = $_POST['monthly_budget'];
    $yearly_budget = $_POST['yearly_budget'];
    $result = mysqli_query($conn, "SELECT id FROM budget WHERE user_id = $user_id");

    if (mysqli_num_rows($result) > 0) {
        $query = "UPDATE budget SET monthly_budget = $monthly_budget, yearly_budget = $yearly_budget WHERE user_id = $user_id";
    } else {
        $query = "INSERT INTO budget (user_id, monthly_budget, yearly_budget) VALUES ($user_id, $monthly_budget, $yearly_budget)";
    }

    if (mysqli_query($conn, $query)) {
        $success_message = "Budget updated successfully!";
    } else {
        $error_message = "Error updating budget. Please try again.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>Set Budget - Expense Tracker</title>
    <link rel="stylesheet" href="mycss.css">
    <link rel="icon" href="logo.png" type="image/x-icon"> 
    <style>
        .budget-form {
            max-width: 500px;
            margin: 0 auto;
        }
        .budget-info {
            margin-top: 30px;
            background-color: #f8f9fa;
            padding: 20px;
            border-radius: 5px;
        }
    </style>
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
                <a href="export_transactions.php" class="nav-link">Export Transactions</a>
            </li>
            <li class="nav-item">
                <a href="set_budget.php" class="nav-link active">Set Budget</a>
            </li>
            <li class="nav-item">
                <a href="Review_aboutus.php" class="nav-link">About & Feedback</a>
            </li>
        </ul>
        <a href="logout.php" class="sign-out">Sign Out</a>
    </div>

    <div class="main-content">
        <div class="content-wrapper">
            <h1>Set Budget</h1>
            <?php if ($success_message): ?>
                <div class="alert success"><?php echo $success_message; ?></div>
            <?php endif; ?>
            <?php if ($error_message): ?>
                <div class="alert error"><?php echo $error_message; ?></div>
            <?php endif; ?>
            <form action="set_budget.php" method="POST" class="budget-form">
                <div class="form-group">
                    <label for="monthly_budget">Monthly Budget</label>
                    <input type="number" id="monthly_budget" name="monthly_budget" value="<?php echo htmlspecialchars($monthly_budget); ?>" required>
                </div>
                <div class="form-group">
                    <label for="yearly_budget">Yearly Budget</label>
                    <input type="number" id="yearly_budget" name="yearly_budget" value="<?php echo htmlspecialchars($yearly_budget); ?>" required>
                </div>
                <button type="submit" class="btn-submit">Set Budget</button>
            </form>
            <div class="budget-info">
                <h2>Budget Information</h2>
                <p>Setting a budget helps you manage your expenses and stay on track with your financial goals.</p>
                <ul>
                    <li>Monthly Budget: Amount you plan to spend or save each month.</li>
                    <li>Yearly Budget: Overall budget for the year.</li>
                    <li>You can update your budget anytime.</li>
                    <li>Used for financial insights in the dashboard.</li>
                </ul>
            </div>
        </div>
    </div>
</body>
</html>
