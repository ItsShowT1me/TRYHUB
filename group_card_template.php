<?php
// Handle join group action with confirmation
if (isset($_GET['join']) && is_numeric($_GET['join']) && isset($_SESSION['user_id'])) {
    $group_id = intval($_GET['join']);
    $user_id = $_SESSION['user_id'];
    // Check if user already joined
    $check = mysqli_query($con, "SELECT * FROM user_groups WHERE user_id = '$user_id' AND group_id = '$group_id'");
    if (mysqli_num_rows($check) == 0) {
        mysqli_query($con, "INSERT INTO user_groups (user_id, group_id) VALUES ('$user_id', '$group_id')");
        echo "<script>alert('You have joined the group!');window.location='chat.php?group_id=$group_id';</script>";
        exit();
    } else {
        echo "<script>alert('You are already a member of this group.');window.location='chat.php?group_id=$group_id';</script>";
        exit();
    }
}
?>

<?php
// Check if user is already in the group
$is_member = false;
if (isset($user_data['user_id'], $group['id'])) {
    $uid = intval($user_data['user_id']);
    $gid = intval($group['id']);
    $check = mysqli_query($con, "SELECT 1 FROM user_groups WHERE user_id=$uid AND group_id=$gid LIMIT 1");
    if ($check && mysqli_num_rows($check) > 0) {
        $is_member = true;
    }
}
?>

<div class="group-card"
     style="
        background: linear-gradient(135deg, #eaf3ff 0%, #fff 100%);
        border-radius: 18px;
        box-shadow: 0 4px 16px #3a7bd520;
        width: 180px;
        min-height: 140px;
        padding: 18px 10px 14px 10px;
        display: flex;
        flex-direction: column;
        align-items: center;
        color: #222;
        position: relative;
        transition: transform 0.2s, box-shadow 0.2s, border-color 0.2s;
        border: 1.5px solid #eaf3ff;
        margin-bottom: 0;
     ">
    <div class="group-color" style="width:60px;height:60px;border-radius:50%;background:#fff;display:flex;align-items:center;justify-content:center;overflow:hidden;margin:auto;">
        <?php if (!empty($group['image'])): ?>
            <img src="<?= htmlspecialchars($group['image']) ?>" alt="Group Image"
                 style="width:60px;height:60px;object-fit:cover;border-radius:50%;display:block;">
        <?php else: ?>
            <img src="images/Logo-nobg.png" alt="Default Group Image"
                 style="width:60px;height:60px;object-fit:cover;border-radius:50%;display:block;">
        <?php endif; ?>
    </div>
    <div class="group-info" style="text-align:center;width:100%;">
        <div class="group-title" style="
            font-size:0.98em;
            font-weight:700;
            color:#5636d6;
            margin-bottom:6px;
            letter-spacing:1px;
            text-align:center;
        ">
            <?= htmlspecialchars($group['name']) ?>
        </div>
        <div class="group-category" style="
            font-size:0.85em;
            color:#fff;
            background:#3a7bd5;
            font-weight:500;
            border-radius:8px;
            padding:2px 10px;
            margin-bottom:6px;
            display:inline-block;
            text-align:center;
            box-shadow:0 2px 8px #3a7bd522;
        ">
            <?= ucfirst(htmlspecialchars($group['category'])) ?>
        </div>
        <div class="group-desc" style="
            font-size:0.85em;
            color:#555;
            margin-bottom:6px;
            word-break:break-all;
            text-align:center;
        ">
            <?= htmlspecialchars($group['description']) ?>
        </div>
    </div>
    <?php if ($is_member): ?>
        <a href="chat.php?group_id=<?= $group['id'] ?>" class="go-group-btn"
           style="
                margin-top:10px;
                width:100%;
                background: linear-gradient(135deg, #3a7bd5 0%, #6A11CB 100%);
                color:#fff;
                border:none;
                border-radius:8px;
                padding:7px 0;
                font-size:0.92em;
                font-weight:600;
                cursor:pointer;
                text-align:center;
                display:block;
                text-decoration:none;
                transition:background 0.2s,transform 0.2s;
                box-shadow:0 2px 8px #3a7bd522;
           ">
            <i class="bx bx-chat"></i> Go to Chat
        </a>
    <?php else: ?>
        <a href="?join=<?= $group['id'] ?>" class="go-group-btn"
           onclick="return confirm('Are you sure you want to join this group?');"
           style="
                margin-top:10px;
                width:100%;
                background: linear-gradient(135deg, #6A11CB 0%, #3a7bd5 100%);
                color:#fff;
                border:none;
                border-radius:8px;
                padding:7px 0;
                font-size:0.92em;
                font-weight:600;
                cursor:pointer;
                text-align:center;
                display:block;
                text-decoration:none;
                transition:background 0.2s,transform 0.2s;
                box-shadow:0 2px 8px #3a7bd522;
           ">
            <i class="bx bx-log-in"></i> Join the Group
        </a>
    <?php endif; ?>
</div>