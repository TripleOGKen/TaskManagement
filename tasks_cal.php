<?php
session_start();
if (!isset($_SESSION['student_id'])) {
    header("Location: login.php");
    exit();
}

$student_id = $_SESSION['student_id'];

$db_host = 'localhost';
$db_user = 'root';
$db_password = '';
$db_name = 'taskmanagement';

$db = new mysqli($db_host, $db_user, $db_password, $db_name);

if ($db->connect_error) {
    die("Database connection failed: " . $db->connect_error);
    
}

function getTasks($db, $student_id) {
    $stmt = $db->prepare("SELECT * FROM tasks WHERE student_id = ? ORDER BY task_date ASC");
    $stmt->bind_param("i", $student_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $tasks = [];
    while ($row = $result->fetch_assoc()) {
        $tasks[] = $row;
    }
    $stmt->close();
    return $tasks;
}

// Fetch PDF if exists
$stmt = $db->prepare("SELECT pdf_file FROM student WHERE student_id = ?");
$stmt->bind_param("i", $student_id);
$stmt->execute();
$result = $stmt->get_result();
$row = $result->fetch_assoc();
$pdf_content_base64 = $row['pdf_file'] ?? null;
$stmt->close();

$has_pdf = !empty($pdf_content_base64);
$tasks = getTasks($db, $student_id);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <title>Interactive Calendar with PDF Viewer</title>
    <link rel="stylesheet" href="tasks_cal.css">
    <link href="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.14/main.min.css" rel="stylesheet">
</head>
<body>
    <div class="sidebar">
        <button id="profile-btn">Profile</button>
        <button id="logout-btn">Logout</button>
    </div>
    <div class="main-container">
        <div class="header">
            <h1>INTI Task Management System</h1>
            <div class="auto-priority-toggle">
                <label class="switch">
                    <input type="checkbox" id="auto-priority-toggle">
                    <span class="slider round"></span>
                </label>
                <span>Auto Priority</span>
            </div>
        </div>
        <div class="pdf-viewer-container">
            <input type="file" id="pdf-upload" accept="application/pdf">
            <div class="pdf-controls">
            </div>
            <?php if ($has_pdf): ?>
                <iframe id="pdf-viewer" src="data:application/pdf;base64,<?php echo $pdf_content_base64; ?>"></iframe>
            <?php else: ?>
                <iframe id="pdf-viewer"></iframe>
            <?php endif; ?>
        </div>
        <div class="container">
            <div id="calendar"></div>
            <div class="task-section">
                <h2>Tasks</h2>
                <form id="task-form">
                    <input type="text" id="task_name" placeholder="New task" required>
                    <input type="date" id="task_date" required>
                    <input type="time" id="task_time" required>
                    <textarea id="task_description" placeholder="Task description"></textarea>
                    <select id="task_priority">
                        <option value="low">Low</option>
                        <option value="medium">Medium</option>
                        <option value="high">High</option>
                    </select>
                    <button type="submit">Add Task</button>
                </form>
                <ul id="task-list"></ul>
            </div>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.14/index.global.min.js"></script>
    <script src="tasks_cal.js"></script>
    <script> var initialTasks = <?php echo json_encode($tasks); ?>; </script>
</body>
</html>