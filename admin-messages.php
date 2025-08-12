<?php
session_start();
include("connection.php");

// Only allow admin (user_id = 971221)
if (!isset($_SESSION['user_id']) || $_SESSION['user_id'] != 971221) {
    header("Location: index.php");
    exit();
}

// Handle delete message
if (isset($_POST['delete_message']) && isset($_POST['message_id'])) {
    $mid = intval($_POST['message_id']);
    mysqli_query($con, "DELETE FROM messages WHERE id='$mid'");
    header("Location: admin-messages.php");
    exit();
}

// Fetch messages with user and group info
$messages = [];
$res = mysqli_query($con, "
    SELECT m.*, u.user_name, u.mbti, u.image AS user_image, g.name AS group_name
    FROM messages m
    LEFT JOIN users u ON m.user_id = u.user_id
    LEFT JOIN groups g ON m.group_id = g.id
    ORDER BY m.created_at DESC
    LIMIT 100
");
while ($row = mysqli_fetch_assoc($res)) {
    $messages[] = $row;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin Messages</title>
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
        .messages-title {
            color: #3a7bd5;
            font-size: 2rem;
            font-weight: 700;
            margin-bottom: 24px;
            text-align: center;
        }
        .message-list {
            margin-top: 24px;
        }
        .message-item {
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
        .message-avatar {
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
        .message-content {
            flex: 1;
        }
        .message-user {
            font-weight: 700;
            color: #3a7bd5;
            font-size: 1.08em;
        }
        .message-mbti {
            background: #eaf3ff;
            color: #3a7bd5;
            padding: 2px 10px;
            border-radius: 8px;
            font-size: 0.98em;
            margin-left: 8px;
        }
        .message-group {
            background: #c3e6ff;
            color: #3a7bd5;
            padding: 2px 10px;
            border-radius: 8px;
            font-size: 0.98em;
            margin-left: 8px;
        }
        .message-date {
            color: #888;
            font-size: 0.98em;
            margin-left: 12px;
        }
        .message-text {
            margin-top: 8px;
            color: #222;
            font-size: 1.08em;
            line-height: 1.6;
            word-break: break-word;
        }
        .message-file {
            margin-top: 8px;
        }
        .message-file img {
            max-width: 180px;
            max-height: 180px;
            border-radius: 10px;
            box-shadow: 0 2px 8px #3a7bd510;
        }
        .message-file a {
            color: #3a7bd5;
            font-weight: 600;
            text-decoration: underline;
        }
        .delete-message-btn {
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
        .delete-message-btn:hover {
            background: linear-gradient(90deg,#b92d23 0%,#DB504A 100%);
        }
        @media (max-width: 900px) {
            .main-content { padding: 14px 4px; }
            .message-item { gap: 8px; }
        }
        @media (max-width: 700px) {
            .main-content { padding: 12px 2vw; }
            .message-item { flex-direction: column; gap: 8px; }
            .message-avatar { width: 38px; height: 38px; font-size: 1em; }
            .delete-message-btn { top: 8px; right: 8px; padding: 4px 10px; font-size: 0.92em;}
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
                    <a href="admin-messages.php" class="nav-link active">
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
            <div class="messages-title"><i class="bx bx-message-dots"></i> Messages</div>
            <div class="message-list">
                <?php if (count($messages) === 0): ?>
                    <div style="color:#aaa;text-align:center;font-size:1.1em;">No messages found.</div>
                <?php else: ?>
                    <?php foreach ($messages as $msg): ?>
                        <div class="message-item">
                            <?php if (!empty($msg['user_image'])): ?>
                                <img src="<?= htmlspecialchars($msg['user_image']) ?>" class="message-avatar" alt="Avatar">
                            <?php else: ?>
                                <div class="message-avatar"><?= strtoupper($msg['user_name'][0] ?? '?') ?></div>
                            <?php endif; ?>
                            <div class="message-content">
                                <div>
                                    <span class="message-user"><?= htmlspecialchars($msg['user_name'] ?? 'Unknown') ?></span>
                                    <?php if (!empty($msg['mbti'])): ?>
                                        <span class="message-mbti"><?= htmlspecialchars($msg['mbti']) ?></span>
                                    <?php endif; ?>
                                    <?php if (!empty($msg['group_name'])): ?>
                                        <span class="message-group"><?= htmlspecialchars($msg['group_name']) ?></span>
                                    <?php endif; ?>
                                    <span class="message-date"><?= date('d/m/Y H:i', strtotime($msg['created_at'])) ?></span>
                                </div>
                                <div class="message-text"><?= nl2br(htmlspecialchars($msg['message'])) ?></div>
                                <?php if (!empty($msg['file_path'])): ?>
                                    <div class="message-file">
                                        <?php
                                            $ext = strtolower(pathinfo($msg['file_path'], PATHINFO_EXTENSION));
                                            if (in_array($ext, ['jpg','jpeg','png','gif','webp'])) {
                                                echo '<img src="'.htmlspecialchars($msg['file_path']).'" alt="Attachment">';
                                            } else {
                                                echo '<a href="'.htmlspecialchars($msg['file_path']).'" target="_blank"><i class="bx bx-file"></i> Download file</a>';
                                            }
                                        ?>
                                    </div>
                                <?php endif; ?>
                            </div>
                            <form method="post" style="margin:0;">
                                <input type="hidden" name="message_id" value="<?= $msg['id'] ?>">
                                <button type="submit" name="delete_message" class="delete-message-btn" onclick="return confirm('Delete this message?');">
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