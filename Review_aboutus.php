<?php
session_start();
require_once 'db_connect.php';
if (!isset($_SESSION['userId'])) {
    header('Location: login.php');
    exit();
}
$user_id = $_SESSION['userId'];
$query = "SELECT username FROM users WHERE id = $user_id";
$result = mysqli_query($conn, $query);
$user = mysqli_fetch_assoc($result);
$username = $user['username'];
$success_message = $error_message = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $description = $_POST['description'];
    $check_query = "SELECT user_id FROM feedback WHERE user_id = $user_id";
    $check_result = mysqli_query($conn, $check_query);
    
    if (mysqli_num_rows($check_result) > 0) {
        $update_query = "UPDATE feedback SET description = '$description' WHERE user_id = $user_id";
        
        if (mysqli_query($conn, $update_query)) {
            $success_message = "Your feedback has been updated successfully!";
        } else {
            $error_message = "Error updating your feedback. Please try again.";
        }
    } else {
        $insert_query = "INSERT INTO feedback (user_id, description) VALUES ($user_id, '$description')";
        
        if (mysqli_query($conn, $insert_query)) {
            $success_message = "Thank you for your feedback!";
        } else {
            $error_message = "Error submitting your feedback. Please try again.";
        }
    }
}
$user_feedback = null;
$feedback_query = "SELECT description FROM feedback WHERE user_id = $user_id";
$feedback_result = mysqli_query($conn, $feedback_query);
if (mysqli_num_rows($feedback_result) > 0) {
    $user_feedback = mysqli_fetch_assoc($feedback_result);
}
$all_feedback_query = "SELECT f.description, u.username 
                      FROM feedback f 
                      JOIN users u ON f.user_id = u.id 
                      ORDER BY u.username";
$all_feedback = mysqli_query($conn, $all_feedback_query);
$all_feedback_data = mysqli_fetch_all($all_feedback, MYSQLI_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>About Us & Feedback - Expense Tracker</title>
    <link rel="icon" href="logo.png" type="image/x-icon"> 
    <link rel="stylesheet" href="mycss.css">
    <style>
        .page-container {
            max-width: 800px;
            margin: 0 auto;
        }
        .section {
            margin-bottom: 40px;
            padding: 30px;
            background: #fff;
            border-radius: 10px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }
        .section-title {
            font-size: 1.8rem;
            color: #333;
            margin-bottom: 20px;
            padding-bottom: 10px;
            border-bottom: 2px solid #4CAF50;
        }
        .feature-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 20px;
            margin: 30px 0;
        }
        .feature-card {
            background: #f8f9fa;
            padding: 20px;
            border-radius: 10px;
            text-align: center;
        }
        .feature-card i {
            font-size: 2rem;
            color: #4CAF50;
            margin-bottom: 15px;
        }
        .feature-card h3 {
            margin-bottom: 10px;
            color: #333;
        }
        .contact-info {
            background: #f8f9fa;
            padding: 20px;
            border-radius: 10px;
            margin-top: 30px;
        }
        .contact-info h2 {
            margin-bottom: 15px;
        }
        .contact-info p {
            margin-bottom: 10px;
        }
        .feedback-form textarea {
            width: 100%;
            min-height: 150px;
            padding: 12px;
            border: 1px solid #ddd;
            border-radius: 5px;
            margin-bottom: 20px;
            resize: vertical;
            font-family: inherit;
        }
        .btn-submit {
            background-color: #4caf50;
            color: white;
            padding: 12px 20px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 16px;
            width: 100%;
            transition: background-color 0.3s;
        }
        .btn-submit:hover {
            background-color: #45a049;
        }
        .alert {
            padding: 15px;
            margin-bottom: 20px;
            border-radius: 5px;
        }
        .success {
            background-color: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }
        .error {
            background-color: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }
        .feedback-list {
            margin-top: 30px;
        }
        .feedback-item {
            background: #f8f9fa;
            padding: 15px;
            border-radius: 5px;
            margin-bottom: 15px;
        }
        .feedback-header {
            display: flex;
            justify-content: space-between;
            margin-bottom: 10px;
        }
        .feedback-user {
            font-weight: bold;
            color: #333;
        }
        .feedback-content {
            color: #555;
            line-height: 1.5;
        }
        .team-section {
            text-align: center;
            margin: 30px 0;
        }
        .team-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 20px;
            margin-top: 20px;
        }
        .team-member {
            background: #f8f9fa;
            padding: 20px;
            border-radius: 10px;
        }
        .team-member img {
            width: 100px;
            height: 100px;
            border-radius: 50%;
            object-fit: cover;
            margin-bottom: 10px;
        }
        .mission-section {
            background: #e8f5e9;
            padding: 20px;
            border-radius: 10px;
            margin-bottom: 30px;
            text-align: center;
        }
        .mission-section h2 {
            color: #2e7d32;
            margin-bottom: 15px;
        }
        .no-feedback {
            text-align: center;
            padding: 20px;
            background: #f8f9fa;
            border-radius: 5px;
            color: #666;
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
                <a href="about_feedback.php" class="nav-link active">
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
            <div class="page-container">
                <div class="section">
                    <h2 class="section-title">About Our Project</h2>
                    
                    <div class="feature-grid">
                        <div class="feature-card">
                            <h3>Expense Tracking</h3>
                            <p>Easily record and categorize your daily expenses to understand your spending habits.</p>
                        </div>
                        <div class="feature-card">
                            <h3>Income Management</h3>
                            <p>Keep track of all your income sources in one place for a complete financial overview.</p>
                        </div>
                        <div class="feature-card">
                            <h3>Budget Planning</h3>
                            <p>Set monthly and yearly budgets to help you stay on track with your financial goals.</p>
                        </div>
                        <div class="feature-card">
                            <h3>Data Export</h3>
                            <p>Export your financial data for further analysis or record-keeping.</p>
                        </div>
                    </div>

                    <div class="contact-info">
                        <h2>Contact Us</h2>
                        <p> Email: admin@expensetracker.com</p>
                        <p> Phone: +91 931 686 8311</p>
                        <p> Address: Shaitan Gali, Khatara Mahal, Andhernagar aur samsan k samna</p>
                    </div>
                </div>

                <!-- Feedback Section -->
                <div class="section">
                    <h2 class="section-title">User Feedback</h2>
                    
                    <?php if ($success_message): ?>
                        <div class="alert success"><?php echo $success_message; ?></div>
                    <?php endif; ?>
                    
                    <?php if ($error_message): ?>
                        <div class="alert error"><?php echo $error_message; ?></div>
                    <?php endif; ?>
                    
                    <div class="feedback-form">
                        <h3><?php echo $user_feedback ? 'Update Your Feedback' : 'Share Your Feedback'; ?></h3>
                        <form action="Review_aboutus.php" method="POST">
                            <textarea name="description" placeholder="We value your feedback! Please share your thoughts, suggestions, or experiences with our Expense Tracker..." required><?php echo $user_feedback ? htmlspecialchars($user_feedback['description']) : ''; ?></textarea>
                            <button type="submit" class="btn-submit"><?php echo $user_feedback ? 'Update Feedback' : 'Submit Feedback'; ?></button>
                        </form>
                    </div>
                    
                    <div class="feedback-list">
                        <h3>What Our Users Say</h3>
                        
                        <?php if (empty($all_feedback)): ?>
                            <div class="no-feedback">
                                <p>No feedback yet. Be the first to share your thoughts!</p>
                            </div>
                        <?php else: ?>
                            <?php foreach ($all_feedback as $feedback): ?>
                                <div class="feedback-item">
                                    <div class="feedback-header">
                                        <span class="feedback-user"><?php echo htmlspecialchars($feedback['username']); ?></span>
                                    </div>
                                    <div class="feedback-content">
                                        <?php echo htmlspecialchars($feedback['description']); ?>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>