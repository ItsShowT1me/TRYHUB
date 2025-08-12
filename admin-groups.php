<?php

session_start();
include 'connection.php';

// Only allow admin
if (!isset($_SESSION['user_id']) || $_SESSION['user_id'] != 971221) {
    header("Location: index.php");
    exit();
}

// Handle delete group
if (isset($_GET['delete']) && is_numeric($_GET['delete'])) {
    $del_id = intval($_GET['delete']);
    // Delete group and related user_groups
    mysqli_query($con, "DELETE FROM user_groups WHERE group_id = $del_id");
    mysqli_query($con, "DELETE FROM groups WHERE id = $del_id");
    header("Location: admin-groups.php?deleted=1");
    exit();
}

// Fetch all groups
$groups = [];
$result = mysqli_query($con, "SELECT * FROM groups ORDER BY created_at DESC, id DESC");
while ($row = mysqli_fetch_assoc($result)) {
    // Get member count for each group
    $member_count = 0;
    $group_id = intval($row['id']);
    $mc_result = mysqli_query($con, "SELECT COUNT(*) as cnt FROM user_groups WHERE group_id = $group_id");
    if ($mc_row = mysqli_fetch_assoc($mc_result)) {
        $member_count = $mc_row['cnt'];
    }
    $row['member_count'] = $member_count;
    $groups[] = $row;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin - Groups</title>
    <link href="https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css" rel="stylesheet">
    <link rel="stylesheet" href="CSS code/admin-dashboard.css">
    <style>
        .groups-table { width:100%; border-collapse:collapse; margin-top:24px; background:#fff; border-radius:12px; box-shadow:0 2px 12px #342e3720; }
        .groups-table th, .groups-table td { padding:12px 16px; border-bottom:1px solid #f0f4fa; text-align:left; }
        .groups-table th { background:#f6f8fa; font-weight:600; }
        .groups-table tr:last-child td { border-bottom:none; }
        .group-color-icon {
            width:36px; height:36px; border-radius:10px;
            display:inline-flex; align-items:center; justify-content:center;
            font-weight:600; color:#fff; margin-right:10px; font-size:1.1em;
            box-shadow:0 2px 8px #3a7bd510;
        }
        .group-name { font-weight:500; font-size:1.08em; }
        .group-desc { color:#666; font-size:0.98em; max-width:220px; }
        .group-date { color:#aaa; font-size:0.95em; }
        .delete-btn {
            background:#DB504A; color:#fff; border:none; border-radius:8px;
            padding:6px 14px; cursor:pointer; font-size:1em; transition:background 0.2s;
            display:flex; align-items:center; gap:4px;
        }
        .delete-btn:hover { background:#b92d23; }
        .header { display:flex; align-items:center; justify-content:space-between; margin-bottom:18px; }
        .header h1 { margin:0; font-size:2em; font-weight:600; }
        .success-msg {
            background:#eafbe7; color:#2e7d32; padding:10px 18px; border-radius:8px;
            margin-bottom:16px; font-size:1.08em; display:inline-block;
        }
        @media (max-width: 900px) {
            .groups-table td, .groups-table th { padding:8px 6px; }
            .group-desc { max-width:120px; }
        }
        .modal {
            position: fixed;
            top:0; left:0; width:100%; height:100%;
            background:rgba(52,46,55,0.18);
            display:none; align-items:center; justify-content:center;
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
        function confirmDelete(groupName, groupId) {
            if (confirm('Are you sure you want to delete group "' + groupName + '"? This cannot be undone.')) {
                window.location.href = 'admin-groups.php?delete=' + groupId;
            }
        }
        // Modal logic
        document.addEventListener("DOMContentLoaded", function() {
            document.querySelectorAll('.group-row').forEach(function(row) {
                row.addEventListener('click', function(e) {
                    // Prevent click on delete button from opening modal
                    if (e.target.closest('.delete-btn')) return;
                    const groupId = this.getAttribute('data-group-id');
                    const modal = document.getElementById('groupMembersModal');
                    const membersList = document.getElementById('groupMembersList');
                    modal.style.display = 'flex';
                    membersList.innerHTML = 'Loading...';
                    fetch('fetch-group-members.php?group_id=' + groupId)
                        .then(response => response.json())
                        .then(data => {
                            let html = '<ul style="list-style:none;padding:0;margin:0;">';
                            data.members.forEach(function(m){
                                let avatar = m.image ? `<img src="${m.image}" class="modal-member-avatar" style="width:32px;height:32px;border-radius:50%;object-fit:cover;">` : `<div class="modal-member-avatar">${m.user_name.charAt(0).toUpperCase()}</div>`;
                                html += `<li style="display:flex;align-items:center;gap:12px;margin-bottom:14px;padding-bottom:8px;border-bottom:1px solid #e0e0e0;">
                                    ${avatar}
                                    <div>
                                        <div class="modal-member-name" style="font-weight:600;color:#222;">${m.user_name}</div>
                                        <div class="modal-member-mbti" style="background:#eaf3ff;color:#3a7bd5;padding:2px 10px;border-radius:8px;font-size:0.98em;margin-top:2px;display:inline-block;">${m.mbti}</div>
                                    </div>
                                </li>`;
                            });
                            html += '</ul>';
                            if (data.total_pages > 1) {
                                html += '<div style="text-align:center;margin-top:12px;">';
                                for (let i = 1; i <= data.total_pages; i++) {
                                    html += `<button class="member-page-btn" data-page="${i}" style="
                                        background:${i==data.page?'#3a7bd5':'#eaf3ff'};
                                        color:${i==data.page?'#fff':'#3a7bd5'};
                                        border:none;border-radius:6px;padding:6px 14px;margin:0 2px;cursor:pointer;font-weight:600;
                                    ">${i}</button>`;
                                }
                                html += '</div>';
                            }
                            membersList.innerHTML = html;
                        });
                });
            });
            document.getElementById('closeGroupModal').onclick = function() {
                document.getElementById('groupMembersModal').style.display = 'none';
            };
            // Optional: handle pagination if needed
            document.getElementById('groupMembersList').addEventListener('click', function(e){
                if (e.target.classList.contains('member-page-btn')) {
                    const page = e.target.getAttribute('data-page');
                    const groupId = document.querySelector('.group-row[data-group-id]').getAttribute('data-group-id');
                    const membersList = document.getElementById('groupMembersList');
                    membersList.innerHTML = 'Loading...';
                    fetch('fetch-group-members.php?group_id=' + groupId + '&page=' + page)
                        .then(response => response.json())
                        .then(data => {
                            let html = '<ul style="list-style:none;padding:0;margin:0;">';
                            data.members.forEach(function(m){
                                let avatar = m.image ? `<img src="${m.image}" class="modal-member-avatar" style="width:32px;height:32px;border-radius:50%;object-fit:cover;">` : `<div class="modal-member-avatar">${m.user_name.charAt(0).toUpperCase()}</div>`;
                                html += `<li style="display:flex;align-items:center;gap:12px;margin-bottom:14px;padding-bottom:8px;border-bottom:1px solid #e0e0e0;">
                                    ${avatar}
                                    <div>
                                        <div class="modal-member-name" style="font-weight:600;color:#222;">${m.user_name}</div>
                                        <div class="modal-member-mbti" style="background:#eaf3ff;color:#3a7bd5;padding:2px 10px;border-radius:8px;font-size:0.98em;margin-top:2px;display:inline-block;">${m.mbti}</div>
                                    </div>
                                </li>`;
                            });
                            html += '</ul>';
                            if (data.total_pages > 1) {
                                html += '<div style="text-align:center;margin-top:12px;">';
                                for (let i = 1; i <= data.total_pages; i++) {
                                    html += `<button class="member-page-btn" data-page="${i}" style="
                                        background:${i==data.page?'#3a7bd5':'#eaf3ff'};
                                        color:${i==data.page?'#fff':'#3a7bd5'};
                                        border:none;border-radius:6px;padding:6px 14px;margin:0 2px;cursor:pointer;font-weight:600;
                                    ">${i}</button>`;
                                }
                                html += '</div>';
                            }
                            membersList.innerHTML = html;
                        });
                }
            });
        });
    </script>
</head>
<body>
    <div class="container">
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
                    <a href="admin-groups.php" class="nav-link active">
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
                <h1 style="color: #ffffffff;">Groups Management</h1>
            </div>
            <div class="content-wrapper">
                <table class="groups-table">
                    <!-- Table Header -->
                    <thead>
                        <tr>
                            <th>Color</th>
                            <th>Name</th>
                            <th>Description</th>
                            <th>Category</th>
                            <th>Members</th>
                            <th>Created</th>
                            <th>Group ID</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($groups as $group): ?>
                        <tr class="group-row" data-group-id="<?= $group['id'] ?>" style="cursor:pointer;">
                            <td>
                                <div class="group-color-icon" style="background:<?= htmlspecialchars($group['color'] ?? '#667eea') ?>">
                                    <?= strtoupper(substr($group['name'], 0, 2)) ?>
                                </div>
                            </td>
                            <td class="group-name"><?= htmlspecialchars($group['name']) ?></td>
                            <td class="group-desc"><?= htmlspecialchars($group['description'] ?? '') ?></td>
                            <td><?= ucfirst(htmlspecialchars($group['category'])) ?></td>
                            <td><?= $group['member_count'] ?></td>
                            <td class="group-date">
                                <?php if (isset($group['created_at'])): ?>
                                    <?= date('M d, Y', strtotime($group['created_at'])) ?>
                                <?php else: ?>
                                    -
                                <?php endif; ?>
                            </td>
                            <td><?= htmlspecialchars($group['group_id']) ?></td>
                            <td>
                                <button class="delete-btn" onclick="confirmDelete('<?= htmlspecialchars($group['name']) ?>', <?= $group['id'] ?>); event.stopPropagation();">
                                    <i class="bx bx-trash"></i> Delete
                                </button>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
                <?php if (isset($_GET['deleted'])): ?>
                    <div class="success-msg" style="margin-top:24px;">
                        <i class="bx bx-check-circle"></i> Group deleted successfully.
                    </div>
                <?php endif; ?>
                <div id="groupMembersModal" class="modal">
                    <div class="modal-content">
                        <span class="close-btn" id="closeGroupModal">&times;</span>
                        <h3 class="modal-title"><i class="bx bx-group"></i> Group Members</h3>
                        <div id="groupMembersList" class="modal-list">Loading...</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>