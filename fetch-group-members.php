<?php
include 'connection.php';
$group_id = intval($_GET['group_id']);
$page = isset($_GET['page']) ? max(1, intval($_GET['page'])) : 1;
$limit = 10;
$offset = ($page - 1) * $limit;

// Get total member count
$total_res = mysqli_query($con, "SELECT COUNT(*) as total FROM user_groups WHERE group_id = '$group_id'");
$total_row = mysqli_fetch_assoc($total_res);
$total_members = $total_row ? intval($total_row['total']) : 0;
$total_pages = ceil($total_members / $limit);

// Get paginated members
$members = [];
$res = mysqli_query($con, "
    SELECT u.user_id, u.user_name, u.mbti, u.image
    FROM user_groups ug
    JOIN users u ON ug.user_id = u.user_id
    WHERE ug.group_id = '$group_id'
    LIMIT $limit OFFSET $offset
");
while ($row = mysqli_fetch_assoc($res)) {
    $members[] = $row;
}

// Output JSON with pagination info
header('Content-Type: application/json');
echo json_encode([
    'members' => $members,
    'total_pages' => $total_pages,
    'page' => $page
]);
?>