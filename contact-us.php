<?php
session_start();
include("connection.php");

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

// Get user information
$user_query = "SELECT user_name, email, phone, banned_until FROM users WHERE user_id = '$user_id'";
$user_result = mysqli_query($con, $user_query);
$user = mysqli_fetch_assoc($user_result);

$message = '';

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $feedback_message = mysqli_real_escape_string($con, $_POST['message']);
    
    if (!empty($feedback_message)) {
        $insert_query = "INSERT INTO feedback (user_id, message) VALUES ('$user_id', '$feedback_message')";
        
        if (mysqli_query($con, $insert_query)) {
            $message = "Thank you for your feedback! We'll get back to you soon.";
        } else {
            $message = "Error sending feedback. Please try again.";
        }
    } else {
        $message = "Please enter your message.";
    }
}

// Check if user is banned
if (!empty($user['banned_until']) && strtotime($user['banned_until']) > time()) {
    $ban_time = date('d M Y H:i', strtotime($user['banned_until']));
    echo "<div style='background:#ffeaea;color:#DB504A;padding:24px 32px;border-radius:16px;margin:64px auto 0 auto;max-width:440px;text-align:center;font-size:1.18em;font-weight:600;box-shadow:0 4px 18px #DB504A22;'>
        <i class='bx bxs-error' style='font-size:2.4em;vertical-align:middle;'></i>
        <div style='margin:18px 0 8px 0;'>You are banned until <span style='color:#b92d23;'>$ban_time</span>.</div>
        <div style='font-size:0.98em;font-weight:400;margin-bottom:18px;'>Please contact support if you believe this is a mistake.</div>
        <button onclick=\"window.location.href='login.php'\" style='background:linear-gradient(135deg,#DB504A 0%,#b92d23 100%);color:#fff;border:none;border-radius:10px;padding:12px 38px;font-size:1.08em;font-weight:600;cursor:pointer;box-shadow:0 2px 8px #DB504A22;transition:background 0.2s;'>
            OK
        </button>
    </div>";
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>TypeToWork Contact Us</title>
    <link rel="stylesheet" href="CSS code\contact-us.css">
    <link rel="stylesheet" href="CSS code\index.css">
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
</head>
<body>
    <!-- Sidebar Menu -->
    <div id="sidebar" class="sidebar">
        <a href="index.php" class="brand">
            <img src="images/Logo-nobg.png" alt="Logo">
            
        </a>
        <ul class="sidebar-menu">
            <li><a href="index.php"><i class="bx bx-home"></i>Main</a></li>
            <li><a href="group.php"><i class="bx bxs-group"></i>My Group</a></li>
            <li><a href="about.php"><i class="bx bxs-group"></i>About</a></li>
            <li><a href="contact-us.php"><i class="bx bxs-envelope"></i>Contact us</a></li>
            <li><a href="profile.php"><i class="bx bx-user"></i>Profile</a></li>
            <li><a href="logout.php"><i class="bx bx-log-out"></i>Logout</a></li>
        </ul>
    </div>

    <div class="contact-main">
        <div class="item">
            <div class="contact">
                <div class="frist-text">Let's get in touch</div>
                <img src="pf_image/contact-us-img.png" alt="" class="image">
                <div class="social-link">
                    <span class="secnd-text">Connect with us :</span>
                </div>
            </div>
            <div class="submit-form">
                <h4 class="thrid-text text-con">Contact Us</h4>
                
                <?php if ($message): ?>
                    <div class="message <?= strpos($message, 'Error') !== false ? 'error' : 'success' ?>">
                        <?= $message ?>
                    </div>
                <?php endif; ?>

                <!-- Display user info (read-only) -->
                <div class="user-info">
                    <p><strong>Name:</strong> <?= htmlspecialchars($user['user_name']) ?></p>
                    <p><strong>Email:</strong> <?= htmlspecialchars($user['email']) ?></p>
                    <p><strong>Phone:</strong> <?= htmlspecialchars($user['phone']) ?></p>
                </div>

                <form method="POST">
                    <div class="input-box-x">
                        <textarea name="message" class="input" required id="message" cols="40" rows="10"></textarea>
                        <label for="message">Message</label>
                    </div>
                    
                    <button type="submit" class="btn-submit-con">Send Feedback</button>
                </form>
            </div>
        </div>
    </div>
</body>
</html>