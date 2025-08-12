<?php
session_start();
include("connection.php");

$message = "";
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $pin = mysqli_real_escape_string($con, $_POST['pin']);
    $user_id = $_SESSION['user_id'];

    // Find group by PIN (allow both public and private)
    $group_result = mysqli_query($con, "SELECT id, is_private FROM groups WHERE pin = '$pin' LIMIT 1");
    if ($group = mysqli_fetch_assoc($group_result)) {
        $group_id = $group['id'];
        // Check if user already joined
        $check = mysqli_query($con, "SELECT * FROM user_groups WHERE user_id = '$user_id' AND group_id = '$group_id'");
        if (mysqli_num_rows($check) == 0) {
            // Add user to group
            mysqli_query($con, "INSERT INTO user_groups (user_id, group_id) VALUES ('$user_id', '$group_id')");
            header("Location: group.php");
            exit();
        } else {
            $message = "You are already a member of this group.";
        }
    } else {
        $message = "Invalid PIN code.";
    }
}

// Check if the user is banned
$user_id = $_SESSION['user_id'];
$user_result = mysqli_query($con, "SELECT banned_until FROM users WHERE id = '$user_id' LIMIT 1");
$user_data = mysqli_fetch_assoc($user_result);
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
    <title>Join Group</title>
    <link rel="stylesheet" href="CSS code/join_group.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet">
</head>
<body>
<div class="modal-overlay" style="position:fixed;top:0;left:0;width:100vw;height:100vh;background:linear-gradient(135deg,#6A11CB,#2575FC);display:flex;align-items:center;justify-content:center;">
    <div class="create-group-modal" style="background:#fff;border-radius:24px;box-shadow:0 8px 32px #0002;max-width:380px;width:100%;padding:0;overflow:hidden;">
        <div class="modal-header" style="background:linear-gradient(90deg,#6A11CB,#2575FC);padding:20px 24px 12px 24px;display:flex;align-items:center;justify-content:space-between;">
            <div class="modal-title" style="display:flex;align-items:center;gap:10px;">
                <span class="group-icon" style="font-size:1.6em;">👥</span>
                <span style="font-size:1.2em;font-weight:600;color:#fff;">Join Group</span>
            </div>
            <button class="close-btn" onclick="window.history.back();" aria-label="Close" style="background:none;border:none;font-size:1.5em;color:#fff;opacity:0.8;cursor:pointer;">&times;</button>
        </div>
        <form method="POST" style="padding:32px 24px 24px 24px;">
            <div class="input-group" style="margin-bottom:24px;">
                <label for="gamePin" style="font-weight:600;color:#2575FC;display:block;margin-bottom:8px;">Group PIN</label>
                <input type="text" id="gamePin" name="pin" class="form-control"
                       placeholder="Enter group PIN..." required maxlength="5" minlength="4"
                       style="font-size:1.1em;padding:12px 16px;border-radius:12px;border:1.5px solid #6A11CB;background:#f6f8fa;display:block;width: 300px;">
            </div>
            <?php if ($message): ?>
                <p class="error" style="color:#ff4d4f;text-align:center;margin-bottom:16px;">
                    <?= $message ?>
                </p>
            <?php endif; ?>
            <button type="submit" class="create-btn" style="width:100%;padding:14px 0;font-size:1.1em;font-weight:600;border:none;border-radius:14px;background:linear-gradient(90deg,#6A11CB,#2575FC);color:#fff;box-shadow:0 2px 8px #0001;transition:background 0.2s;">
                Join Group
            </button>
        </form>
    </div>
</div>
<span class="group-type" style="background:#eaf3ff;color:#3a7bd5;padding:4px 12px;border-radius:10px;font-weight:500;">
    <?= $group['is_private'] ? 'Private' : 'Public' ?>
</span>
</body>
</html>