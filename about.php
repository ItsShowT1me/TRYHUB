<?php
session_start(); // <-- Always first!
include 'function.php';
include 'connection.php';

$user_data = check_login($con);

// Check if user is banned
if (!empty($user_data['banned_until']) && strtotime($user_data['banned_until']) > time()) {
    $ban_time = date('d M Y H:i', strtotime($user_data['banned_until']));
    echo "<div style='background:#ffeaea;color:#DB504A;padding:18px 24px;border-radius:12px;margin:32px auto;max-width:420px;text-align:center;font-size:1.15em;font-weight:600;box-shadow:0 2px 12px #DB504A22;'>
        <i class='bx bxs-error' style='font-size:2em;vertical-align:middle;'></i>
        <br>
        You are banned until <span style='color:#b92d23;'>$ban_time</span>.
        <br>
        Please contact support if you believe this is a mistake.<br><br>
        <button onclick=\"window.location.href='login.php'\" style='background:#DB504A;color:#fff;border:none;border-radius:8px;padding:10px 32px;font-size:1em;font-weight:600;cursor:pointer;box-shadow:0 2px 8px #DB504A22;'>OK</button>
    </div>";
    exit();
}


?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>About Us - TypeToWork News</title>
    <link rel="stylesheet" href="CSS code/index.css">
    <link rel="stylesheet" href="CSS code/about.css">
    <link href="https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css" rel="stylesheet">

</head>
<body>

    <!-- Sidebar Menu -->
    <nav id="sidebar">
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
    </nav>


    <section class="about-hero">
        <div class="container">
            <h1>About TypeHUB News</h1>
            <p>Your trusted source for MBTI news, insights, and community.</p>
        </div>
    </section>
    <section class="about-mission">
        <div class="container">
            <h2>Our Mission</h2>
            <p>TypeHub aims to enhance efficiency and elevate the team collaboration experience by using the Myers–Briggs Type Indicator (MBTI) as its primary tool for assessing personalities. This enables the platform to match individuals with similar or compatible personality types, ensuring the formation of well-suited advisory teams. By considering personality similarities in team formation, TypeHub reduces potential conflicts arising from differences in perspectives, fosters smoother and more effective communication, and directly supports the achievement of users’ goals. Ultimately, this approach enhances satisfaction and cultivates a positive, long-term collaborative experience.</p>
        </div>
    </section>
    <section class="about-team">
        <div class="container">
            <h2>Meet the Team</h2>
            <div class="team-members">
                <div class="team-member">
                    <img src="pf_image/Kaowpan.png" alt="Team Member 1">
                    <h3>Pratchaya Camsrisuk</h3>
                    <p>Front end developer & Website Design</p>
                </div>
                <div class="team-member">
                    <img src="pf_image/Oak.png" alt="Team Member 2">
                    <h3>Narakorn Tessakool</h3>
                    <p>Back end developer</p>
                </div>
                <div class="team-member">
                    <img src="pf_image/Kong.jpg" alt="Team Member 3">
                    <h3>Thananchai Moonjaroen </h3>
                    <p>Website Design & Paper Work</p>
                </div>
            </div>
        </div>
    </section>
    <section class="about-contact">
        <div class="container">
            <h2>Contact Us</h2>
            <p>Have questions or feedback? <a href="contact-us.php">Reach out here</a>.</p>
        </div>
    </section>

</body>
</html>