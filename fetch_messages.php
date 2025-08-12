<?php
session_start();
include("connection.php");

$group_id = intval($_GET['group_id']);
$result = mysqli_query($con, "
    SELECT m.*, u.user_name, u.mbti 
    FROM messages m
    JOIN users u ON m.user_id = u.user_id
    WHERE m.group_id = '$group_id'
    ORDER BY m.created_at ASC
");

$messages = [];
while ($row = mysqli_fetch_assoc($result)) {
    $row['user_id'] = (int)$row['user_id'];
    $messages[] = $row;
}
header('Content-Type: application/json');
echo json_encode($messages);
?>