<?php

session_start();
include 'connection.php';

// Only allow admin
if (!isset($_SESSION['user_id']) || $_SESSION['user_id'] != 971221) {
    header("Location: index.php");
    exit();
}

// Pagination setup
$limit = 20;
$page = isset($_GET['page']) ? max(1, intval($_GET['page'])) : 1;
$offset = ($page - 1) * $limit;

// Handle search
$search = isset($_GET['search']) ? trim($_GET['search']) : '';
$where = '';

// Build WHERE clause to exclude admin
$where_clause = '';
if ($search !== '') {
    $safe_search = mysqli_real_escape_string($con, $search);
    $where_clause = "WHERE (user_name LIKE '%$safe_search%' OR mbti LIKE '%$safe_search%') AND user_id != 971221";
} else {
    $where_clause = "WHERE user_id != 971221";
}

// Get total users count (with search)
$total_result = mysqli_query($con, "SELECT COUNT(*) as total FROM users $where_clause");
$total_users = mysqli_fetch_assoc($total_result)['total'];
$total_pages = ceil($total_users / $limit);

// Fetch users for current page (with search), EXCLUDE admin (user_id = 971221)
$users = [];
$result = mysqli_query($con, "SELECT * FROM users $where_clause ORDER BY date DESC LIMIT $limit OFFSET $offset");
while ($row = mysqli_fetch_assoc($result)) {
    // Get group count for each user
    $group_count = 0;
    $group_result = mysqli_query($con, "SELECT COUNT(*) as cnt FROM user_groups WHERE user_id = " . intval($row['user_id']));
    if ($group_row = mysqli_fetch_assoc($group_result)) {
        $group_count = $group_row['cnt'];
    }
    $row['group_count'] = $group_count;
    $users[] = $row;
}

// Ban user
if (isset($_GET['ban']) && is_numeric($_GET['ban']) && isset($_GET['days'])) {
    $ban_id = intval($_GET['ban']);
    $days = intval($_GET['days']);
    $until = date('Y-m-d H:i:s', strtotime("+$days days"));
    mysqli_query($con, "UPDATE users SET banned_until='$until' WHERE user_id=$ban_id");
    header("Location: admin-users.php?banned=1");
    exit();
}

// Unban user
if (isset($_GET['unban']) && is_numeric($_GET['unban'])) {
    $unban_id = intval($_GET['unban']);
    mysqli_query($con, "UPDATE users SET banned_until=NULL WHERE user_id=$unban_id");
    header("Location: admin-users.php?unbanned=1");
    exit();
}

// Delete user
if (isset($_GET['delete']) && is_numeric($_GET['delete'])) {
    $del_id = intval($_GET['delete']);
    mysqli_query($con, "DELETE FROM user_groups WHERE user_id=$del_id");
    mysqli_query($con, "DELETE FROM users WHERE user_id=$del_id");
    header("Location: admin-users.php?deleted=1");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin - Users</title>
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
        .header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 28px;
        }
        .header h1 {
            margin: 0;
            font-size: 2.2em;
            font-weight: 700;
            color: #3C91E6;
            letter-spacing: 1px;
        }
        .search-bar-container { position: static; }
        .search-bar-form {
            display: flex;
            align-items: center;
            background: #f7f9fb;
            border-radius: 28px;
            box-shadow: 0 2px 8px #3a7bd510;
            padding: 2px 8px;
        }
        .search-bar-input {
            padding: 12px 20px;
            border-radius: 28px 0 0 28px;
            border: none;
            font-size: 1.08em;
            outline: none;
            background: transparent;
            min-width: 220px;
        }
        .search-bar-btn {
            padding: 12px 22px;
            border-radius: 0 28px 28px 0;
            border: none;
            background: #667eea;
            color: #fff;
            font-size: 1.3em;
            cursor: pointer;
            transition: background 0.2s;
            display: flex;
            align-items: center;
        }
        .search-bar-btn:hover { background: #3a7bd5; }
        .users-table {
            width:100%;
            border-collapse:collapse;
            margin-top:24px;
            background:#fff;
            border-radius:14px;
            box-shadow:0 2px 12px #342e3720;
            overflow:hidden;
        }
        .users-table th, .users-table td {
            padding:16px 20px;
            border-bottom:1px solid #f0f4fa;
            text-align:left;
            vertical-align:middle;
        }
        .users-table th {
            background:#f6f8fa;
            font-weight:700;
            color:#3C91E6;
            font-size:1.08em;
        }
        .users-table tr:last-child td { border-bottom:none; }
        .users-table tr:hover { background: #f7f9fb; transition: background 0.2s; }
        .user-avatar {
            width:40px; height:40px; border-radius:50%; object-fit:cover; background:#eaf3ff;
            display:inline-block; box-shadow:0 2px 8px #3a7bd510;
        }
        .mbti-badge {
            padding:4px 12px;
            border-radius:14px;
            background:#667eea;
            color:#fff;
            font-size:14px;
            font-weight:500;
            letter-spacing:1px;
            margin-left:2px;
            box-shadow:0 1px 4px #667eea22;
        }
        .ban-btn {
            background: #ffce26;
            color: #222;
            border: none;
            border-radius: 8px;
            padding: 6px 14px;
            margin-bottom: 6px;
            cursor: pointer;
            font-size: 1em;
            font-weight: 500;
            transition: background 0.2s, box-shadow 0.2s;
            box-shadow: 0 1px 4px #ffce2622;
            display: block;
            width: 100%;
            text-align: left;
        }
        .ban-btn:hover { background: #ffe48a; box-shadow: 0 2px 8px #ffce2640; }
        .delete-btn {
            background: #DB504A;
            color: #fff;
            border: none;
            border-radius: 8px;
            padding: 6px 14px;
            cursor: pointer;
            font-size: 1em;
            font-weight: 500;
            transition: background 0.2s, box-shadow 0.2s;
            box-shadow: 0 1px 4px #DB504A22;
            display: block;
            width: 100%;
            text-align: left;
            margin-top: 6px;
        }
        .delete-btn:hover { background: #b92d23; box-shadow: 0 2px 8px #DB504A40; }
        .banned-label {
            color:#DB504A;
            font-weight:600;
            background:#ffeaea;
            border-radius:8px;
            padding:6px 10px;
            font-size:0.98em;
            display:inline-block;
        }
        .pagination {
            position: fixed;
            left: 0;
            bottom: 0;
            width: 100vw;
            background: #fff;
            box-shadow: 0 -2px 12px #342e3720;
            padding: 18px 0;
            margin: 0;
            z-index: 100;
            border-radius: 0;
            text-align: center;
        }
        .pagination a, .pagination span {
            display: inline-block;
            padding: 8px 16px;
            margin: 0 2px;
            border-radius: 6px;
            background: #f6f8fa;
            color: #333;
            text-decoration: none;
            font-size: 1.08em;
            border: 1px solid #e0e7ff;
            min-width: 38px;
            transition: background 0.2s, color 0.2s;
        }
        .pagination a:hover {
            background: #e0e7ff;
            color: #3C91E6;
        }
        .pagination .active {
            background: #667eea;
            color: #fff;
            font-weight: bold;
            border: 1px solid #667eea;
        }
        .pagination .disabled {
            color: #aaa;
            background: #f6f8fa;
            cursor: not-allowed;
            border: 1px solid #f6f8fa;
        }
        .pagination .ellipsis {
            background: transparent;
            color: #aaa;
            cursor: default;
            padding: 8px 0;
            border: none;
        }
        @media (max-width: 900px) {
            .main-content { padding: 14px 4px; }
            .users-table th, .users-table td { padding:8px 6px; }
            .search-bar-input { min-width: 120px; }
            .pagination { padding: 10px 0; font-size: 0.95em; }
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
                    <a href="admin-users.php" class="nav-link active">
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
            <div class="header">
                <h1>Users Management</h1>
                <div class="search-bar-container">
                    <form class="search-bar-form" method="get" action="admin-users.php">
                        <input type="text" name="search" class="search-bar-input" placeholder="Search name or MBTI..." value="<?= htmlspecialchars($search) ?>">
                        <button type="submit" class="search-bar-btn"><i class="bx bx-search"></i></button>
                    </form>
                </div>
            </div>
            <div class="content-wrapper">
                <table class="users-table">
                    <thead>
                        <tr>
                            <th>Avatar</th>
                            <th>Name</th>
                            <th>Email</th>
                            <th>MBTI</th>
                            <th>Groups</th>
                            <th>Joined</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($users as $user): ?>
                        <tr>
                            <td>
                                <?php if ($user['image']): ?>
                                    <img src="<?= htmlspecialchars($user['image']) ?>" class="user-avatar" alt="Avatar">
                                <?php else: ?>
                                    <span class="user-avatar"></span>
                                <?php endif; ?>
                            </td>
                            <td style="font-weight:600; color:#222;"><?= htmlspecialchars($user['user_name']) ?></td>
                            <td style="color:#555;"><?= htmlspecialchars($user['email']) ?></td>
                            <td>
                                <?php if ($user['mbti']): ?>
                                    <span class="mbti-badge"><?= htmlspecialchars($user['mbti']) ?></span>
                                <?php endif; ?>
                            </td>
                            <td style="color:#3C91E6; font-weight:500;"><?= $user['group_count'] ?></td>
                            <td style="color:#aaa;"><?= date('M d, Y', strtotime($user['date'])) ?></td>
                            <td>
                                <?php if ($user['user_id'] != 971221): ?>
                                    <?php if (!empty($user['banned_until']) && strtotime($user['banned_until']) > time()): ?>
                                        <span class="banned-label">
                                            <i class="bx bx-block"></i> Banned<br><?= date('M d, H:i', strtotime($user['banned_until'])) ?><br>
                                            <button onclick="unbanUser(<?= $user['user_id'] ?>)" class="ban-btn" style="background:#3C91E6;color:#fff;margin-top:6px;" title="Unban"><i class="bx bx-check"></i> Unban</button>
                                        </span>
                                    <?php else: ?>
                                        <button onclick="banUser(<?= $user['user_id'] ?>,1)" class="ban-btn" title="Ban 1 day"><i class="bx bx-block"></i> Ban 1d</button>
                                        <button onclick="banUser(<?= $user['user_id'] ?>,3)" class="ban-btn" title="Ban 3 days"><i class="bx bx-block"></i> Ban 3d</button>
                                    <?php endif; ?>
                                    <button onclick="deleteUser(<?= $user['user_id'] ?>)" class="delete-btn" title="Delete"><i class="bx bx-trash"></i> Delete</button>
                                <?php endif; ?>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
                <div class="pagination">
                    <?php
                    // Previous button
                    if ($page > 1) {
                        echo '<a href="?page=' . ($page - 1) . ($search ? '&search=' . urlencode($search) : '') . '">&laquo; Prev</a>';
                    } else {
                        echo '<span class="disabled">&laquo; Prev</span>';
                    }

                    // Page numbers with ellipsis for large page counts
                    $max_display = 5;
                    if ($total_pages <= $max_display + 2) {
                        // Show all pages
                        for ($p = 1; $p <= $total_pages; $p++) {
                            if ($p == $page) {
                                echo '<span class="active">' . $p . '</span>';
                            } else {
                                echo '<a href="?page=' . $p . ($search ? '&search=' . urlencode($search) : '') . '">' . $p . '</a>';
                            }
                        }
                    } else {
                        // Show first, last, current, and neighbors
                        if ($page > 2) {
                            echo '<a href="?page=1' . ($search ? '&search=' . urlencode($search) : '') . '">1</a>';
                            if ($page > 3) echo '<span class="ellipsis">...</span>';
                        }
                        $start = max(2, $page - 1);
                        $end = min($total_pages - 1, $page + 1);
                        for ($p = $start; $p <= $end; $p++) {
                            if ($p == $page) {
                                echo '<span class="active">' . $p . '</span>';
                            } else {
                                echo '<a href="?page=' . $p . ($search ? '&search=' . urlencode($search) : '') . '">' . $p . '</a>';
                            }
                        }
                        if ($page < $total_pages - 1) {
                            if ($page < $total_pages - 2) echo '<span class="ellipsis">...</span>';
                            echo '<a href="?page=' . $total_pages . ($search ? '&search=' . urlencode($search) : '') . '">' . $total_pages . '</a>';
                        }
                    }

                    // Next button
                    if ($page < $total_pages) {
                        echo '<a href="?page=' . ($page + 1) . ($search ? '&search=' . urlencode($search) : '') . '">Next &raquo;</a>';
                    } else {
                        echo '<span class="disabled">Next &raquo;</span>';
                    }
                    ?>
                </div>
            </div>
        </div>
    </div>
    <script>
function banUser(id, days) {
    if (confirm('Ban this user for ' + days + ' day(s)?')) {
        window.location.href = 'admin-users.php?ban=' + id + '&days=' + days;
    }
}
function unbanUser(id) {
    if (confirm('Unban this user?')) {
        window.location.href = 'admin-users.php?unban=' + id;
    }
}
function deleteUser(id) {
    if (confirm('Delete this user? This cannot be undone.')) {
        window.location.href = 'admin-users.php?delete=' + id;
    }
}
</script>
</body>
</html>