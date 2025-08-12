<?php
session_start();
include("connection.php");

// Only allow admin (user_id = 971221)
if (!isset($_SESSION['user_id']) || $_SESSION['user_id'] != 971221) {
    header("Location: index.php");
    exit();
}

// Example: Change site name and description
if (isset($_POST['save_settings'])) {
    $site_name = mysqli_real_escape_string($con, $_POST['site_name']);
    $site_desc = mysqli_real_escape_string($con, $_POST['site_desc']);
    mysqli_query($con, "UPDATE system_settings SET setting_value='$site_name' WHERE setting_key='site_name'");
    mysqli_query($con, "UPDATE system_settings SET setting_value='$site_desc' WHERE setting_key='site_desc'");
    $success = "Settings updated!";
}

// Fetch current settings
$site_name = '';
$site_desc = '';
$res = mysqli_query($con, "SELECT * FROM system_settings WHERE setting_key IN ('site_name','site_desc')");
while ($row = mysqli_fetch_assoc($res)) {
    if ($row['setting_key'] == 'site_name') $site_name = $row['setting_value'];
    if ($row['setting_key'] == 'site_desc') $site_desc = $row['setting_value'];
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin Settings</title>
    <link href="https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css" rel="stylesheet">
    <link rel="stylesheet" href="CSS code/admin-dashboard.css">
    <style>
        body {background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);}
        .main-content {
            background: #fff;
            border-radius: 18px;
            box-shadow: 0 6px 32px #342e3720;
            padding: 36px 36px 28px 36px;
            margin: 36px 0;
            min-height: 80vh;
        }
        .settings-title {
            color: #3a7bd5;
            font-size: 2rem;
            font-weight: 700;
            margin-bottom: 24px;
            text-align: center;
        }
        .settings-form {
            max-width: 480px;
            margin: 0 auto;
            background: #f8faff;
            border-radius: 14px;
            box-shadow: 0 2px 8px #3a7bd510;
            padding: 2rem 2rem 1.5rem 2rem;
        }
        .settings-form label {
            font-weight: 600;
            color: #3a7bd5;
            margin-bottom: 8px;
            display: block;
        }
        .settings-form input, .settings-form textarea {
            width: 100%;
            padding: 12px 14px;
            border-radius: 8px;
            border: 1px solid #eaf3ff;
            margin-bottom: 18px;
            font-size: 1.08em;
            background: #fff;
            color: #222;
            transition: border 0.2s;
        }
        .settings-form input:focus, .settings-form textarea:focus {
            border-color: #3a7bd5;
            outline: none;
        }
        .settings-form button {
            background: linear-gradient(90deg,#3a7bd5 0%,#764ba2 100%);
            color: #fff;
            border: none;
            border-radius: 8px;
            padding: 12px 32px;
            font-size: 1.08em;
            font-weight: 600;
            cursor: pointer;
            box-shadow: 0 2px 8px #3a7bd522;
            transition: background 0.2s;
        }
        .settings-form button:hover {
            background: linear-gradient(90deg,#764ba2 0%,#3a7bd5 100%);
        }
        .success-msg {
            background: #eafbe7;
            color: #2e7d32;
            padding: 10px 18px;
            border-radius: 8px;
            margin-bottom: 16px;
            font-size: 1.08em;
            text-align: center;
            display: block;
        }
        @media (max-width: 700px) {
            .sidebar { display: none; }
            .main-content { padding: 12px 2vw; border-radius: 0; margin: 0; }
            .settings-form { padding: 1rem 0.5rem; }
        }
    </style>
</head>
<body>
    <div class="container" style="position:relative;">
        <div class="sidebar">
            <div class="logo" style="display: flex; align-items: center; justify-content: center; padding: 18px 0;">
                <img src="images/Logo-nobg.png" alt="BUMBTI Logo" class="logo-img" style="height:80px; width:80px; display:block;">
            </div>
            <div class="admin-badge">
                <i class="bx bx-shield"></i> Admin Panel
            </div>
            <ul class="nav-menu">
                <li class="nav-item">
                    <a href="admin-dashboard.php" class="nav-link">
                        <i class="bx bx-grid-alt nav-icon"></i>
                        Dashboard
                    </a>
                </li>
                <li class="nav-item">
                    <a href="admin-users.php" class="nav-link">
                        <i class="bx bx-user nav-icon"></i>
                        Users
                    </a>
                </li>
                <li class="nav-item">
                    <a href="admin-groups.php" class="nav-link">
                        <i class="bx bxs-group nav-icon"></i>
                        Groups
                    </a>
                </li>
                <li class="nav-item">
                    <a href="admin-messages.php" class="nav-link">
                        <i class="bx bx-message-dots nav-icon"></i>
                        Messages
                    </a>
                </li>
                <li class="nav-item">
                    <a href="admin-analytics.php" class="nav-link">
                        <i class="bx bx-brain nav-icon"></i>
                        MBTI Analytics
                    </a>
                </li>
                <li class="nav-item">
                    <a href="admin-reports.php" class="nav-link">
                        <i class="bx bx-bar-chart-alt-2 nav-icon"></i>
                        Reports
                    </a>
                </li>
                <li class="nav-item">
                    <a href="admin-settings.php" class="nav-link active">
                        <i class="bx bx-cog nav-icon"></i>
                        Settings
                    </a>
                </li>
                <li class="nav-item">
                    <a href="logout.php" class="nav-link">
                        <i class="bx bx-log-out nav-icon"></i>
                        Logout
                    </a>
                </li>
            </ul>
        </div>
        <div class="main-content">
            <div class="settings-title"><i class="bx bx-cog"></i> Site Settings</div>
            <?php if (!empty($success)): ?>
                <div class="success-msg"><?= htmlspecialchars($success) ?></div>
            <?php endif; ?>
            <form class="settings-form" method="post">
                <label for="site_name">Site Name</label>
                <input type="text" id="site_name" name="site_name" value="<?= htmlspecialchars($site_name) ?>" required>
                <label for="site_desc">Site Description</label>
                <textarea id="site_desc" name="site_desc" rows="3" required><?= htmlspecialchars($site_desc) ?></textarea>
                <button type="submit" name="save_settings"><i class="bx bx-save"></i> Save Settings</button>
            </form>
        </div>
    </div>
</body>
</html>