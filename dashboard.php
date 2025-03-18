<?php
session_start();
require_once 'db_connect.php';

if (!isset($_SESSION['userId'])) {
    header('Location: index.php');
    exit();
}

$user_id = $_SESSION['userId'];

$result_user = mysqli_query($conn, "SELECT username FROM users WHERE id = $user_id");
$user_data = mysqli_fetch_assoc($result_user);
$username = $user_data['username']; 

$result_income = mysqli_query($conn, "SELECT SUM(amount) as total_income FROM income WHERE user_id = $user_id");
$total_income = mysqli_fetch_assoc($result_income)['total_income'];

$result_expense = mysqli_query($conn, "SELECT SUM(amount) as total_expense FROM expenses WHERE user_id = $user_id");
$total_expense = mysqli_fetch_assoc($result_expense)['total_expense'];

$query = "SELECT date, 'income' as type, amount FROM income WHERE user_id = $user_id
          UNION ALL
          SELECT date, 'expense' as type, amount FROM expenses WHERE user_id = $user_id
          ORDER BY date DESC LIMIT 10";
$result = mysqli_query($conn, $query);
$transactions = mysqli_fetch_all($result, MYSQLI_ASSOC);
?>

<!DOCTYPE html>
<head>
    <title>Expense Tracker - Dashboard</title>
    <link rel="icon" href="logo.png" type="image/x-icon"> 
    <link rel="stylesheet" href="mycss.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
    .stats-container {
    display: flex;
    justify-content: space-around; 
    align-items: center; 
    gap: 20px; 
    margin-top: 20px;
    flex-wrap: wrap; 
}

.stat-card {
    background:White;
    padding: 20px;
    border-radius: 20px; 
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1); 
    text-align: center;
    width: 250px; 
    flex: 1; 
    min-width: 200px; 
}

.stat-card h3 {
    font-size: 18px;
    font-weight: bold;
    margin-bottom: 10px;
}

.stat-card p {
    font-size: 24px;
    font-weight: bold;
    color: #333;
}

    </style>
</head>
<body>
    <div class="sidebar">
        <div class="profile-section">
            <div class="user-name"><?php echo htmlspecialchars($username); ?></div> 
        </div>
        <ul class="nav-menu">
            <li class="nav-item"><a href="dashboard.php" class="nav-link active">Dashboard</a></li>
            <li class="nav-item"><a href="add_income.php" class="nav-link">Add Income</a></li>
            <li class="nav-item"><a href="add_expense.php" class="nav-link">Add Expense</a></li>
            <li class="nav-item"><a href="export_transactions.php" class="nav-link">Export Transactions</a></li>
            <li class="nav-item"><a href="set_budget.php" class="nav-link">Set Budget</a></li>
            <li class="nav-item"><a href="Review_aboutus.php" class="nav-link">About & Feedback</a></li>
        </ul>
        <a href="logout.php" class="sign-out"><i class="fas fa-sign-out-alt"></i> Sign Out</a>
    </div>

    <div class="main-content">
        <div class="content-wrapper">
            <h1>Dashboard</h1>
            <div class="stats-container">
                <div class="stat-card">
                    <h3>Total Income</h3>
                    <p>₹<?php echo number_format($total_income, 2); ?></p>
                </div>
                <div class="stat-card">
                    <h3>Total Expense</h3>
                    <p>₹<?php echo number_format($total_expense, 2); ?></p>
                </div>
                <div class="stat-card">
                    <h3>Balance</h3>
                    <p>₹<?php echo number_format($total_income - $total_expense, 2); ?></p>
                </div>
            </div>
            <div class="chart-container">
                <canvas id="transactionChart"></canvas>
            </div>
        </div>
    </div>

    <script>
        var dates = [<?php
            $dateArray = array_map(fn($t) => "'" . $t['date'] . "'", $transactions);
            echo implode(',', $dateArray);
        ?>];
        var incomes = [<?php
            $incomeArray = array_map(fn($t) => $t['type'] == 'income' ? $t['amount'] : 0, $transactions);
            echo implode(',', $incomeArray);
        ?>];
        var expenses = [<?php
            $expenseArray = array_map(fn($t) => $t['type'] == 'expense' ? $t['amount'] : 0, $transactions);
            echo implode(',', $expenseArray);
        ?>];

        const ctx = document.getElementById('transactionChart').getContext('2d');
        new Chart(ctx, {
            type: 'line',
            data: {
                labels: dates,
                datasets: [{
                    label: 'Income',
                    data: incomes,
                    borderColor: 'green',
                    fill: false
                }, {
                    label: 'Expense',
                    data: expenses,
                    borderColor: 'red',
                    fill: false
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    title: {
                        display: true,
                        text: 'Income and Expense Trend'
                    }
                },
                scales: {
                    x: {
                        title: {
                            display: true,
                            text: 'Date'
                        }
                    },
                    y: {
                        title: {
                            display: true,
                            text: 'Amount'
                        }
                    }
                }
            }
        });
    </script>
</body>
</html>
