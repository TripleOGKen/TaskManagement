<?php
// Enable error reporting for debugging
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once 'databaseTask.php';

$response = array('success' => false, 'message' => '');

session_start(); // Start or resume session

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $student_id = $_POST['student_id'];
    $student_password = $_POST['student_password'];

    $conn = new mysqli(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);

    if ($conn->connect_error) {
        $response['message'] = "Connection failed: " . $conn->connect_error;
    } else {
        $stmt = $conn->prepare("SELECT student_id, student_email, student_password FROM student WHERE student_id = ?");
        $stmt->bind_param("s", $student_id);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows === 1) {
            $user = $result->fetch_assoc();
            if (password_verify($student_password, $user['student_password'])) {
                $_SESSION['student_id'] = $user['student_id'];
                $_SESSION['student_email'] = $user['student_email'];
                $response['success'] = true;
                $response['message'] = 'Login successful';
                $response['redirect'] = 'tasks_cal.php';
            } else {
                $response['message'] = "Invalid student ID or password.";
            }
        } else {
            $response['message'] = "Invalid student ID or password.";
        }

        $stmt->close();
        $conn->close();
    }

    // Send JSON response for AJAX requests
    if (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {
        header('Content-Type: application/json');
        echo json_encode($response);
        exit;
    } else {
        // Redirect if not an AJAX request and login was successful
        if ($response['success']) {
            header('Location: tasks_cal.php');
            exit;
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Page</title>
    <link rel="stylesheet" href="login.css">
</head>
<body>
    <div class="container">
        <h2 class="welcome-message">Hello, Welcome (User)</h2>
        <form id="login-form" action="login.php" method="post">
            <div class="form-group">
                <label for="student_id">Student ID</label>
                <input type="text" id="student_id" name="student_id" required>
            </div>
            <div class="form-group">
                <label for="student_password">Password</label>
                <input type="password" id="student_password" name="student_password" required>
            </div>
            <div class="form-group">
                <input type="submit" value="Login">
                <div id="error-message" class="error-message" style="display: none;">Error: Invalid Input</div>
            </div>
        </form>
    </div>
    <div class="register-link">
        <p>Not registered? Register <a href="register2.php">here</a>.</p>
    </div>
    <script src="js/login.js"></script>
</body>
</html>
