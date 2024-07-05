<?php
session_start();
if (!isset($_SESSION['student_id'])) {
    exit(json_encode(['error' => 'Not logged in']));
}

$student_id = $_SESSION['student_id'];
$data = json_decode(file_get_contents('php://input'), true);

$db = new mysqli('localhost', 'root', '', 'taskmanagement');

if ($db->connect_error) {
    exit(json_encode(['error' => 'Database connection failed']));
}

$stmt = $db->prepare("UPDATE tasks SET task_name = ?, task_description = ?, task_date = ?, task_priority = ? WHERE task_id = ? AND student_id = ?");
$stmt->bind_param("ssssii", $data['task_name'], $data['task_description'], $data['task_date'], $data['task_priority'], $data['task_id'], $student_id);

if ($stmt->execute()) {
    echo json_encode($data);
} else {
    echo json_encode(['error' => 'Failed to update task']);
}

$stmt->close();
$db->close();