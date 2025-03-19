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
$result_budget = mysqli_query($conn, "SELECT monthly_budget, yearly_budget FROM budget WHERE user_id = $user_id");
$budget_data = mysqli_fetch_assoc($result_budget);
$monthly_budget = $budget_data['monthly_budget'] ?? 0;
$yearly_budget = $budget_data['yearly_budget'] ?? 0;
$daily_budget = $monthly_budget / 30; // Approximate daily budget based on monthly

$query = "SELECT date, 'income' as type, amount FROM income WHERE user_id = $user_id
          UNION ALL
          SELECT date, 'expense' as type, amount FROM expenses WHERE user_id = $user_id
          ORDER BY date DESC";
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
        background: White;
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

    .budget-stats {
        display: flex;
        justify-content: space-around;
        align-items: center;
        gap: 20px;
        margin-top: 20px;
        flex-wrap: wrap;
    }

    .budget-card {
        background: White;
        padding: 20px;
        border-radius: 20px;
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        text-align: center;
        width: 250px;
        flex: 1;
        min-width: 200px;
    }

    .budget-card h3 {
        font-size: 18px;
        font-weight: bold;
        margin-bottom: 10px;
    }

    .budget-card p {
        font-size: 24px;
        font-weight: bold;
    }

    .budget-card .remaining {
        font-size: 16px;
        margin-top: 10px;
    }

    .positive {
        color: green;
    }

    .negative {
        color: red;
    }

    .chart-container {
        margin-top: 30px;
        background: white;
        padding: 20px;
        border-radius: 20px;
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        height: 400px;
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
        <a href="logout.php" class="sign-out">Sign Out</a>
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

            <!-- Budget Stats Section -->
            <div class="budget-stats">
                <div class="budget-card">
                    <h3>Monthly Budget</h3>
                    <p>₹<?php echo number_format($monthly_budget, 2); ?></p>
                    <?php 
                    $remaining_monthly = $monthly_budget - $total_expense;
                    $class = $remaining_monthly >= 0 ? 'positive' : 'negative';
                    ?>
                    <div class="remaining <?php echo $class; ?>">
                        Remaining: ₹<?php echo number_format(abs($remaining_monthly), 2); ?>
                        <?php echo $remaining_monthly >= 0 ? '(Under budget)' : '(Over budget)'; ?>
                    </div>
                </div>
                <div class="budget-card">
                    <h3>Yearly Budget</h3>
                    <p>₹<?php echo number_format($yearly_budget, 2); ?></p>
                    <?php 
                    $remaining_yearly = $yearly_budget - $total_expense;
                    $class = $remaining_yearly >= 0 ? 'positive' : 'negative';
                    ?>
                    <div class="remaining <?php echo $class; ?>">
                        Remaining: ₹<?php echo number_format(abs($remaining_yearly), 2); ?>
                        <?php echo $remaining_yearly >= 0 ? '(Under budget)' : '(Over budget)'; ?>
                    </div>
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

        // Budget values
        var monthlyBudget = <?php echo $monthly_budget; ?>;
        var yearlyBudget = <?php echo $yearly_budget; ?>;
        var dailyBudget = <?php echo $daily_budget; ?>;
        
        // Create arrays of budget values for each date
        var monthlyBudgetLine = Array(dates.length).fill(dailyBudget);
        var yearlyBudgetLine = Array(dates.length).fill(yearlyBudget / 365); // Daily equivalent of yearly budget

        const ctx = document.getElementById('transactionChart').getContext('2d');
        new Chart(ctx, {
            type: 'line',
            data: {
                labels: dates,
                datasets: [
                    {
                        label: 'Income',
                        data: incomes,
                        borderColor: 'green',
                        backgroundColor: 'rgba(0, 128, 0, 0.1)',
                        fill: false,
                        tension: 0.1
                    }, 
                    {
                        label: 'Expense',
                        data: expenses,
                        borderColor: 'red',
                        backgroundColor: 'rgba(255, 0, 0, 0.1)',
                        fill: false,
                        tension: 0.1
                    },
                    {
                        label: 'Daily Budget (Monthly)',
                        data: monthlyBudgetLine,
                        borderColor: 'rgba(75, 192, 192, 1)',
                        borderDash: [5, 5],
                        fill: false,
                        pointRadius: 0
                    },
                    {
                        label: 'Daily Budget (Yearly)',
                        data: yearlyBudgetLine,
                        borderColor: 'rgba(153, 102, 255, 1)',
                        borderDash: [10, 5],
                        fill: false,
                        pointRadius: 0
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    title: {
                        display: true,
                        text: 'Income, Expense and Budget Trend',
                        font: {
                            size: 16
                        }
                    },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                let label = context.dataset.label || '';
                                if (label) {
                                    label += ': ';
                                }
                                if (context.parsed.y !== null) {
                                    label += '₹' + context.parsed.y.toFixed(2);
                                }
                                return label;
                            }
                        }
                    },
                    legend: {
                        position: 'bottom'
                    }
                },
                scales: {
                    x: {
                        title: {
                            display: true,
                            text: 'Date'
                        },
                        reverse: true
                    },
                    y: {
                        title: {
                            display: true,
                            text: 'Amount (₹)'
                        },
                        beginAtZero: true
                    }
                }
            }
        });
    </script>
</body>
</html>