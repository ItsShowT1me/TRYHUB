<?php

session_start();
include("connection.php");

// Only allow admin (user_id = 971221)
if (!isset($_SESSION['user_id']) || $_SESSION['user_id'] != 971221) {
    header("Location: index.php");
    exit();
}

// MBTI distribution
$mbti_counts = [];
$res = mysqli_query($con, "SELECT mbti, COUNT(*) as cnt FROM users WHERE mbti IS NOT NULL AND mbti != '' GROUP BY mbti ORDER BY cnt DESC");
while ($row = mysqli_fetch_assoc($res)) {
    $mbti_counts[$row['mbti']] = $row['cnt'];
}

// Group analytics (latest 10 groups)
$group_analytics = [];
$res2 = mysqli_query($con, "
    SELECT g.name, ga.*
    FROM group_analytics ga
    LEFT JOIN groups g ON ga.group_id = g.id
    ORDER BY ga.date DESC
    LIMIT 10
");
while ($row = mysqli_fetch_assoc($res2)) {
    $group_analytics[] = $row;
}

// MBTI compatibility pairs (top 8)
$compatibility = [];
$res3 = mysqli_query($con, "SELECT * FROM mbti_compatibility ORDER BY compatibility_score DESC LIMIT 8");
while ($row = mysqli_fetch_assoc($res3)) {
    $compatibility[] = $row;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin MBTI Analytics</title>
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
        .analytics-title {
            color: #3a7bd5;
            font-size: 2rem;
            font-weight: 700;
            margin-bottom: 24px;
            text-align: center;
        }
        .mbti-chart {
            display: flex;
            flex-wrap: wrap;
            gap: 18px;
            justify-content: center;
            margin-bottom: 36px;
        }
        .mbti-card {
            background: #f8faff;
            border-radius: 14px;
            box-shadow: 0 2px 8px #3a7bd510;
            padding: 1.2rem 1.2rem 1rem 1.2rem;
            text-align: center;
            width: 120px;
            display: flex;
            flex-direction: column;
            align-items: center;
            font-weight: 600;
            color: #3a7bd5;
            font-size: 1.18em;
            border: 2px solid #eaf3ff;
        }
        .mbti-card .mbti-type {
            font-size: 1.3em;
            font-weight: 700;
            margin-bottom: 0.3rem;
        }
        .mbti-card .mbti-count {
            color: #222;
            font-size: 1.1em;
        }
        .analytics-section-title {
            color: #667eea;
            font-size: 1.2em;
            font-weight: 700;
            margin-bottom: 14px;
            margin-top: 32px;
        }
        .analytics-table {
            width: 100%;
            border-collapse: collapse;
            background: #fff;
            border-radius: 12px;
            box-shadow: 0 2px 12px #342e3720;
            overflow: hidden;
            margin-bottom: 24px;
        }
        .analytics-table th, .analytics-table td {
            padding: 14px 18px;
            border-bottom: 1px solid #f0f4fa;
            text-align: left;
            font-size: 1.05em;
        }
        .analytics-table th {
            background: #f6f8fa;
            font-weight: 600;
            color: #3a7bd5;
            letter-spacing: 0.5px;
        }
        .analytics-table tr:last-child td { border-bottom: none; }
        .compat-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 18px;
        }
        .compat-table th, .compat-table td {
            padding: 10px 14px;
            border-bottom: 1px solid #f0f4fa;
            text-align: left;
            font-size: 1em;
        }
        .compat-table th {
            background: #f6f8fa;
            font-weight: 600;
            color: #3a7bd5;
        }
        .compat-table tr:last-child td { border-bottom: none; }
        .compat-pair {
            font-weight: 600;
            color: #667eea;
        }
        .compat-score {
            font-weight: 700;
            color: #3a7bd5;
        }
        .compat-type {
            font-size: 0.98em;
            color: #888;
        }
        @media (max-width: 900px) {
            .main-content { padding: 14px 4px; }
            .mbti-card { width: 90px; font-size: 1em; }
            .analytics-table th, .analytics-table td { padding: 8px 6px; }
        }
        @media (max-width: 700px) {
            .sidebar { display: none; }
            .main-content { padding: 12px 2vw; border-radius: 0; margin: 0; }
            .mbti-chart { gap: 8px; }
            .mbti-card { width: 70px; font-size: 0.92em; }
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
                    <a href="admin-analytics.php" class="nav-link active">
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
            <div class="analytics-title"><i class="bx bx-brain"></i> MBTI Analytics</div>
            
            <div style="text-align:right; margin-bottom:18px;">
                <button onclick="window.print()" style="
                    background: linear-gradient(90deg,#3a7bd5 0%,#764ba2 100%);
                    color: #fff;
                    border: none;
                    border-radius: 8px;
                    padding: 10px 28px;
                    font-size: 1.08em;
                    font-weight: 600;
                    cursor: pointer;
                    box-shadow: 0 2px 8px #3a7bd522;
                    transition: background 0.2s;
                ">
                    <i class="bx bx-printer"></i> Print Report
                </button>
            </div>

            <div class="analytics-section-title">MBTI Type Distribution</div>
            <div class="mbti-chart">
                <?php foreach ($mbti_counts as $type => $count): ?>
                    <div class="mbti-card">
                        <div class="mbti-type"><?= htmlspecialchars($type) ?></div>
                        <div class="mbti-count"><?= $count ?> users</div>
                    </div>
                <?php endforeach; ?>
            </div>

            <div class="analytics-section-title">Recent Group Analytics</div>
            <table class="analytics-table">
                <thead>
                    <tr>
                        <th>Group</th>
                        <th>Date</th>
                        <th>Messages</th>
                        <th>Active Members</th>
                        <th>Engagement Score</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (count($group_analytics) === 0): ?>
                        <tr><td colspan="5" style="text-align:center;color:#aaa;">No analytics data found.</td></tr>
                    <?php else: foreach ($group_analytics as $ga): ?>
                        <tr>
                            <td><?= htmlspecialchars($ga['name'] ?? 'Unknown') ?></td>
                            <td><?= htmlspecialchars($ga['date']) ?></td>
                            <td><?= intval($ga['message_count']) ?></td>
                            <td><?= intval($ga['active_members']) ?></td>
                            <td><?= number_format($ga['engagement_score'],2) ?></td>
                        </tr>
                    <?php endforeach; endif; ?>
                </tbody>
            </table>

            <div class="analytics-section-title">Top MBTI Compatibility Pairs</div>
            <table class="compat-table">
                <thead>
                    <tr>
                        <th>Pair</th>
                        <th>Score</th>
                        <th>Type</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($compatibility as $c): ?>
                        <tr>
                            <td class="compat-pair"><?= htmlspecialchars($c['type1']) ?> + <?= htmlspecialchars($c['type2']) ?></td>
                            <td class="compat-score"><?= number_format($c['compatibility_score'],2) ?></td>
                            <td class="compat-type"><?= htmlspecialchars($c['relationship_type']) ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</body>
</html>