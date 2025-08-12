<?php
session_start();
include("connection.php");



// ดึงข้อมูลผู้ใช้
$user_id = $_SESSION['user_id'];
$query = "SELECT * FROM users WHERE user_id = '$user_id' LIMIT 1";
$result = mysqli_query($con, $query);
$user = mysqli_fetch_assoc($result);

if (!$user) {
    echo "<!DOCTYPE html><html><head><title>Profile</title></head><body>";
    echo "<div style='padding:40px;'><h2 style='color:#3a7bd5;'>User not found.</h2><p>Please check your account or contact support.</p></div>";
    echo "</body></html>";
    exit();
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
<style>
    /* Add to CSS code/profile.css or in a <style> tag */
    /* Center the card */
    .profile-center-container {
      min-height: 100vh;
      display: flex;
      align-items: center;
      justify-content: center;
      background: linear-gradient(135deg, #e3eafc 0%, #f5f8ff 100%);
    }
    
    /* Card styling */
    .profile-card-modern {
      background: #fff;
      border-radius: 24px;
      box-shadow: 0 8px 32px rgba(58,123,213,0.10);
      padding: 40px 36px 32px 36px;
      width: 400px;
      max-width: 95vw;
      display: flex;
      flex-direction: column;
      align-items: center;
    }
    
    .profile-title {
      color: #3686ea;
      font-size: 2rem;
      font-weight: 700;
      margin-bottom: 8px;
      text-align: center;
    }
    
    .profile-subtitle {
      color: #555;
      font-size: 1.05rem;
      margin-bottom: 18px;
      text-align: center;
    }
    
    .profile-avatar {
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
    
    .profile-avatar img {
      width: 100%;
      height: 100%;
      object-fit: cover;
      border-radius: 50%;
      display: block;
    }
    
    .profile-field {
      width: 100%;
      margin-bottom: 16px;
      display: flex;
      flex-direction: column;
      align-items: flex-start;
    }
    
    .profile-field label {
      font-weight: 600;
      color: #3686ea;
      margin-bottom: 4px;
      font-size: 1rem;
    }
    
    .profile-field input[type="text"] {
      width: 100%;
      background: #f5f8ff;
      border: none;
      border-radius: 8px;
      padding: 8px 12px;
      font-size: 1rem;
      color: #333;
      margin-bottom: 0;
      outline: none;
    }
    
    .profile-field input[readonly] {
      pointer-events: none;
    }
    
    .profile-field a {
      color: #3686ea;
      text-decoration: underline;
      font-size: 1rem;
    }
    
    .profile-no-link {
      color: #aaa;
      font-size: 1rem;
    }
    
    .profile-edit-btn {
      display: block;
      width: 100%;
      background: linear-gradient(90deg, #3686ea 0%, #3a7bd5 100%);
      color: #fff;
      text-align: center;
      padding: 12px 0;
      border-radius: 8px;
      font-weight: 600;
      font-size: 1.1rem;
      margin-top: 12px;
      text-decoration: none;
      transition: background 0.2s;
    }
    
    .profile-edit-btn:hover {
      background: linear-gradient(90deg, #3a7bd5 0%, #3686ea 100%);
    }
    #sidebar {
      z-index: 1 !important; /* Make sure sidebar is under modal */
    }
    @media (max-width: 768px) {
    .profile-box {
        flex-direction: column;
        align-items: center;
        padding: 16px;
        gap: 16px;
    }
}
.profile-wide-container {
  min-height: 100vh;
  display: flex;
  align-items: flex-start;
  justify-content: center;
  background: linear-gradient(135deg, #3a7bd5 0%, #6a11cb 100%);
  padding-top: 40px;
}
.profile-wide-card {
  background: transparent;
  width: 700px;
  max-width: 98vw;
  margin: 0 auto;
  padding: 40px 40px 32px 40px;
  border-radius: 16px;
  display: flex;
  flex-direction: column;
  align-items: flex-start;
}
.profile-wide-title {
  color: #fff;
  font-size: 2.5rem;
  font-weight: 700;
  margin-bottom: 8px;
  text-align: left;
}
.profile-wide-subtitle {
  color: #e0e0e0;
  font-size: 1.1rem;
  margin-bottom: 18px;
  text-align: left;
}
.profile-wide-avatar {
  width: 120px;
  height: 120px;
  border-radius: 50%;
  overflow: hidden;
  margin: 0 0 24px 0;
  border: 4px solid #fff;
  box-shadow: 0 2px 8px #3a7bd520;
  background: #fff;
  display: flex;
  align-items: center;
  justify-content: center;
}
.profile-wide-avatar img {
  width: 100%;
  height: 100%;
  object-fit: cover;
  border-radius: 50%;
  display: block;
}
.profile-about-section {
  background: rgba(255,255,255,0.08);
  border-left: 4px solid #fff;
  padding: 16px 24px;
  margin-bottom: 28px;
  width: 100%;
}
.profile-about-title {
  color: #fff;
  font-weight: 600;
  font-size: 1.15rem;
  margin-bottom: 6px;
}
.profile-about-desc {
  color: #fff;
  font-size: 1rem;
}
.profile-info-list {
  width: 100%;
  margin-bottom: 18px;
}
.profile-info-label {
  color: #fff;
  font-weight: 500;
  margin-top: 18px;
  margin-bottom: 2px;
}
.profile-info-value {
  color: #fff;
  border-bottom: 2px dashed #fff;
  padding-bottom: 6px;
  margin-bottom: 2px;
  font-size: 1.08rem;
  word-break: break-all;
}
.profile-info-list a {
  color: #fff;
  text-decoration: underline;
}
.profile-no-link {
  color: #e0e0e0;
}
.profile-wide-edit-btn {
  margin-top: 24px;
  background: #fff;
  color: #3a7bd5;
  font-weight: 600;
  border-radius: 6px;
  padding: 10px 0;
  width: 100px;
  text-align: center;
  text-decoration: none;
  font-size: 1.1rem;
  transition: background 0.2s, color 0.2s;
  display: inline-block;
}
.profile-wide-edit-btn:hover {
  background: #3a7bd5;
  color: #fff;
}
@media (max-width: 900px) {
  .profile-wide-card { width: 98vw; padding: 24px 6vw; }
}
</style>
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Profile</title>
  <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
  <link rel="stylesheet" href="css/bootstrap.min.css">
  <link rel="stylesheet" href="CSS code/index.css">
</head>
<body>
  <!-- Sidebar -->
  <nav id="sidebar">
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
</nav>
  <div class="container">
    
<div class="profile-wide-container">
  <div class="profile-wide-card">
    <h2 class="profile-wide-title">Welcome! To Profile</h2>
    <p class="profile-wide-subtitle"><i>Hi, my name is <?= htmlspecialchars($user['user_name']) ?>. This is my profile page. Feel free to explore!</i></p>
    <div class="profile-wide-avatar">
      <img src="<?= !empty($user['image']) ? htmlspecialchars($user['image']) : 'images/default-user.png' ?>" alt="<?= htmlspecialchars($user['user_name']) ?>">
    </div>
    <div class="profile-about-section">
      <div class="profile-about-title">About Me</div>
      <div class="profile-about-desc"><?= !empty($user['about']) ? htmlspecialchars($user['about']) : "I'm a web developer with a passion for creating interactive and user-friendly applications. I love coding, learning new technologies, and collaborating with others." ?></div>
    </div>
    <div class="profile-info-list">
      <div class="profile-info-label">Name</div>
      <div class="profile-info-value"><?= htmlspecialchars($user['user_name']) ?></div>
      <div class="profile-info-label">Email</div>
      <div class="profile-info-value"><?= htmlspecialchars($user['email']) ?></div>
      <div class="profile-info-label">Phone</div>
      <div class="profile-info-value"><?= htmlspecialchars($user['phone']) ?></div>
      <div class="profile-info-label">MBTI Type</div>
      <div class="profile-info-value">
          <?= !empty($user['mbti']) ? htmlspecialchars($user['mbti']) : '<span class="profile-no-link">Not set</span>' ?>
      </div>
      
      <div class="profile-info-label">Interested in Group</div>
      <div class="profile-info-value">
          <?= !empty($user['interested_category']) ? ucfirst(htmlspecialchars($user['interested_category'])) : '<span class="profile-no-link">Not set</span>' ?>
      </div>
    </div>
    <a href="edit_profile.php" class="profile-wide-edit-btn">Edit</a>
  </div>
</div>
  
  </div>

  <!-- Bootstrap JS (for modal) -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
  
</body>
</html>
