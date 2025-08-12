<?php
session_start();
include 'connection.php';

// Check if user is admin
if (!isset($_SESSION['user_id']) || $_SESSION['user_id'] != 971221) {
    header("Location: index.php");
    exit();
}

// Fetch dashboard statistics
$stats = [];

// Total Users
$result = mysqli_query($con, "SELECT COUNT(*) as total_users FROM users");
$stats['total_users'] = mysqli_fetch_assoc($result)['total_users'];

// Total Groups
$result = mysqli_query($con, "SELECT COUNT(*) as total_groups FROM groups");
$stats['total_groups'] = mysqli_fetch_assoc($result)['total_groups'];

// New Users This Month
$result = mysqli_query($con, "SELECT COUNT(*) as new_users FROM users WHERE DATE_FORMAT(date, '%Y-%m') = DATE_FORMAT(NOW(), '%Y-%m')");
$stats['new_users'] = mysqli_fetch_assoc($result)['new_users'];

// Active Groups (groups with messages in last 30 days) - Check if messages table has created_at
$result = mysqli_query($con, "SHOW COLUMNS FROM messages LIKE 'created_at'");
if (mysqli_num_rows($result) > 0) {
    $result = mysqli_query($con, "
        SELECT COUNT(DISTINCT m.group_id) as active_groups 
        FROM messages m 
        WHERE m.created_at >= DATE_SUB(NOW(), INTERVAL 30 DAY)
    ");
} else {
    // Fallback if created_at doesn't exist in messages table
    $result = mysqli_query($con, "SELECT COUNT(DISTINCT group_id) as active_groups FROM messages");
}
$stats['active_groups'] = mysqli_fetch_assoc($result)['active_groups'];

// Messages This Week - Check if messages table has created_at
$result = mysqli_query($con, "SHOW COLUMNS FROM messages LIKE 'created_at'");
if (mysqli_num_rows($result) > 0) {
    $result = mysqli_query($con, "SELECT COUNT(*) as weekly_messages FROM messages WHERE created_at >= DATE_SUB(NOW(), INTERVAL 7 DAY)");
} else {
    // Fallback - just count all messages if no timestamp
    $result = mysqli_query($con, "SELECT COUNT(*) as weekly_messages FROM messages");
}
$stats['weekly_messages'] = mysqli_fetch_assoc($result)['weekly_messages'];

// MBTI Distribution
$mbti_data = [];
$result = mysqli_query($con, "SELECT mbti, COUNT(*) as count FROM users WHERE mbti IS NOT NULL AND mbti != '' GROUP BY mbti ORDER BY count DESC");
while ($row = mysqli_fetch_assoc($result)) {
    $mbti_data[] = $row;
}

// Recent Groups - Check if groups table has created_at
$recent_groups = [];
$result = mysqli_query($con, "SHOW COLUMNS FROM groups LIKE 'created_at'");
if (mysqli_num_rows($result) > 0) {
    // Check if user_groups table exists
    $table_check = mysqli_query($con, "SHOW TABLES LIKE 'user_groups'");
    if (mysqli_num_rows($table_check) > 0) {
        $result = mysqli_query($con, "
            SELECT g.*, COUNT(ug.user_id) as member_count 
            FROM groups g 
            LEFT JOIN user_groups ug ON g.id = ug.group_id 
            GROUP BY g.id 
            ORDER BY g.created_at DESC 
            LIMIT 5
        ");
    } else {
        $result = mysqli_query($con, "
            SELECT g.*, 0 as member_count 
            FROM groups g 
            ORDER BY g.created_at DESC 
            LIMIT 5
        ");
    }
} else {
    // Fallback if no created_at column
    $result = mysqli_query($con, "
        SELECT g.*, 0 as member_count 
        FROM groups g 
        ORDER BY g.id DESC 
        LIMIT 5
    ");
}
while ($row = mysqli_fetch_assoc($result)) {
    $recent_groups[] = $row;
}

// Monthly user growth data
$growth_data = [];
for ($i = 5; $i >= 0; $i--) {
    $month = date('Y-m', strtotime("-$i months"));
    $result = mysqli_query($con, "SELECT COUNT(*) as count FROM users WHERE DATE_FORMAT(date, '%Y-%m') = '$month'");
    $count = mysqli_fetch_assoc($result)['count'];
    $growth_data[] = [
        'month' => date('M', strtotime("-$i months")),
        'count' => $count
    ];
}

// Most Active Groups - Check if messages table has created_at
$active_groups = [];
$result = mysqli_query($con, "SHOW COLUMNS FROM messages LIKE 'created_at'");
if (mysqli_num_rows($result) > 0) {
    $result = mysqli_query($con, "
        SELECT g.name, g.color, COUNT(m.id) as message_count, 
               COUNT(DISTINCT m.user_id) as active_members
        FROM groups g 
        LEFT JOIN messages m ON g.id = m.group_id 
        WHERE m.created_at >= DATE_SUB(NOW(), INTERVAL 7 DAY)
        GROUP BY g.id 
        ORDER BY message_count DESC 
        LIMIT 5
    ");
} else {
    // Fallback - get groups with most messages overall
    $result = mysqli_query($con, "
        SELECT g.name, g.color, COUNT(m.id) as message_count, 
               COUNT(DISTINCT m.user_id) as active_members
        FROM groups g 
        LEFT JOIN messages m ON g.id = m.group_id 
        GROUP BY g.id 
        ORDER BY message_count DESC 
        LIMIT 5
    ");
}
while ($row = mysqli_fetch_assoc($result)) {
    $active_groups[] = $row;
}

// System Health Metrics
$system_health = [];
$result = mysqli_query($con, "SELECT COUNT(*) as total_files FROM messages WHERE file_path IS NOT NULL");
$system_health['total_files'] = mysqli_fetch_assoc($result)['total_files'];

// Fix the avg_session calculation - use 'date' column instead of 'created_at'
$result = mysqli_query($con, "SELECT AVG(TIMESTAMPDIFF(MINUTE, date, NOW())) as avg_session FROM users WHERE date >= DATE_SUB(NOW(), INTERVAL 1 DAY)");
$avg_session_result = mysqli_fetch_assoc($result);
$system_health['avg_session'] = $avg_session_result ? round($avg_session_result['avg_session'], 2) : 0;
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>BUMBTI Admin Dashboard</title>
    <link href="https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css" rel="stylesheet">
    <link rel="stylesheet" href="CSS code/admin-dashboard.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body>
    <div class="container">
        <!-- Sidebar -->
        <div class="sidebar">
            <div class="logo" style="display: flex; align-items: center; justify-content: center; padding: 18px 0;">
                <img src="images/Logo-nobg.png" alt="BUMBTI Logo" class="logo-img" style="height:80px; width:80px; display:block;">
            </div>
            
            <div class="admin-badge">
                <i class="bx bx-shield"></i> Admin Panel
            </div>
            
            <ul class="nav-menu">
                <li class="nav-item">
                    <a href="admin-dashboard.php" class="nav-link active">
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

        <!-- Main Content -->
        <div class="main-content">
            <!-- Header -->
            <div class="header">
                <div></div>
                
            </div>

            <div class="content-wrapper">
                <!-- Main Section -->
                <div class="main-section">
                    <div class="section-header">
                        <h1 class="section-title">Admin Dashboard</h1>
                    </div>

                    <!-- Statistics Grid -->
                    <div class="stats-grid">
                        <div class="stat-card users">
                            <i class="bx bx-user stat-icon"></i>
                            <div class="stat-number"><?= number_format($stats['total_users']) ?></div>
                            <div class="stat-label">Total Users</div>
                            <div class="stat-change">+<?= $stats['new_users'] ?> this month</div>
                        </div>

                        <div class="stat-card groups">
                            <i class="bx bxs-group stat-icon"></i>
                            <div class="stat-number"><?= number_format($stats['total_groups']) ?></div>
                            <div class="stat-label">Total Groups</div>
                            <div class="stat-change"><?= $stats['active_groups'] ?> active</div>
                        </div>

                        <div class="stat-card activity">
                            <i class="bx bx-trending-up stat-icon"></i>
                            <div class="stat-number"><?= count($mbti_data) ?></div>
                            <div class="stat-label">MBTI Types</div>
                            <div class="stat-change">personality diversity</div>
                        </div>
                    </div>

                    <!-- Charts Section -->
                    <div class="charts-section">
                        <div class="chart-container">
                            <h3 class="chart-title">User Growth (6 Months)</h3>
                            <canvas id="userGrowthChart" width="400" height="200"></canvas>
                        </div>

                        <div class="chart-container">
                            <h3 class="chart-title">MBTI Distribution</h3>
                            <canvas id="mbtiChart" width="400" height="200"></canvas>
                        </div>
                    </div>

                    <!-- Recent Groups -->
                    <div class="recent-groups">
                        <h3>Recent Groups</h3>
                        <?php foreach ($recent_groups as $group): ?>
                            <div class="group-item" data-group-id="<?= $group['id'] ?>" style="cursor:pointer;">
                                <div class="group-color-icon" style="background: <?= htmlspecialchars($group['color'] ?? '#667eea') ?>;">
                                    <?= strtoupper(substr($group['name'], 0, 2)) ?>
                                </div>
                                <div class="group-info">
                                    <div class="group-name"><?= htmlspecialchars($group['name']) ?></div>
                                    <div class="group-date">
                                        <?php if (isset($group['created_at'])): ?>
                                            Created: <?= date('M d, Y', strtotime($group['created_at'])) ?>
                                        <?php else: ?>
                                            Group ID: <?= htmlspecialchars($group['group_id']) ?>
                                        <?php endif; ?>
                                    </div>
                                </div>
                                <div class="group-members">
                                    <i class="bx bx-user"></i> <?= $group['member_count'] ?> members
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>

                <!-- Right Sidebar -->
                <div class="sidebar-right">
                    <!-- MBTI Distribution -->
                    <div class="mbti-distribution">
                        <h3 class="section-title-small">MBTI Distribution</h3>
                        <?php if (!empty($mbti_data)): ?>
                            <?php foreach (array_slice($mbti_data, 0, 8) as $mbti): ?>
                                <div class="mbti-item">
                                    <span class="mbti-type"><?= htmlspecialchars($mbti['mbti']) ?></span>
                                    <span class="mbti-count"><?= $mbti['count'] ?></span>
                                </div>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <p>No MBTI data available.</p>
                        <?php endif; ?>
                    </div>

                    <!-- Activity Feed -->
                    <div class="activity-feed">
                        <h3 class="section-title-small">Recent Activity</h3>
                        <?php if (!empty($active_groups)): ?>
                            <?php foreach (array_slice($active_groups, 0, 5) as $activity): ?>
                                <div class="activity-item">
                                    <div class="activity-icon" style="background: <?= htmlspecialchars($activity['color'] ?? '#667eea') ?>;">
                                        <i class="bx bx-message"></i>
                                    </div>
                                    <div class="activity-text">
                                        <?= $activity['message_count'] ?> messages in <?= htmlspecialchars($activity['name']) ?>
                                    </div>
                                    <div class="activity-time">Recent</div>
                                </div>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <p>No recent activity.</p>
                        <?php endif; ?>
                    </div>

                    <!-- Quick Actions -->
                    <div class="quick-actions">
                        <h3>Quick Actions</h3>
                        <div class="action-buttons">
                            <a href="create_group.php" class="action-btn">
                                <i class="bx bx-plus"></i>
                                Create Group
                            </a>
                            <a href="admin-users.php" class="action-btn">
                                <i class="bx bx-user-plus"></i>
                                Manage Users
                            </a>
                            <a href="index.php" class="action-btn">
                                <i class="bx bx-download"></i>
                                Main Page
                            </a>
                            <a href="admin-settings.php" class="action-btn">
                                <i class="bx bx-cog"></i>
                                System Settings
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Group Members Modal -->
    <div id="groupMembersModal" class="modal" style="display:none;">
        <div class="modal-content">
            <span class="close-btn" id="closeGroupModal">&times;</span>
            <h3 class="modal-title"><i class="bx bx-group"></i> Group Members</h3>
            <div id="groupMembersList" class="modal-list">Loading...</div>
        </div>
    </div>
    <style>
    .modal {
        position: fixed;
        top:0; left:0; width:100%; height:100%;
        background:rgba(52,46,55,0.18);
        display:flex; align-items:center; justify-content:center;
        z-index:9999;
        animation: fadeInModal 0.2s;
    }
    @keyframes fadeInModal {
        from { opacity: 0; }
        to { opacity: 1; }
    }
    .modal-content {
        background: #fff;
        padding: 32px 28px 24px 28px;
        border-radius: 16px;
        min-width: 340px;
        max-width: 96vw;
        box-shadow: 0 8px 32px #342e3720;
        position:relative;
        animation: slideUpModal 0.25s;
    }
    @keyframes slideUpModal {
        from { transform: translateY(40px); opacity: 0; }
        to { transform: translateY(0); opacity: 1; }
    }
    .close-btn {
        position:absolute; top:14px; right:18px;
        cursor:pointer; font-size:28px; color:#667eea;
        transition: color 0.2s;
    }
    .close-btn:hover { color:#DB504A; }
    .modal-title {
        font-size: 1.3em;
        font-weight: 600;
        margin-bottom: 18px;
        color: #3C91E6;
        display: flex;
        align-items: center;
        gap: 8px;
    }
    .modal-list {
        max-height: 320px;
        overflow-y: auto;
        padding-right: 4px;
    }
    .modal-list ul {
        list-style: none;
        padding-left: 0;
        margin: 0;
    }
    .modal-list li {
        display: flex;
        align-items: center;
        gap: 12px;
        padding: 10px 0;
        border-bottom: 1px solid #f0f4fa;
        font-size: 1.08em;
    }
    .modal-list li:last-child { border-bottom: none; }
    .modal-member-avatar {
        width: 32px; height: 32px;
        border-radius: 50%;
        background: #eaf3ff;
        display: flex; align-items: center; justify-content: center;
        font-weight: 600; color: #667eea; font-size: 1.1em;
    }
    .modal-member-name {
        font-weight: 500; color: #222;
    }
    .modal-member-mbti {
        background: #667eea;
        color: #fff;
        border-radius: 10px;
        padding: 2px 8px;
        font-size: 0.95em;
        margin-left: 6px;
    }
    </style>

    <script>
        // User Growth Chart
        const userGrowthCtx = document.getElementById('userGrowthChart').getContext('2d');
        new Chart(userGrowthCtx, {
            type: 'line',
            data: {
                labels: <?= json_encode(array_column($growth_data, 'month')) ?>,
                datasets: [{
                    label: 'New Users',
                    data: <?= json_encode(array_column($growth_data, 'count')) ?>,
                    backgroundColor: 'rgba(102, 126, 234, 0.1)',
                    borderColor: '#667eea',
                    borderWidth: 3,
                    fill: true,
                    tension: 0.4
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: {
                        display: false
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true
                    }
                }
            }
        });

        // MBTI Distribution Chart
        const mbtiCtx = document.getElementById('mbtiChart').getContext('2d');
        new Chart(mbtiCtx, {
            type: 'doughnut',
            data: {
                labels: <?= json_encode(array_column($mbti_data, 'mbti')) ?>,
                datasets: [{
                    data: <?= json_encode(array_column($mbti_data, 'count')) ?>,
                    backgroundColor: [
                        '#667eea', '#4ecdc4', '#f093fb', '#ffeaa7',
                        '#fdcb6e', '#fd79a8', '#a29bfe', '#6c5ce7',
                        '#00b894', '#00cec9', '#e17055', '#e84393'
                    ]
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: {
                        position: 'bottom',
                        labels: {
                            boxWidth: 12,
                            font: {
                                size: 10
                            }
                        }
                    }
                }
            }
        });

        // Navigation interactivity
        document.querySelectorAll('.nav-link').forEach(link => {
            link.addEventListener('click', function() {
                if (this.getAttribute('href').startsWith('#')) {
                    document.querySelectorAll('.nav-link').forEach(l => l.classList.remove('active'));
                    this.classList.add('active');
                }
            });
        });

        // Group Members Modal
        document.querySelectorAll('.group-item').forEach(item => {
            item.addEventListener('click', function() {
                const groupId = this.getAttribute('data-group-id');
                const modal = document.getElementById('groupMembersModal');
                const membersList = document.getElementById('groupMembersList');
                modal.style.display = 'flex';
                membersList.innerHTML = 'Loading...';
                fetch('fetch-group-members.php?group_id=' + groupId)
                    .then(response => response.text())
                    .then(html => {
                        membersList.innerHTML = html;
                    });
            });
        });
        document.getElementById('closeGroupModal').onclick = function() {
            document.getElementById('groupMembersModal').style.display = 'none';
        };
    </script>
</body>
</html>