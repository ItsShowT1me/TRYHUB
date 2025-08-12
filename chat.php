<?php
session_start();
include("connection.php");

$group_id = isset($_GET['group_id']) ? intval($_GET['group_id']) : 1;

// Fetch group info
$group = mysqli_fetch_assoc(mysqli_query($con, "SELECT * FROM groups WHERE id = '$group_id'"));

// Fetch members and group creator
$members = [];
$creator_id = null;
if ($group) {
    $creator_res = mysqli_query($con, "SELECT user_id FROM user_groups WHERE group_id = '$group_id' ORDER BY id ASC LIMIT 1");
    if ($creator_row = mysqli_fetch_assoc($creator_res)) {
        $creator_id = $creator_row['user_id'];
    }
    $res = mysqli_query($con, "
        SELECT u.user_id, u.user_name, u.mbti 
        FROM user_groups ug
        JOIN users u ON ug.user_id = u.user_id
        WHERE ug.group_id = '$group_id'
    ");
    while ($row = mysqli_fetch_assoc($res)) {
        $members[] = $row;
    }
}

// Handle leave group action
if (isset($_GET['leave']) && isset($_SESSION['user_id']) && $group) {
    $leave_id = intval($_SESSION['user_id']);
    // Prevent group creator from leaving
    if ($leave_id != $creator_id) {
        mysqli_query($con, "DELETE FROM user_groups WHERE user_id='$leave_id' AND group_id='$group_id'");
        header("Location: group.php");
        exit();
    }
}

// Check if user is banned
$user_id = $_SESSION['user_id'] ?? null;
$user_data = null;
if ($user_id) {
    $user_data = mysqli_fetch_assoc(mysqli_query($con, "SELECT banned_until FROM users WHERE user_id = '$user_id'"));
}
if (!empty($user_data['banned_until']) && strtotime($user_data['banned_until']) > time()) {
    $ban_time = date('d M Y H:i', strtotime($user_data['banned_until']));
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
<html>
<head>
    <title>Group Chat</title>
    <link rel="stylesheet" href="CSS code/chat.css">
    <link rel="stylesheet" href="CSS code/group.css">
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <style>
        .color-badge {
            display: inline-block;
            width: 22px;
            height: 22px;
            border-radius: 6px;
            border: 2px solid #eee;
            margin-right: 8px;
            vertical-align: middle;
        }
        .group-description {
            background: #f7f9fb;
            border-radius: 10px;
            padding: 12px 16px;
            color: #222;
            margin-bottom: 18px;
            font-size: 1em;
            box-shadow: 0 2px 8px #3a7bd510;
        }
        .return-btn {
            position: absolute;
            top: 24px;
            left: 24px;
            background: #3a7bd5;
            color: #fff;
            border: none;
            border-radius: 8px;
            padding: 8px 18px;
            font-size: 1em;
            cursor: pointer;
            z-index: 100;
            box-shadow: 0 2px 8px #3a7bd540;
            text-decoration: none;
            display: flex;
            align-items: center;
            gap: 8px;
        }
        .return-btn i {
            font-size: 1.2em;
        }
        .profile-card {
            background: #fff;
            border-radius: 16px;
            box-shadow: 0 4px 16px #3a7bd520;
            width: 340px;
            margin: 60px auto 0 auto;
            padding: 32px 28px 24px 28px;
            display: flex;
            flex-direction: column;
            align-items: center;
            font-family: 'Poppins', Arial, sans-serif;
        }

        .profile-image {
            width: 100px;
            height: 100px;
            border-radius: 50%;
            overflow: hidden;
            margin-bottom: 18px;
            border: 4px solid #3a7bd5;
            box-shadow: 0 2px 8px #3a7bd520;
            background: #fff;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .profile-image img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            border-radius: 50%;
            display: block;
        }

        .profile-info {
            width: 100%;
            text-align: left;
        }

        .profile-name {
            font-size: 1.4em;
            font-weight: 600;
            color: #222;
            margin-bottom: 12px;
            text-align: left;
            word-break: break-word;
        }

        .profile-detail {
            font-size: 1em;
            color: #222;
            margin-bottom: 6px;
            word-break: break-word;
        }

        .pdf-link {
            display: inline-flex;
            align-items: center;
            color: #e74c3c;
            font-weight: 500;
            text-decoration: none;
            font-size: 1em;
        }
        .pdf-link i {
            margin-right: 6px;
            font-size: 1.2em;
        }
        .chat-message {
            display: flex;
            align-items: flex-end;
            margin-bottom: 18px;
            transition: background 0.2s;
        }
        .chat-message.left { justify-content: flex-start; }
        .chat-message.right { justify-content: flex-end; }
        .chat-avatar {
            width: 36px;
            height: 36px;
            background: #3a7bd5;
            color: #fff;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: bold;
            font-size: 1.1em;
            margin: 0 10px;
            box-shadow: 0 2px 8px #3a7bd510;
        }
        .chat-avatar img { width:100%; height:100%; border-radius:50%; }
        .chat-bubble {
            max-width: 380px;
            padding: 14px 18px;
            border-radius: 16px;
            box-shadow: 0 2px 8px #3a7bd510;
            transition: box-shadow 0.2s;
        }
        .bubble-mine {
            background: linear-gradient(135deg, #6A11CB 0%, #2575FC 100%);
            color: #fff;
            border-bottom-right-radius: 4px;
        }
        .bubble-other {
            background: #eaf3ff;
            color: #222;
            border-bottom-left-radius: 4px;
        }
        .chat-bubble:hover { box-shadow: 0 4px 16px #3a7bd520; }
        #chat-form {
            display: flex;
            align-items: center;
            gap: 10px;
            margin-top: 8px;
            background: #fff;
            border-radius: 12px;
            padding: 10px 16px;
            box-shadow: 0 2px 8px #3a7bd510;
        }
        #chat-form input[type="text"] {
            flex: 1 1 0;
            padding: 12px 16px;
            border-radius: 8px;
            border: 1px solid #d0d7e2;
            font-size: 1.1em;
            outline: none;
            background: #f7f9fb;
            transition: border 0.2s;
        }
        #chat-form input[type="text"]:focus { border-color: #3a7bd5; }
        #attach-btn, .send-btn {
            background: #3a7bd5;
            color: #fff;
            border: none;
            border-radius: 8px;
            width: 44px;
            height: 44px;
            font-size: 1.5em;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: background 0.2s;
        }
        #attach-btn:hover, .send-btn:hover { background: #2575FC; }
        /* New styles for member modal */
        #show-members-btn {
            position: fixed;
            top: 32px;
            right: 32px;
            z-index: 200;
            background: linear-gradient(90deg,#3a7bd5 0%,#764ba2 100%);
            color: #fff;
            font-weight: 700;
            border-radius: 10px;
            padding: 12px 28px;
            font-size: 1.08em;
            box-shadow: 0 2px 8px #3a7bd522;
            cursor: pointer;
            border: none;
        }
        #member-modal {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100vw;
            height: 100vh;
            background: rgba(0,0,0,0.18);
            z-index: 9999;
            align-items: center;
            justify-content: center;
            animation: fadeInModal 0.2s;
        }
        #member-modal > div {
            background: #fff;
            border-radius: 22px;
            box-shadow: 0 8px 32px #3a7bd540;
            width: 420px;
            max-width: 96vw;
            padding: 36px 28px;
            position: relative;
            margin: auto;
            top: 10vh;
            transition: box-shadow 0.2s;
            border: 1px solid #eaf3ff;
        }
        #modal-member-list {
            max-height: 300px;
            overflow-y: auto;
            margin-top: 16px;
        }
        .modal-member-avatar {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            overflow: hidden;
            margin-right: 12px;
            border: 2px solid #3a7bd5;
            box-shadow: 0 2px 8px #3a7bd520;
            background: #fff;
        }
        .modal-member-name {
            font-size: 1.1em;
            font-weight: 500;
            color: #222;
            margin-bottom: 4px;
            display: flex;
            align-items: center;
            gap: 4px;
        }
        .modal-member-mbti {
            font-size: 0.9em;
            color: #666;
        }
        .kick-member-btn {
            background: linear-gradient(90deg,#DB504A 0%,#b92d23 100%);
            color: #fff;
            border: none;
            border-radius: 8px;
            padding: 6px 14px;
            font-size: 0.98em;
            cursor: pointer;
            font-weight: 600;
            margin-left: auto;
            box-shadow: 0 2px 8px #DB504A22;
            transition: background 0.2s;
        }
        @keyframes fadeInModal {
            from { opacity: 0; }
            to { opacity: 1; }
        }
        .member-modal-pagination {
            text-align: center;
            margin-top: 18px;
        }
        .member-modal-pagination button {
            background: #eaf3ff;
            color: #3a7bd5;
            border: none;
            border-radius: 8px;
            padding: 7px 18px;
            margin: 0 2px;
            cursor: pointer;
            font-weight: 600;
            font-size: 1em;
            transition: background 0.2s, color 0.2s;
        }
        .member-modal-pagination button.active {
            background: #3a7bd5;
            color: #fff;
        }
        .kick-member-btn:hover {
            background: linear-gradient(90deg,#b92d23 0%,#DB504A 100%);
        }
    </style>
</head>
<body>
<!-- Sidebar from group.php -->
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
<a href="group.php" class="return-btn"><i class="bx bx-arrow-back"></i> Return</a>
<div class="container">
    <?php if ($group): ?>
    <div class="sidebar">
        <div style="display:flex;justify-content:space-between;align-items:center;">
            <h4 style="text-align:center;">Group Details</h4>
            <?php if (isset($_SESSION['user_id']) && $_SESSION['user_id'] == $creator_id): ?>
                <a href="edit_group.php?group_id=<?= $group_id ?>" class="edit-group-btn" style="
                    background: linear-gradient(90deg,#667eea 0%,#3a7bd5 100%);
                    color: #fff;
                    border: none;
                    border-radius: 8px;
                    padding: 6px 18px;
                    font-size: 1em;
                    margin-left: 16px;
                    cursor: pointer;
                    text-decoration: none;
                    display: inline-flex;
                    align-items: center;
                    gap: 6px;
                    box-shadow: 0 2px 8px #667eea22;
                    transition: background 0.2s;
                " onmouseover="this.style.background='#3a7bd5'" onmouseout="this.style.background='linear-gradient(90deg,#667eea 0%,#3a7bd5 100%)'">
                    <i class="bx bx-edit"></i> Edit Group
                </a>
            <?php endif; ?>
        </div>
        <div class="detail-label">Group Name:</div>
        <div class="detail-value" style="display:flex;align-items:center;gap:8px;">
            <span class="color-badge" style="background:<?= htmlspecialchars($group['color']) ?>"></span>
            <span style="font-size:1.18em;font-weight:700;color:#5636d6;"><?= htmlspecialchars($group['name']) ?></span>
            <span class="group-type" style="background:#eaf3ff;color:#3a7bd5;padding:4px 12px;border-radius:10px;font-weight:500;">
                <?= $group['is_private'] ? 'Private' : 'Public' ?>
            </span>
        </div>
        <div class="detail-label">Category:</div>
        <div class="detail-value">
            <span class="group-category" style="background:#f7f9fb;color:#764ba2;padding:4px 12px;border-radius:10px;font-weight:500;">
                <?= ucfirst($group['category']) ?>
            </span>
        </div>
        <div class="detail-label">Created:</div>
        <div class="detail-value"><?= date('d/m/Y', strtotime($group['created_at'])) ?></div>
        <div class="detail-label">Group Pin:</div>
        <div class="detail-value" style="font-family:monospace;font-size:1.08em;"><?= htmlspecialchars($group['pin']) ?></div>
        <div class="detail-label">Description:</div>
        <div class="group-description"><?= nl2br(htmlspecialchars($group['description'])) ?></div>
        <?php if (isset($_SESSION['user_id']) && $_SESSION['user_id'] != $creator_id): ?>
            <a href="chat.php?group_id=<?= $group_id ?>&leave=1" class="leave-group-btn" style="
                background: linear-gradient(90deg,#DB504A 0%,#b92d23 100%);
                color: #fff;
                border: none;
                border-radius: 8px;
                padding: 8px 18px;
                font-size: 1em;
                margin-top: 18px;
                cursor: pointer;
                text-decoration: none;
                display: inline-flex;
                align-items: center;
                gap: 8px;
                font-weight: 600;
                box-shadow: 0 2px 8px #DB504A22;
                transition: background 0.2s;
            " onclick="return confirm('Are you sure you want to leave this group?');">
                <i class="bx bx-log-out"></i> Leave Group
            </a>
        <?php endif; ?>
    </div>
    <div class="chat-area" style="position:relative;border-radius:0px;box-shadow:0 4px 16px #3a7bd520;">
        <button id="show-members-btn" style="
            position:absolute;
            top:0;
            right:0;
            margin:18px 24px 0 0;
            background:linear-gradient(90deg,#3a7bd5 0%,#764ba2 100%);
            color:#fff;font-weight:700;border-radius:10px;padding:8px 22px;
            font-size:1.08em;box-shadow:0 2px 8px #3a7bd522;cursor:pointer;border:none;
            display:flex;align-items:center;gap:8px;
            z-index:10;
        ">
            <i class="bx bx-group"></i> Member
        </button>
        <div style="display:flex;align-items:center;justify-content:flex-start;margin-bottom:16px;">
            <h4 style="color:#3a7bd5;margin:0;">
                <i class="bx bx-chat"></i> Group Chat
            </h4>
        </div>
        <div id="chat-box"></div>
        <form id="chat-form" enctype="multipart/form-data" style="margin-top:12px;">
            <input type="hidden" name="group_id" value="<?= $group_id ?>">
            <input type="text" name="message" placeholder="Type your message..." required autocomplete="off" style="font-size:1.08em;">
            <input type="file" name="file" id="file-input" style="display:none;">
            <button type="button" id="attach-btn" title="Attach file"><i class="bx bx-paperclip"></i></button>
            <button type="submit" class="send-btn" title="Send"><i class="bx bx-send"></i></button>
        </form>
    </div>

    <!-- Member Modal -->
    <div id="member-modal" style="
        display:none;position:fixed;top:0;left:0;width:100vw;height:100vh;
        background:rgba(0,0,0,0.18);z-index:9999;align-items:center;justify-content:center;
        animation: fadeInModal 0.2s;
    ">
        <div style="
            background:#fff;border-radius:22px;box-shadow:0 8px 32px #3a7bd540;
            width:420px;max-width:96vw;padding:36px 28px;position:relative;
            margin:auto;top:10vh;transition:box-shadow 0.2s;
            border: 1px solid #eaf3ff;
        ">
            <h3 style="color:#3a7bd5;text-align:center;margin-bottom:24px;font-size:1.3em;font-weight:700;">
                <i class="bx bx-group"></i> Member List
            </h3>
            <div id="modal-member-list">
                <div style="color:#aaa;text-align:center;">Loading...</div>
            </div>
            <button id="close-member-modal" style="
                position:absolute;top:18px;right:18px;background:none;border:none;
                font-size:1.5em;color:#3a7bd5;cursor:pointer;
                transition:color 0.2s;
            " onmouseover="this.style.color='#DB504A'" onmouseout="this.style.color='#3a7bd5'">
                <i class="bx bx-x"></i>
            </button>
        </div>
    </div>
    <div id="profile-preview-modal" style="
        display:none;position:fixed;top:0;left:0;width:100vw;height:100vh;
        background:rgba(0,0,0,0.18);z-index:10000;
    ">
        <div id="profile-preview-popup" style="
            position:absolute;
            background:#fff;
            border-radius:22px;
            box-shadow:0 8px 32px #3a7bd540;
            width:380px;max-width:98vw;padding:0;
        ">
            <iframe id="profile-preview-frame" src="" style="width:100%;height:420px;border:none;border-radius:22px;"></iframe>
            <button id="close-profile-preview" style="
                position:absolute;top:18px;right:18px;background:none;border:none;
                font-size:1.5em;color:#3a7bd5;cursor:pointer;transition:color 0.2s;
            ">&times;</button>
        </div>
    </div>
    <style>
    @keyframes fadeInModal {
        from { opacity: 0; }
        to { opacity: 1; }
    }
    .member-modal-pagination {
        text-align: center;
        margin-top: 18px;
    }
    .member-modal-pagination button {
        background: #eaf3ff;
        color: #3a7bd5;
        border: none;
        border-radius: 8px;
        padding: 7px 18px;
        margin: 0 2px;
        cursor: pointer;
        font-weight: 600;
        font-size: 1em;
        transition: background 0.2s, color 0.2s;
    }
    .member-modal-pagination button.active {
        background: #3a7bd5;
        color: #fff;
    }
    .kick-member-btn:hover {
        background: linear-gradient(90deg,#b92d23 0%,#DB504A 100%);
    }
    </style>
    <script>
$(function(){
    // Show modal
    $('#show-members-btn').on('click', function(){
        $('#member-modal').fadeIn(150);
        loadModalMembers(1);
    });
    // Hide modal
    $('#close-member-modal').on('click', function(){
        $('#member-modal').fadeOut(150);
    });
    // Hide modal on background click
    $('#member-modal').on('click', function(e){
        if (e.target === this) $(this).fadeOut(150);
    });

    function loadModalMembers(page) {
        var group_id = <?= intval($group_id) ?>;
        $('#modal-member-list').html('<div style="color:#aaa;text-align:center;">Loading...</div>');
        $.get('fetch-group-members.php?group_id=' + group_id + '&page=' + page, function(data) {
            let html = '<ul style="list-style:none;padding:0;margin:0;">';
            let currentUserId = <?= json_encode($_SESSION['user_id']) ?>;
            let ownerId = <?= json_encode($creator_id) ?>;
            data.members.forEach(function(m){
                let avatar = m.image ? `<img src="${m.image}" class="modal-member-avatar">` : `<div class="modal-member-avatar">${m.user_name.charAt(0).toUpperCase()}</div>`;
                html += `<li style="display:flex;align-items:center;gap:12px;margin-bottom:18px;padding-bottom:8px;border-bottom:1px solid #e0e0e0;">
                    ${avatar}
                    <div>
                        <div class="modal-member-name">
                            <a href="profile_preview.php" class="preview-member-link" data-user="${m.user_id}" style="color:#3a7bd5;text-decoration:underline;font-weight:600;">${m.user_name}</a>
                            ${m.user_id == ownerId ? ' <span title="Owner" style="color:#ffce26;font-size:1.2em;">👑</span>' : ''}
                        </div>
                        <div class="modal-member-mbti">${m.mbti}</div>
                    </div>`;
                // Show kick button only for owner and not for self
                if (currentUserId == ownerId && m.user_id != ownerId) {
                    html += `<button class="kick-member-btn" data-user="${m.user_id}" style="
                        background:linear-gradient(90deg,#DB504A 0%,#b92d23 100%);
                        color:#fff;border:none;border-radius:8px;padding:6px 14px;
                        font-size:0.98em;cursor:pointer;font-weight:600;margin-left:auto;
                        box-shadow:0 2px 8px #DB504A22;transition:background 0.2s;
                    "><i class="bx bx-user-x"></i> Kick</button>`;
                }
                html += `</li>`;
            });
            html += '</ul>';

            // Pagination
            if (data.total_pages > 1) {
                html += '<div class="member-modal-pagination">';
                for (let i = 1; i <= data.total_pages; i++) {
                    html += `<button class="member-page-btn" data-page="${i}" style="
                        background:${i==data.page?'#3a7bd5':'#eaf3ff'};
                        color:${i==data.page?'#fff':'#3a7bd5'};
                        border:none;border-radius:6px;padding:6px 14px;margin:0 2px;cursor:pointer;font-weight:600;
                    ">${i}</button>`;
                }
                html += '</div>';
            }

            $('#modal-member-list').html(html);
        }, 'json');
    }

    // Pagination click
    $(document).on('click', '.member-page-btn', function(){
        let page = $(this).data('page');
        loadModalMembers(page);
    });

    // Kick member AJAX
    $(document).on('click', '.kick-member-btn', function(){
        var user_id = $(this).data('user');
        var group_id = <?= intval($group_id) ?>;
        var currentPage = $('.member-page-btn[style*="#3a7bd5"]').data('page') || 1;
        if (confirm('Are you sure you want to kick this member?')) {
            $.post('kick_member.php', {user_id:user_id, group_id:group_id}, function(res){
                loadModalMembers(currentPage);
            });
        }
    });

    // Show profile preview when member name is clicked
    $(document).on('click', '.preview-member-link', function(e){
        e.preventDefault();
        var user_id = $(this).data('user');
        $('#profile-preview-frame').attr('src', 'profile_preview.php?user_id=' + user_id);
        $('#profile-preview-modal').fadeIn(150);

        // Get position of clicked element
        var rect = this.getBoundingClientRect();
        var popup = $('#profile-preview-popup');
        var top = rect.top + window.scrollY;
        var left = rect.right + window.scrollX + 16; // 16px gap to the right

        // If not enough space on right, show on left
        if (left + popup.outerWidth() > $(window).width()) {
            left = rect.left + window.scrollX - popup.outerWidth() - 16;
        }
        // If not enough space at bottom, adjust top
        if (top + popup.outerHeight() > $(window).height()) {
            top = $(window).height() - popup.outerHeight() - 24;
        }
        popup.css({top: top + 'px', left: left + 'px', position: 'absolute'});
    });

    // Close profile preview modal
    $('#close-profile-preview').on('click', function(){
        $('#profile-preview-modal').fadeOut(150);
        $('#profile-preview-frame').attr('src', '');
    });

    // Allow profile_preview.php to close itself via postMessage
    window.addEventListener('message', function(e){
        if (e.data === 'closeProfilePreview') {
            $('#profile-preview-modal').fadeOut(150);
            $('#profile-preview-frame').attr('src', '');
        }
    });

    // Hide modal on background click
    $('#profile-preview-modal').on('click', function(e){
        if (e.target === this) $(this).fadeOut(150);
    });
});
    </script>
    <?php else: ?>
    <div style="padding:40px;">
        <h2 style="color:#3a7bd5;">Group not found.</h2>
        <p>Please check the group ID or create a new group.</p>
    </div>
    <?php endif; ?>
</div>
<script>
    window.currentUserId = <?= json_encode($_SESSION['user_id']) ?>;
</script>
<script src="JS code/chat.js"></script>
<script>
    $(function(){
        function renderMessage(msg) {
            var isMine = msg.user_id == <?= json_encode($_SESSION['user_id']) ?>;
            var align = isMine ? 'right' : 'left';
            var bubble = isMine ? 'bubble-mine' : 'bubble-other';
            var avatar = msg.image ? `<img src="${msg.image}" class="chat-avatar">` : `<div class="chat-avatar">${msg.user_name.charAt(0).toUpperCase()}</div>`;
            return `
            <div class="chat-message ${align}">
                ${!isMine ? avatar : ''}
                <div class="chat-bubble ${bubble}">
                    <div class="chat-header">
                        <span class="chat-user">${msg.user_name}</span>
                        <span class="chat-mbti">(${msg.mbti})</span>
                        <span class="chat-time">${msg.time}</span>
                    </div>
                    <div class="chat-text">${msg.text}</div>
                </div>
                ${isMine ? avatar : ''}
            </div>`;
        }
        // Example: Load messages (replace with AJAX)
        // $('#chat-box').html(renderMessage({user_id:1,user_name:'Alice',mbti:'INTJ',text:'Hello!',time:'10:00',image:''}));
    });
</script>
</body>
</html>