<?php
session_start();
if (!isset($_SESSION['student_id'])) {
    exit(json_encode(['error' => 'Not logged in']));
}

$student_id = $_SESSION['student_id'];
$data = json_decode(file_get_contents('php://input'), true);

if (!isset($data['task_id'])) {
    exit(json_encode(['error' => 'No task ID provided']));
}

$db = new mysqli('localhost', 'root', '', 'taskmanagement');

if ($db->connect_error) {
    exit(json_encode(['error' => 'Database connection failed']));
}

$stmt = $db->prepare("DELETE FROM tasks WHERE task_id = ? AND student_id = ?");
$stmt->bind_param("ii", $data['task_id'], $student_id);

if ($stmt->execute()) {
    if ($stmt->affected_rows > 0) {
        echo json_encode(['success' => true, 'message' => 'Task deleted successfully']);
    } else {
        echo json_encode(['success' => false, 'message' => 'No task found with the given ID']);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Failed to delete task']);
}

$stmt->close();
$db->close();