<?php
require_once 'databaseTask.php';

session_start();
if (!isset($_SESSION['student_id'])) {
    header("HTTP/1.0 403 Forbidden");
    exit;
}

$conn = new mysqli(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$stmt = $conn->prepare("SELECT profile_picture FROM student WHERE student_id = ?");
$stmt->bind_param("s", $_SESSION['student_id']);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();

if ($user && $user['profile_picture']) {
    header("Content-Type: image/jpeg");
    echo $user['profile_picture'];
} else {
    // Serve a default image
    header("Content-Type: image/png");
    readfile("default-profile.png");
}

$stmt->close();
$conn->close();