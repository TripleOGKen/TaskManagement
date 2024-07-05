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

$stmt = $db->prepare("INSERT INTO tasks (student_id, task_name, task_description, task_date, task_priority) VALUES (?, ?, ?, ?, ?)");
$stmt->bind_param("issss", $student_id, $data['task_name'], $data['task_description'], $data['task_date'], $data['task_priority']);

if ($stmt->execute()) {
    $data['task_id'] = $stmt->insert_id;
    echo json_encode($data);
} else {
    echo json_encode(['error' => 'Failed to add task']);
}

$stmt->close();
$db->close();