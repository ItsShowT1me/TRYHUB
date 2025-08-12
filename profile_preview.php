<?php
session_start();
include("connection.php");

$user_id = isset($_GET['user_id']) ? intval($_GET['user_id']) : 0;
$user = null;
if ($user_id) {
    $res = mysqli_query($con, "SELECT user_name, mbti, about, image, email, phone FROM users WHERE user_id = '$user_id' LIMIT 1");
    $user = mysqli_fetch_assoc($res);
}
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Profile Preview</title>
    <link rel="stylesheet" href="CSS code/chat.css">
    <link rel="stylesheet" href="CSS code/group.css">
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
    <style>
        html, body {
            height: 100%;
            margin: 0;
            padding: 0;
            background: transparent;
            font-family: 'Poppins', Arial, sans-serif;
        }
        body {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .preview-card {
            background: linear-gradient(135deg, #e0e7ff 0%, #f8fafc 100%);
            border-radius: 22px;
            box-shadow: 0 8px 32px #3a7bd540;
            width: 340px;
            padding: 38px 32px 28px 32px;
            display: flex;
            flex-direction: column;
            align-items: center;
            position: relative;
            animation: fadeIn 0.25s;
            margin: auto;
        }
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(30px);}
            to { opacity: 1; transform: translateY(0);}
        }
        .preview-image {
            width: 96px;
            height: 96px;
            border-radius: 50%;
            overflow: hidden;
            margin-bottom: 18px;
            border: 3px solid #3a7bd5;
            box-shadow: 0 2px 8px #3a7bd520;
            background: #fff;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .preview-image img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            border-radius: 50%;
            display: block;
        }
        .preview-initial {
            font-size: 2.6em;
            color: #fff;
            background: linear-gradient(135deg,#3a7bd5 0%,#764ba2 100%);
            width: 100%;
            height: 100%;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 50%;
            font-weight: 700;
            letter-spacing: 2px;
        }
        .preview-name {
            font-size: 1.32em;
            font-weight: 700;
            color: #3a7bd5;
            margin-bottom: 8px;
            text-align: center;
            word-break: break-word;
            letter-spacing: 1px;
        }
        .preview-mbti {
            background: linear-gradient(90deg,#667eea 0%,#3a7bd5 100%);
            color: #fff;
            padding: 5px 18px;
            border-radius: 14px;
            font-weight: 600;
            font-size: 1.08em;
            margin-bottom: 14px;
            display: inline-block;
            box-shadow: 0 1px 4px #667eea22;
            letter-spacing: 1px;
        }
        .preview-about {
            color: #444;
            font-size: 1.08em;
            margin-bottom: 0;
            text-align: center;
            line-height: 1.7;
            background: #f8faff;
            border-radius: 10px;
            padding: 12px 10px;
            min-height: 48px;
            box-shadow: 0 1px 4px #3a7bd510;
            width: 100%;
        }
        .close-preview-btn {
            position: absolute;
            top: 18px;
            right: 24px;
            background: none;
            border: none;
            font-size: 2em;
            color: #888;
            cursor: pointer;
            z-index: 10;
            transition: color 0.2s;
        }
        .close-preview-btn:hover { color: #DB504A; }
        .preview-card { position: relative; }
        .preview-label {
            font-size: 0.98em;
            color: #888;
            margin-bottom: 2px;
            font-weight: 500;
            letter-spacing: 0.5px;
        }
        .preview-contact {
            width: 100%;
            margin-top: 16px;
            margin-bottom: 8px;
            text-align: left;
        }
        .contact-row {
            display: flex;
            align-items: center;
            gap: 10px;
            margin-bottom: 8px;
            font-size: 1.05em;
        }
        .contact-icon {
            color: #3a7bd5;
            font-size: 1.2em;
        }
        .contact-value {
            color: #222;
            font-weight: 500;
            word-break: break-all;
        }
        @media (max-width: 500px) {
            .preview-card { width: 98vw; padding: 18px 4vw 18px 4vw;}
            .preview-image { width: 70px; height: 70px;}
            .preview-name { font-size: 1.1em;}
            .preview-mbti { font-size: 0.98em; padding: 4px 10px;}
            .preview-about { font-size: 0.98em; padding: 8px 4px;}
            .contact-row { font-size: 0.98em; }
        }
    </style>
</head>
<body>
    <?php if ($user): ?>
    <div class="preview-card">
        
        <div class="preview-image">
            <?php if (!empty($user['image'])): ?>
                <img src="<?= htmlspecialchars($user['image']) ?>" alt="Profile">
            <?php else: ?>
                <span class="preview-initial"><?= strtoupper($user['user_name'][0]) ?></span>
            <?php endif; ?>
        </div>
        <div class="preview-name"><?= htmlspecialchars($user['user_name']) ?></div>
        <div class="preview-label">MBTI Type</div>
        <div class="preview-mbti"><?= htmlspecialchars($user['mbti'] ?: 'Not set') ?></div>
        <div class="preview-label">About</div>
        <div class="preview-about"><?= !empty($user['about']) ? nl2br(htmlspecialchars($user['about'])) : '<span style="color:#aaa;">No description provided.</span>' ?></div>
        <div class="preview-contact">
            <div class="preview-label">Contact</div>
            <div class="contact-row">
                <i class='bx bx-envelope contact-icon'></i>
                <span class="contact-value"><?= !empty($user['email']) ? htmlspecialchars($user['email']) : '<span style="color:#aaa;">No email</span>' ?></span>
            </div>
            <div class="contact-row">
                <i class='bx bx-phone contact-icon'></i>
                <span class="contact-value"><?= !empty($user['phone']) ? htmlspecialchars($user['phone']) : '<span style="color:#aaa;">No phone number</span>' ?></span>
            </div>
        </div>
    </div>
    <?php else: ?>
    <div class="preview-card">
        <div style="color:#DB504A;text-align:center;font-weight:600;">User not found.</div>
    </div>
    <?php endif; ?>
</body>
</html>