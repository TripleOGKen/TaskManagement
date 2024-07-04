<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
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
            <iframe id="pdf-viewer"></iframe>
        </div>
        <div class="container">
            <div id="calendar"></div>
            <div class="task-section">
                <h2>Tasks</h2>
                <form id="task-form">
                    <input type="text" id="task-input" placeholder="New task" required>
                    <input type="date" id="task-date" required>
                    <input type="time" id="task-time" required>
                    <textarea id="task-description" placeholder="Task description"></textarea>
                    <select id="priority-select">
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
</body>
</html>