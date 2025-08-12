<?php
session_start();
include("connection.php");

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $group_id = intval($_POST['group_id']);
    $user_id = $_SESSION['user_id'];
    $message = trim($_POST['message']);
    $file_path = null;

    if (isset($_FILES['file']) && $_FILES['file']['error'] === UPLOAD_ERR_OK) {
        $upload_dir = 'uploads/';
        if (!is_dir($upload_dir)) mkdir($upload_dir, 0777, true);
        $filename = time() . '_' . basename($_FILES['file']['name']);
        $target = $upload_dir . $filename;
        if (move_uploaded_file($_FILES['file']['tmp_name'], $target)) {
            $file_path = $target;
        }
    }

    $stmt = $con->prepare("INSERT INTO messages (group_id, user_id, message, file_path) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("iiss", $group_id, $user_id, $message, $file_path);
    $stmt->execute();
    $stmt->close();
}
?>