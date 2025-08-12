<?php

session_start();
include("connection.php");

$group_id = isset($_GET['group_id']) ? intval($_GET['group_id']) : 0;
$user_id = $_SESSION['user_id'] ?? 0;

// Find group and owner
$group = mysqli_fetch_assoc(mysqli_query($con, "SELECT * FROM groups WHERE id = '$group_id'"));
$owner_row = mysqli_fetch_assoc(mysqli_query($con, "SELECT user_id FROM user_groups WHERE group_id = '$group_id' ORDER BY id ASC LIMIT 1"));
$owner_id = $owner_row ? $owner_row['user_id'] : 0;

// Only owner can edit
if ($user_id != $owner_id) {
    die("You are not the owner of this group.");
}

// Handle group update
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_group'])) {
    $name = mysqli_real_escape_string($con, $_POST['name']);
    $description = mysqli_real_escape_string($con, $_POST['description']);
    $color = mysqli_real_escape_string($con, $_POST['color']);
    $category = mysqli_real_escape_string($con, $_POST['category']);
    $is_private = isset($_POST['is_private']) ? intval($_POST['is_private']) : 0;
    $pin = $is_private ? mysqli_real_escape_string($con, $_POST['pin']) : $group['pin'];
    mysqli_query($con, "UPDATE groups SET name='$name', description='$description', color='$color', category='$category', is_private='$is_private', pin='$pin' WHERE id='$group_id'");
    // Handle image upload
    $image_path = '';
    if (isset($_FILES['group_image']) && $_FILES['group_image']['error'] == UPLOAD_ERR_OK) {
        $ext = pathinfo($_FILES['group_image']['name'], PATHINFO_EXTENSION);
        $image_path = 'uploads/group_' . time() . '_' . rand(1000,9999) . '.' . $ext;
        move_uploaded_file($_FILES['group_image']['tmp_name'], $image_path);
        mysqli_query($con, "UPDATE groups SET image='$image_path' WHERE id='$group_id'");
    }
    // Redirect back to chat page after update
    header("Location: chat.php?group_id=$group_id");
    exit();
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Edit Group</title>
    <link rel="stylesheet" href="CSS code/create_group.css">
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
    <style>
        body {
            background: linear-gradient(120deg, #eaf3ff 0%, #fff 100%);
            font-family: 'Poppins', sans-serif;
        }
        .modal-overlay {
            position: fixed;
            top: 0; left: 0;
            width: 100vw; height: 100vh;
            background: rgba(106,17,203,0.12);
            display: flex;
            align-items: center;
            justify-content: center;
            z-index: 1000;
            animation: fadeInModal 0.2s;
        }
        @keyframes fadeInModal {
            from { opacity: 0; }
            to { opacity: 1; }
        }
        .create-group-modal {
            background: #fff;
            border-radius: 32px;
            width: 420px;
            max-width: 98vw;
            box-shadow: 0 10px 32px #342e3720;
            position: relative;
            overflow: hidden;
            animation: slideUpModal 0.25s;
            max-height: 90vh;
            overflow-y: auto;
        }
        @keyframes slideUpModal {
            from { transform: translateY(40px); opacity: 0; }
            to { transform: translateY(0); opacity: 1; }
        }
        .modal-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: #fff;
            padding: 22px 32px 18px 32px;
            border-radius: 32px 32px 0 0;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }
        .modal-title {
            display: flex;
            align-items: center;
            gap: 14px;
            font-size: 1.35rem;
            font-weight: 700;
            letter-spacing: 1px;
        }
        .group-icon {
            font-size: 1.7rem;
        }
        .create-group-modal form {
            padding: 28px 32px 24px 32px;
        }
        .input-group {
            margin-bottom: 18px;
        }
        .input-group label {
            display: block;
            color: #3686ea;
            font-weight: 600;
            margin-bottom: 8px;
            font-size: 1rem;
            letter-spacing: 0.5px;
        }
        .input-group input[type="text"],
        .input-group input[type="color"],
        .input-group textarea,
        .input-group select {
            width: 100%;
            padding: 12px 16px;
            border: 2px solid #e3e8f0;
            border-radius: 12px;
            font-size: 1rem;
            transition: border-color 0.2s, box-shadow 0.2s;
            outline: none;
            background: #f7f9fb;
            margin-top: 4px;
        }
        .input-group input[type="text"]:focus,
        .input-group textarea:focus,
        .input-group select:focus {
            border-color: #667eea;
            box-shadow: 0 2px 8px #667eea22;
        }
        .input-group input[type="color"] {
            width: 48px;
            height: 38px;
            border-radius: 8px;
            cursor: pointer;
            margin-right: 10px;
            background: #fff;
            border: 2px solid #e3e8f0;
            padding: 2px;
        }
        .color-container {
            display: flex;
            align-items: center;
            gap: 14px;
            margin-top: 8px;
        }
        #colorPreview {
            display: inline-block;
            width: 32px;
            height: 32px;
            border-radius: 8px;
            border: 2px solid #e3e8f0;
            background: #3a7bd5;
            box-shadow: 0 1px 4px #667eea22;
        }
        .input-group textarea {
            resize: vertical;
            min-height: 70px;
            max-height: 180px;
        }
        .create-btn {
            width: 100%;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: #fff;
            border: none;
            padding: 14px;
            border-radius: 14px;
            font-size: 1.1rem;
            font-weight: 700;
            cursor: pointer;
            transition: transform 0.2s, box-shadow 0.2s;
            margin-top: 10px;
            box-shadow: 0 2px 8px #667eea22;
        }
        .create-btn:hover {
            transform: translateY(-2px) scale(1.03);
            box-shadow: 0 4px 16px #764ba240;
        }
        .edit-success {
            background: #eafbe7;
            color: #2e7d32;
            padding: 10px 18px;
            border-radius: 8px;
            margin-bottom: 16px;
            font-size: 1.08em;
            display: inline-block;
        }
        .input-group img {
            width: 60px;
            height: 60px;
            border-radius: 14px;
            margin-bottom: 8px;
            box-shadow: 0 2px 8px #667eea22;
            object-fit: cover;
            border: 2px solid #eaf3ff;
        }
        @media (max-width: 600px) {
            .create-group-modal {
                width: 98vw;
                padding: 0;
            }
            .modal-header, .create-group-modal form {
                padding: 18px 8px;
            }
            .input-group img {
                width: 40px;
                height: 40px;
            }
        }
        body {
            overflow: auto !important;
        }
    </style>
</head>
<body>
<div class="modal-overlay">
    <div class="create-group-modal">
        <div class="modal-header">
            <span class="modal-title"><i class="bx bxs-group group-icon"></i> Edit Group</span>
            <a href="chat.php?group_id=<?= $group_id ?>" class="return-btn" style="
                background: #eaf3ff;
                color: #3a7bd5;
                border: none;
                border-radius: 8px;
                padding: 8px 18px;
                font-size: 1em;
                cursor: pointer;
                text-decoration: none;
                display: inline-flex;
                align-items: center;
                gap: 8px;
                font-weight: 600;
                box-shadow: 0 2px 8px #3a7bd522;
                transition: background 0.2s, color 0.2s;
            " onmouseover="this.style.background='#3a7bd5';this.style.color='#fff'" onmouseout="this.style.background='#eaf3ff';this.style.color='#3a7bd5'">
                <i class="bx bx-arrow-back"></i> Return
            </a>
        </div>
        <?php if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_group'])): ?>
            <div class="edit-success"><i class="bx bx-check-circle"></i> Group updated successfully.</div>
        <?php endif; ?>
        <form method="POST" enctype="multipart/form-data">
            <div class="input-group">
                <label for="name">Group Name</label>
                <input type="text" name="name" id="name" value="<?= htmlspecialchars($group['name']) ?>" required>
            </div>
            <div class="input-group">
                <label for="description">Description</label>
                <textarea name="description" id="description" required><?= htmlspecialchars($group['description']) ?></textarea>
            </div>
            <div class="input-group color-container">
                <label for="color">Color</label>
                <input type="color" name="color" id="color" value="<?= htmlspecialchars($group['color']) ?>">
                <span id="colorPreview" style="background:<?= htmlspecialchars($group['color']) ?>"></span>
            </div>
            <div class="input-group">
                <label for="category">Category</label>
                <select name="category" id="category" required>
                    <option value="game" <?= $group['category']=='game'?'selected':'' ?>>Game</option>
                    <option value="music" <?= $group['category']=='music'?'selected':'' ?>>Music</option>
                    <option value="movie" <?= $group['category']=='movie'?'selected':'' ?>>Movie</option>
                    <option value="sport" <?= $group['category']=='sport'?'selected':'' ?>>Sport</option>
                    <option value="tourism" <?= $group['category']=='tourism'?'selected':'' ?>>Tourism</option>
                    <option value="other" <?= $group['category']=='other'?'selected':'' ?>>Other</option>
                </select>
            </div>
            <div class="input-group">
                <label for="is_private">Group Type</label>
                <select name="is_private" id="is_private" required>
                    <option value="0" <?= $group['is_private']==0?'selected':'' ?>>Public</option>
                    <option value="1" <?= $group['is_private']==1?'selected':'' ?>>Private</option>
                </select>
            </div>
            <div class="input-group" id="pin-group" style="<?= $group['is_private']?'':'display:none;' ?>">
                <label for="pin">Group PIN (4-5 digits)</label>
                <input type="text" name="pin" id="pin" pattern="\d{4,5}" maxlength="5" minlength="4" value="<?= htmlspecialchars($group['pin']) ?>">
            </div>
            <div class="input-group">
                <label for="group_image">Group Image</label>
                <?php if (!empty($group['image'])): ?>
                    <img src="<?= htmlspecialchars($group['image']) ?>" alt="Group Image">
                <?php endif; ?>
                <input type="file" name="group_image" accept="image/*">
            </div>
            <button type="submit" name="update_group" class="create-btn">Update Group</button>
        </form>
    </div>
</div>
<script>
document.getElementById('color').addEventListener('input', function() {
    document.getElementById('colorPreview').style.background = this.value;
});
document.getElementById('is_private').addEventListener('change', function() {
    document.getElementById('pin-group').style.display = this.value == "1" ? "" : "none";
});
</script>
</body>
</html>