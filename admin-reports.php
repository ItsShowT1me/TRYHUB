<?php
session_start();
include("connection.php");

// Only allow admin (user_id = 971221)
if (!isset($_SESSION['user_id']) || $_SESSION['user_id'] != 971221) {
    header("Location: index.php");
    exit();
}

// Handle delete feedback
if (isset($_POST['delete_feedback']) && isset($_POST['feedback_id'])) {
    $fid = intval($_POST['feedback_id']);
    mysqli_query($con, "DELETE FROM feedback WHERE id='$fid'");
    header("Location: admin-reports.php");
    exit();
}

// Fetch feedback messages
$feedbacks = [];
$res = mysqli_query($con, "SELECT f.*, u.user_name, u.mbti, u.image FROM feedback f LEFT JOIN users u ON f.user_id = u.user_id ORDER BY f.created_at DESC");
while ($row = mysqli_fetch_assoc($res)) {
    $feedbacks[] = $row;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin Feedback Report</title>
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
        .report-title {
            color: #3a7bd5;
            font-size: 2rem;
            font-weight: 700;
            margin-bottom: 24px;
            text-align: center;
        }
        .feedback-list {
            margin-top: 24px;
        }
        .feedback-item {
            display: flex;
            align-items: flex-start;
            gap: 18px;
            background: #f8faff;
            border-radius: 12px;
            box-shadow: 0 2px 8px #3a7bd510;
            padding: 18px 16px;
            margin-bottom: 18px;
            border: 2px solid #eaf3ff;
            position: relative;
        }
        .feedback-avatar {
            width: 54px;
            height: 54px;
            border-radius: 50%;
            background: #3a7bd5;
            color: #fff;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: bold;
            font-size: 1.5em;
            object-fit: cover;
            border: 3px solid #eaf3ff;
            box-shadow: 0 2px 8px #3a7bd510;
        }
        .feedback-content {
            flex: 1;
        }
        .feedback-user {
            font-weight: 700;
            color: #3a7bd5;
            font-size: 1.08em;
        }
        .feedback-mbti {
            background: #eaf3ff;
            color: #3a7bd5;
            padding: 2px 10px;
            border-radius: 8px;
            font-size: 0.98em;
            margin-left: 8px;
        }
        .feedback-date {
            color: #888;
            font-size: 0.98em;
            margin-left: 12px;
        }
        .feedback-message {
            margin-top: 8px;
            color: #222;
            font-size: 1.08em;
            line-height: 1.6;
            word-break: break-word;
        }
        .delete-feedback-btn {
            position: absolute;
            top: 14px;
            right: 14px;
            background: linear-gradient(90deg,#DB504A 0%,#b92d23 100%);
            color: #fff;
            border: none;
            border-radius: 8px;
            padding: 6px 16px;
            font-size: 1em;
            font-weight: 600;
            cursor: pointer;
            box-shadow: 0 2px 8px #DB504A22;
            transition: background 0.2s;
        }
        .delete-feedback-btn:hover {
            background: linear-gradient(90deg,#b92d23 0%,#DB504A 100%);
        }
        @media (max-width: 900px) {
            .main-content { padding: 14px 4px; }
            .feedback-item { gap: 8px; }
        }
        @media (max-width: 700px) {
            .main-content { padding: 12px 2vw; }
            .feedback-item { flex-direction: column; gap: 8px; }
            .feedback-avatar { width: 38px; height: 38px; font-size: 1em; }
            .delete-feedback-btn { top: 8px; right: 8px; padding: 4px 10px; font-size: 0.92em;}
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
                    <a href="admin-reports.php" class="nav-link active">
                        <i class="bx bx-bar-chart-alt-2 nav-icon"></i>
                        Reports
                    </a>
                </li>
                <li class="nav-item">
                    <a href="admin-settings.php" class="nav-link">
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
            <div class="report-title"><i class="bx bx-bar-chart-alt"></i> Feedback Report</div>
            <div class="feedback-list">
                <?php if (count($feedbacks) === 0): ?>
                    <div style="color:#aaa;text-align:center;font-size:1.1em;">No feedback messages found.</div>
                <?php else: ?>
                    <?php foreach ($feedbacks as $fb): ?>
                        <div class="feedback-item">
                            <?php if (!empty($fb['image'])): ?>
                                <img src="<?= htmlspecialchars($fb['image']) ?>" class="feedback-avatar" alt="Avatar">
                            <?php else: ?>
                                <div class="feedback-avatar"><?= strtoupper($fb['user_name'][0] ?? '?') ?></div>
                            <?php endif; ?>
                            <div class="feedback-content">
                                <div>
                                    <span class="feedback-user"><?= htmlspecialchars($fb['user_name'] ?? 'Unknown') ?></span>
                                    <?php if (!empty($fb['mbti'])): ?>
                                        <span class="feedback-mbti"><?= htmlspecialchars($fb['mbti']) ?></span>
                                    <?php endif; ?>
                                    <span class="feedback-date"><?= date('d/m/Y H:i', strtotime($fb['created_at'])) ?></span>
                                </div>
                                <div class="feedback-message"><?= nl2br(htmlspecialchars($fb['message'])) ?></div>
                            </div>
                            <form method="post" style="margin:0;">
                                <input type="hidden" name="feedback_id" value="<?= $fb['id'] ?>">
                                <button type="submit" name="delete_feedback" class="delete-feedback-btn" onclick="return confirm('Delete this feedback?');">
                                    <i class="bx bx-trash"></i> Delete
                                </button>
                            </form>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>
    </div>
</body>
</html>