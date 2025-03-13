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
$username = $user['username'];

$success_message = $error_message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = $_POST['title'];
    $amount = $_POST['amount'];
    $description = $_POST['description'];
    $date = $_POST['date'];
    $category = $_POST['category'];
    if (mysqli_query($conn, "INSERT INTO expenses (user_id, title, amount, description, date, category) 
              VALUES ('$user_id', '$title', '$amount', '$description', '$date', '$category')")) {
        $success_message = "Expense added successfully!";
    } else {
        $error_message = "Error: " . mysqli_error($conn);
    }
}
?>

<!DOCTYPE html>
<head>
    <title>Add Expense - Expense Tracker</title>
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
                <a href="dashboard.php" class="nav-link">
                    Dashboard
                </a>
            </li>
            <li class="nav-item">
                <a href="add_income.php" class="nav-link">
                    Add Income
                </a>
            </li>
            <li class="nav-item">
                <a href="add_expense.php" class="nav-link active">
                    Add Expense
                </a>
            </li>
            <li class="nav-item">
                <a href="export_transactions.php" class="nav-link">
                    Export Transactions
                </a>
            </li>
            <li class="nav-item">
                <a href="set_budget.php" class="nav-link">
                    Set Budget
                </a>
            </li>
            <li class="nav-item">
                <a href="Review_aboutus.php" class="nav-link">
                    About & Feedback
                </a>
            </li>
        </ul>
        <a href="logout.php" class="sign-out">
            Sign Out
        </a>
    </div>

    <div class="main-content">
        <div class="content-wrapper">
            <h1>Add Expense</h1>
            <?php if ($success_message): ?>
                <div class="alert success"><?php echo $success_message; ?></div>
            <?php endif; ?>
            <?php if ($error_message): ?>
                <div class="alert error"><?php echo $error_message; ?></div>
            <?php endif; ?>
            <form action="add_expense.php" method="POST" class="add-form">
                <div class="form-group">
                    <label for="title">Title</label>
                    <input type="text" id="title" name="title" required>
                </div>
                <div class="form-group">
                    <label for="amount">Amount</label>
                    <input type="number" id="amount" name="amount" required>
                </div>
                <div class="form-group">
                    <label for="description">Description</label>
                    <input type="text" id="description" name="description">
                </div>
                <div class="form-group">
                    <label for="date">Date</label>
                    <input type="date" id="date" name="date" required>
                </div>
                <div class="form-group">
                    <label for="category">Category</label>
                    <input type="text" id="category" name="category" required>
                </div>
                <button type="submit" class="btn-submit">Add Expense</button>
            </form>
        </div>
    </div>
</body>
</html>
