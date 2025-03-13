<?php
session_start();
require_once 'db_connect.php';

if (!isset($_SESSION['userId'])) {
    header('Location: index.php');
    exit();
}

$user_id = $_SESSION['userId'];
$result = mysqli_query($conn, "SELECT username FROM users WHERE id = $user_id");
$user = mysqli_fetch_assoc($result);
$username = $user['username'];

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $title = $_POST['title'];
    $amount = $_POST['amount'];
    $description = $_POST['description'];
    $date = $_POST['date'];
    $category = $_POST['category'];
    if (mysqli_query($conn, "INSERT INTO income (user_id, title, amount, description, date, category) 
              VALUES ('$user_id', '$title', '$amount', '$description', '$date', '$category')")) {
        $success_message = "Income added successfully!";
    } else {
        $error_message = "Error: " . mysqli_error($conn);
    }
}
?>

<!DOCTYPE html>
<head>
    <title>Add Income - Expense Tracker</title>
    <link rel="icon" href="logo.png" type="image/x-icon"> 
    <link rel="stylesheet" href="mycss.css">
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
                <a href="add_income.php" class="nav-link active">
                    Add Income
                </a>
            </li>
            <li class="nav-item">
                <a href="add_expense.php" class="nav-link">
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
            <h1>Add Income</h1>
            <?php
            if (isset($success_message)) {
                echo "<div class='alert success'>$success_message</div>";
            }
            if (isset($error_message)) {
                echo "<div class='alert error'>$error_message</div>";
            }
            ?>
            <form action="add_income.php" method="POST" class="add-form">
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
                <button type="submit" class="btn-submit">Add Income</button>
            </form>
        </div>
    </div>
</body>
</html>
