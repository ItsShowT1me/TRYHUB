<?php

session_start();
include("connection.php");

if (!isset($_SESSION['user_id'])) {
    header("Location: login_f1.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$query = "SELECT * FROM users WHERE user_id = '$user_id' LIMIT 1";
$result = mysqli_query($con, $query);
$user = mysqli_fetch_assoc($result);

if (isset($_POST['save_profile'])) {
    $about = mysqli_real_escape_string($con, $_POST['about']);
    $mbti = mysqli_real_escape_string($con, $_POST['mbti']);
    $email = mysqli_real_escape_string($con, $_POST['email']);
    $phone = mysqli_real_escape_string($con, $_POST['phone']);
    $interested_category = mysqli_real_escape_string($con, $_POST['interested_category'] ?? $user['interested_category']);
    $image_path = $user['image'] ?? null;

    if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
        $ext = strtolower(pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION));
        $allowed = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
        if (in_array($ext, $allowed)) {
            $upload_dir = 'uploads/';
            if (!is_dir($upload_dir)) mkdir($upload_dir, 0777, true);
            $filename = 'profile_' . $user_id . '_' . time() . '.' . $ext;
            $target = $upload_dir . $filename;
            if (move_uploaded_file($_FILES['image']['tmp_name'], $target)) {
                $image_path = $target;
            } else {
                echo "<div style='color:red;'>Failed to move uploaded file.</div>";
            }
        }
    }

    $update = "UPDATE users SET 
        about = '$about',
        mbti = '$mbti',
        email = '$email',
        phone = '$phone',
        image = " . ($image_path ? "'$image_path'" : "NULL") . ",
        interested_category = '$interested_category'
        WHERE user_id = '$user_id' LIMIT 1";
    if (!mysqli_query($con, $update)) {
        echo "<div style='color:red;'>Database update failed: " . mysqli_error($con) . "</div>";
    } else {
        header("Location: profile.php");
        exit();
    }
}

if (!empty($user['banned_until']) && strtotime($user['banned_until']) > time()) {
    $ban_time = date('d M Y H:i', strtotime($user['banned_until']));
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
    <title>Edit Profile</title>
    <link rel="stylesheet" href="css/bootstrap.min.css">
    <link rel="stylesheet" href="CSS code/join_group.css">
    <style>
        .edit-profile-card {
          background: #fff;
          border-radius: 32px;
          box-shadow: 0 8px 32px #3a7bd520;
          padding: 48px 40px 36px 40px;
          width: 420px;
          max-width: 98vw;
          margin: 40px auto;
          display: flex;
          flex-direction: column;
          align-items: center;
        }
        .edit-profile-title {
          color: #3a7bd5;
          font-size: 2rem;
          font-weight: 800;
          margin-bottom: 18px;
          text-align: center;
          letter-spacing: 1px;
        }
        .edit-profile-avatar {
          width: 100px;
          height: 100px;
          border-radius: 50%;
          overflow: hidden;
          margin-bottom: 24px;
          border: 4px solid #3a7bd5;
          box-shadow: 0 2px 8px #3a7bd520;
          background: #fff;
          display: flex;
          align-items: center;
          justify-content: center;
        }
        .edit-profile-avatar img {
          width: 100%;
          height: 100%;
          object-fit: cover;
          border-radius: 50%;
          display: block;
        }
        .edit-profile-form {
            position: relative;
        }
        .edit-profile-form .form-label {
          color: #3a7bd5;
          font-weight: 600;
          margin-bottom: 4px;
          font-size: 1rem;
        }
        .edit-profile-form .form-control {
          border-radius: 10px;
          border: 2px solid #eaf3ff;
          background: #f8faff;
          font-size: 1rem;
          margin-bottom: 12px;
          transition: border-color 0.2s;
        }
        .edit-profile-form .form-control:focus {
          border-color: #3a7bd5;
          box-shadow: 0 2px 8px #3a7bd522;
        }
        .edit-profile-btn {
          width: 100%;
          background: linear-gradient(90deg, #3a7bd5 0%, #764ba2 100%);
          color: #fff;
          text-align: center;
          padding: 12px 0;
          border-radius: 10px;
          font-weight: 700;
          font-size: 1.1rem;
          margin-top: 12px;
          text-decoration: none;
          transition: background 0.2s;
          box-shadow: 0 2px 8px #3a7bd522;
          border: none;
        }
        .edit-profile-btn:hover {
          background: linear-gradient(90deg, #764ba2 0%, #3a7bd5 100%);
        }
        .edit-profile-btn.cancel-btn {
            position: absolute;
            left: 0;
            top: -12px;
            width: auto;
            background: #eaf3ff;
            color: #3a7bd5;
            margin-top: 0;
            box-shadow: none;
            border: none;
            padding: 12px 32px;
            font-weight: 700;
            font-size: 1.08em;
            border-radius: 10px;
            text-align: left;
            transition: background 0.2s;
        }
        .edit-profile-btn.cancel-btn:hover {
            background: #dbeafe;
            color: #764ba2;
        }
        .close-modal-btn {
            position: absolute;
            top: 18px;
            right: 24px;
            background: none;
            border: none;
            font-size: 2.2em;
            color: #888;
            cursor: pointer;
            z-index: 10;
            transition: color 0.2s;
        }
        .close-modal-btn:hover {
            color: #DB504A;
        }
        .edit-profile-card {
    position: relative;
}
        @media (max-width: 900px) {
          .edit-profile-card { width: 98vw; padding: 24px 6vw; }
          .edit-profile-btn.cancel-btn {
              position: static;
              width: 100%;
              margin-top: 12px;
          }
        }
    </style>
</head>
<body>
<div class="container mt-5">
    <div class="edit-profile-card">
        <button type="button" onclick="window.location.href='profile.php'" class="close-modal-btn" aria-label="Close" style="
    position: absolute;
    top: 18px;
    right: 24px;
    background: none;
    border: none;
    font-size: 2.2em;
    color: #888;
    cursor: pointer;
    z-index: 10;
    transition: color 0.2s;
">
    &times;
</button>
        <div class="edit-profile-title">Edit Profile</div>
        <div class="edit-profile-avatar">
            <img src="<?= !empty($user['image']) ? htmlspecialchars($user['image']) : 'images/default-user.png' ?>" alt="Profile Image">
        </div>
        <form method="POST" enctype="multipart/form-data" class="edit-profile-form">
            <div class="mb-3">
                <label for="about" class="form-label">About Me</label>
                <textarea class="form-control" id="about" name="about" rows="3"><?php echo htmlspecialchars($user['about'] ?? ''); ?></textarea>
            </div>
            <div class="mb-3">
                <label for="mbti" class="form-label" style="text-transform: uppercase;">MBTI</label>
                <select class="form-control" id="mbti" name="mbti" style="text-transform: uppercase;">
                    <?php
                    $mbti_types = [
                        "INTJ","INTP","ENTJ","ENTP",
                        "INFJ","INFP","ENFJ","ENFP",
                        "ISTJ","ISFJ","ESTJ","ESFJ",
                        "ISTP","ISFP","ESTP","ESFP"
                    ];
                    foreach ($mbti_types as $type) {
                        $selected = (strtoupper($user['mbti']) == $type) ? 'selected' : '';
                        echo "<option value=\"$type\" $selected>$type</option>";
                    }
                    ?>
                </select>
            </div>
            <div class="mb-3">
                <label for="interested_category" class="form-label">Interested Category</label>
                <select class="form-control" id="interested_category" name="interested_category" required>
                    <?php
                    $categories = ['game','music','movie','sport','tourism','other'];
                    foreach ($categories as $cat) {
                        $selected = ($user['interested_category'] == $cat) ? 'selected' : '';
                        echo "<option value=\"$cat\" $selected>" . ucfirst($cat) . "</option>";
                    }
                    ?>
                </select>
            </div>
            <div class="mb-3">
                <label for="email" class="form-label">Email</label>
                <input type="email" class="form-control" id="email" name="email" value="<?php echo htmlspecialchars($user['email']); ?>">
            </div>
            <div class="mb-3">
                <label for="phone" class="form-label">Phone Number</label>
                <input type="text" class="form-control" id="phone" name="phone" value="<?php echo htmlspecialchars($user['phone']); ?>">
            </div>
            <div class="mb-3">
                <label for="image" class="form-label">Profile Image</label><br>
                <?php if (!empty($user['image'])): ?>
                    <img src="<?= htmlspecialchars($user['image']) ?>" alt="Profile Image" style="width:100px;height:100px;border-radius:50%;object-fit:cover;margin-bottom:10px;">
                <?php endif; ?>
                <input type="file" class="form-control" id="image" name="image" accept="image/*">
            </div>
            
            <button type="submit" name="save_profile" class="edit-profile-btn">Save</button>
            <a href="profile.php" class="edit-profile-btn cancel-btn">Cancel</a>
        </form>
    </div>
</div>
</body>
</html>