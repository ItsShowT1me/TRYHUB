<?php
<?php
session_start();
include 'connection.php';
$user_id = intval($_POST['user_id'] ?? 0);
$group_id = intval($_POST['group_id'] ?? 0);
$current_user = $_SESSION['user_id'] ?? 0;

// Check owner
$owner_row = mysqli_fetch_assoc(mysqli_query($con, "SELECT user_id FROM user_groups WHERE group_id='$group_id' ORDER BY id ASC LIMIT 1"));
if ($owner_row && $current_user == $owner_row['user_id'] && $user_id != $owner_row['user_id']) {
    mysqli_query($con, "DELETE FROM user_groups WHERE user_id='$user_id' AND group_id='$group_id'");
    echo 'success';
} else {
    echo 'fail';
}
?>