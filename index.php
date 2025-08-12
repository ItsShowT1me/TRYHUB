<?php
session_start(); // <-- Always first!
include 'function.php';
include 'connection.php';

$user_data = check_login($con);

// Check if user is banned
if (!empty($user_data['banned_until']) && strtotime($user_data['banned_until']) > time()) {
    $ban_time = date('d M Y H:i', strtotime($user_data['banned_until']));
    echo "<div style='background:#ffeaea;color:#DB504A;padding:18px 24px;border-radius:12px;margin:32px auto;max-width:420px;text-align:center;font-size:1.15em;font-weight:600;box-shadow:0 2px 12px #DB504A22;'>
        <i class='bx bxs-error' style='font-size:2em;vertical-align:middle;'></i>
        <br>
        You are banned until <span style='color:#b92d23;'>$ban_time</span>.
        <br>
        Please contact support if you believe this is a mistake.<br><br>
        <button onclick=\"window.location.href='login.php'\" style='background:#DB504A;color:#fff;border:none;border-radius:8px;padding:10px 32px;font-size:1em;font-weight:600;cursor:pointer;box-shadow:0 2px 8px #DB504A22;'>OK</button>
    </div>";
    exit();
}

// Handle join group action
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

// Handle interest category selection and save to database
if (isset($_POST['interestCategory']) && isset($_SESSION['user_id'])) {
    $interest = mysqli_real_escape_string($con, $_POST['interestCategory']);
    $user_id = $_SESSION['user_id'];
    mysqli_query($con, "UPDATE users SET interested_category='$interest' WHERE user_id='$user_id'");
    $_SESSION['interested_category'] = $interest;
    $show_interest_modal = false;
}

// Get user's interested category
$interested_category = '';
if (isset($_SESSION['user_id'])) {
    $user_id = $_SESSION['user_id'];
    $result_interest = mysqli_query($con, "SELECT interested_category FROM users WHERE user_id='$user_id'");
    if ($row = mysqli_fetch_assoc($result_interest)) {
        $interested_category = $row['interested_category'];
    }
}

// Fetch only public groups, filtered by interest if set
$groups = [];
$group_sql = "SELECT id, group_id, name, color, description, category FROM groups WHERE is_private = 0";
if ($interested_category) {
    $group_sql .= " AND category = '" . mysqli_real_escape_string($con, $interested_category) . "'";
}
$group_sql .= " ORDER BY created_at DESC";
$result = mysqli_query($con, $group_sql);
while ($row = mysqli_fetch_assoc($result)) {
    $groups[] = $row;
}

// Check if user has joined any group
$show_interest_modal = false;
if (isset($_SESSION['user_id'])) {
    $user_id = $_SESSION['user_id'];
    $check_group = mysqli_query($con, "SELECT 1 FROM user_groups WHERE user_id = '$user_id' LIMIT 1");
    if (mysqli_num_rows($check_group) == 0) {
        $show_interest_modal = true;
    }
}

// Layer 1: Interested groups by user's interested_category
$interested_groups = [];
if ($interested_category) {
    $sql = "SELECT id, group_id, name, color, image, description, category FROM groups WHERE is_private = 0 AND category = '" . mysqli_real_escape_string($con, $interested_category) . "' ORDER BY created_at DESC LIMIT 6";
    $result = mysqli_query($con, $sql);
    while ($row = mysqli_fetch_assoc($result)) {
        $interested_groups[] = $row;
    }
}

// Layer 2: Popular groups by user's MBTI (most members with same MBTI)
$popular_groups = [];
if (!empty($user_data['mbti'])) {
    $mbti = mysqli_real_escape_string($con, $user_data['mbti']);
    $sql = "
        SELECT g.id, g.group_id, g.name, g.color, g.image, g.description, g.category, COUNT(u.id) as mbti_count
        FROM groups g
        JOIN user_groups ug ON g.id = ug.group_id
        JOIN users u ON ug.user_id = u.user_id
        WHERE g.is_private = 0 AND u.mbti = '$mbti'
        GROUP BY g.id
        ORDER BY mbti_count DESC, g.created_at DESC
        LIMIT 6
    ";
    $result = mysqli_query($con, $sql);
    while ($row = mysqli_fetch_assoc($result)) {
        $popular_groups[] = $row;
    }
}

// Layer 3: Random public groups
$random_groups = [];
$sql = "SELECT id, group_id, name, color, image, description, category FROM groups WHERE is_private = 0 ORDER BY RAND() LIMIT 6";
$result = mysqli_query($con, $sql);
while ($row = mysqli_fetch_assoc($result)) {
    $random_groups[] = $row;
}

function paginate_groups($groups, $layer_name) {
    $groups_per_page = 4;
    $page = isset($_GET[$layer_name . '_page']) ? max(1, intval($_GET[$layer_name . '_page'])) : 1;
    $total = count($groups);
    $total_pages = ceil($total / $groups_per_page);
    $start = ($page - 1) * $groups_per_page;
    $paged_groups = array_slice($groups, $start, $groups_per_page);

    return [
        'groups' => $paged_groups,
        'page' => $page,
        'total_pages' => $total_pages
    ];
}

// Paginate each layer
$interested_pagination = paginate_groups($interested_groups, 'interested');
$popular_pagination = paginate_groups($popular_groups, 'popular');
$random_pagination = paginate_groups($random_groups, 'random');
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>BUMBTI</title>
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
    <?php $ver = time() - (time() % 60); // changes every minute ?>
    <link rel="stylesheet" href="css/bootstrap.min.css?v=<?= $ver ?>">
    <link rel="stylesheet" href="CSS code/index.css?v=<?= $ver ?>">
    <script src="JS code/index.js?v=<?= $ver ?>"></script>
    <script src="js/bootstrap.min.js?v=<?= $ver ?>"></script>
</head>
<body>
  <div class="container">
      <!-- Header -->
      <header class="top-header" style="position:fixed;top:0;left:240px;right:0;height:90px;background:#fff;display:flex;flex-direction:row;align-items:center;justify-content:space-between;z-index:100;box-shadow:0 2px 8px #7b26e9;padding:0 32px;">
    <div>
        
        <form method="POST" style="margin-top:8px;display:flex;justify-content:center;">
            <div style="display:flex;gap:18px;align-items:center;justify-content:center;background:#f8faff;padding:10px 0;border-radius:16px;box-shadow:0 2px 12px #3a7bd522;width:fit-content;">
                <?php
                $categories = ['music','sport','movie','game','tourism'];
                foreach ($categories as $cat):
                ?>
                    <button type="submit" name="interestCategory" value="<?= $cat ?>"
                        style="background:<?= ($interested_category == $cat) ? 'linear-gradient(90deg,#3a7bd5 0%,#764ba2 100%)' : 'none' ?>;
                               color:<?= ($interested_category == $cat) ? '#fff' : '#222' ?>;
                               border:none;font-size:1.08em;padding:8px 22px;border-radius:8px;cursor:pointer;font-weight:600;transition:background 0.18s, color 0.18s;box-shadow:<?= ($interested_category == $cat) ? '0 2px 8px #3a7bd522' : 'none' ?>;">
                        <?= ucfirst($cat) ?>
                    </button>
                <?php endforeach; ?>
                <button type="submit" name="interestCategory" value="all"
                    style="background:<?= ($interested_category == 'all' || !$interested_category) ? 'linear-gradient(90deg,#3a7bd5 0%,#764ba2 100%)' : 'none' ?>;
                           color:<?= ($interested_category == 'all' || !$interested_category) ? '#fff' : '#222' ?>;
                           font-size:1.08em;padding:8px 22px;border-radius:8px;cursor:pointer;font-weight:700;border:none;box-shadow:<?= ($interested_category == 'all' || !$interested_category) ? '0 2px 8px #3a7bd522' : 'none' ?>;">
                    All &nbsp; <i class="bx bx-chevron-right"></i>
                </button>
            </div>
        </form>
    </div>
    <div class="user-profile-bar" style="display:flex;align-items:center;gap:18px;">
        <div class="user-profile-info" style="display:flex;align-items:center;gap:10px;">
            <a href="profile.php" style="display:flex;align-items:center;text-decoration:none;gap:10px;">
                <?php
                $profile_img = !empty($user_data['image']) ? $user_data['image'] : '';
                if ($profile_img && file_exists($profile_img)) {
                    echo '<img src="' . htmlspecialchars($profile_img) . '" alt="Profile" style="width:38px;height:38px;border-radius:50%;object-fit:cover;border:2px solid #3a7bd5;">';
                } else {
                    echo '<i class="bx bx-user-circle" style="font-size:2em;color:#3a7bd5;"></i>';
                }
                ?>
                <span style="font-weight:600;font-size:1.08em;color:#222;">
                    <?= htmlspecialchars($user_data['user_name'] ?? 'User') ?>
                </span>
            </a>
        </div>
    </div>
</header>
  </div>

  <!-- Sidebar -->
  <nav id="sidebar">
        <a href="#" class="brand">
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



<!-- Main grid layout for layers -->
<div class="main-layers-grid">
    <!-- Layer 1: Interested Groups (row 1, col 1) -->
    <section class="layer layer-1">
        <h2 style="font-size:1.2em;font-weight:700;color:#3a7bd5;margin-bottom:18px;">Groups Based on Your Interest</h2>
        <div class="group-grid">
            <?php foreach ($interested_pagination['groups'] as $group): ?>
                <?php include 'group_card_template.php'; ?>
            <?php endforeach; ?>
            <?php if (empty($interested_pagination['groups'])): ?>
                <div style="color:#aaa;font-size:1.1em;">No groups found for your interest.</div>
            <?php endif; ?>
        </div>
        <?php if ($interested_pagination['total_pages'] > 1): ?>
            <div class="pagination">
                <?php for ($i = 1; $i <= $interested_pagination['total_pages']; $i++): ?>
                    <a href="?interested_page=<?= $i ?>" class="<?= $i == $interested_pagination['page'] ? 'active' : '' ?>"><?= $i ?></a>
                <?php endfor; ?>
            </div>
        <?php endif; ?>
    </section>
    <!-- Layer 2: Popular Groups by MBTI (row 1, col 2) -->
    <section class="layer layer-2">
        <h2 style="font-size:1.2em;font-weight:700;color:#5636d6;margin-bottom:18px;">
        Popular Groups for Your MBTI (<?= htmlspecialchars($user_data['mbti'] ?? '') ?>)
    </h2>

    <?php if (empty($user_data['mbti'])): ?>
        <div style="background:#fffbe6;border-radius:18px;box-shadow:0 2px 12px #ffe06644;padding:32px 24px;margin-bottom:24px;text-align:center;">
            <div style="font-size:1.15em;font-weight:600;color:#5636d6;margin-bottom:18px;">
                You haven't set your MBTI yet!
            </div>
            <div style="display:flex;justify-content:center;gap:18px;">
                <a href="https://www.16personalities.com/free-personality-test" target="_blank"
                   style="background:linear-gradient(90deg,#3a7bd5 0%,#764ba2 100%);color:#fff;font-weight:700;border-radius:10px;padding:12px 28px;font-size:1.08em;text-decoration:none;box-shadow:0 2px 8px #3a7bd522;transition:background 0.2s;">
                    🧭 Find Your MBTI
                </a>
                <a href="edit_profile.php"
                   style="background:#eaf3ff;color:#3a7bd5;font-weight:700;border-radius:10px;padding:12px 28px;font-size:1.08em;text-decoration:none;box-shadow:0 2px 8px #3a7bd522;transition:background 0.2s;">
                    ✏️ Set Your MBTI
                </a>
            </div>
        </div>
    <?php endif; ?>

    <div class="group-grid">
        <?php foreach ($popular_pagination['groups'] as $group): ?>
            <?php include 'group_card_template.php'; ?>
        <?php endforeach; ?>
        <?php if (empty($popular_pagination['groups'])): ?>
            <div style="color:#aaa;font-size:1.1em;">No popular groups for your MBTI yet.</div>
        <?php endif; ?>
    </div>
    <?php if ($popular_pagination['total_pages'] > 1): ?>
        <div class="pagination">
            <?php for ($i = 1; $i <= $popular_pagination['total_pages']; $i++): ?>
                <a href="?popular_page=<?= $i ?>" class="<?= $i == $popular_pagination['page'] ? 'active' : '' ?>"><?= $i ?></a>
            <?php endfor; ?>
        </div>
    <?php endif; ?>
    </section>
    <!-- Layer 3: Random Public Groups (row 2, col 1 and 2) -->
    <section class="layer layer-3">
        <h2 style="font-size:1.2em;font-weight:700;color:#2575FC;margin-bottom:18px;">Random Public Groups</h2>
        <div class="group-grid">
            <?php foreach ($random_pagination['groups'] as $group): ?>
                <?php include 'group_card_template.php'; ?>
            <?php endforeach; ?>
            <?php if (empty($random_pagination['groups'])): ?>
                <div style="color:#aaa;font-size:1.1em;">No public groups found.</div>
            <?php endif; ?>
        </div>
        <?php if ($random_pagination['total_pages'] > 1): ?>
            <div class="pagination">
                <?php for ($i = 1; $i <= $random_pagination['total_pages']; $i++): ?>
                    <a href="?random_page=<?= $i ?>" class="<?= $i == $random_pagination['page'] ? 'active' : '' ?>"><?= $i ?></a>
                <?php endfor; ?>
            </div>
        <?php endif; ?>
    </section>
</div>



<script>
function confirmJoin(groupId) {
    if (confirm('Do you want to join this group?')) {
        window.location.href = 'index.php?join=' + groupId;
    }
}

</script>
</body>
</html>