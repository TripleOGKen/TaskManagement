<?php
// Enable error reporting for debugging
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once 'databaseTask.php';

$response = array('success' => false, 'message' => '');

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $conn = new mysqli(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);

    if ($conn->connect_error) {
        $response['message'] = "Connection failed: " . $conn->connect_error;
    } else {
        try {
            $student_id = $_POST['student_id'];
            $student_email = $_POST['student_email'];
            $student_name = $_POST['student_name'];
            $student_password = password_hash($_POST['student_password'], PASSWORD_DEFAULT);

            $stmt = $conn->prepare("INSERT INTO student (student_id, student_email, student_name, student_password) VALUES (?, ?, ?, ?)");
            $stmt->bind_param("ssss", $student_id, $student_email, $student_name, $student_password);

            if ($stmt->execute()) {
                $response['success'] = true;
                $response['message'] = 'Registration successful!';
                $response['redirect'] = 'login.php';
            } else {
                $response['message'] = 'Registration failed. Please try again. Error: ' . $stmt->error;
            }

            $stmt->close();
            $conn->close();
        } catch (Exception $e) {
            $response['message'] = 'An error occurred: ' . $e->getMessage();
        }
    }

    // Send JSON response for AJAX requests
    header('Content-Type: application/json');
    echo json_encode($response);
    exit;
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register Page</title>
    <link rel="stylesheet" href="register2.css">
</head>
<body>
    <div class="container">
        <h2>Register</h2>
        <form id="register-form" action="#" method="post">
            <div class="form-group">
                <label for="student_id">Student ID</label>
                <input type="text" id="student_id" name="student_id" required>
            </div>
            <div class="form-group">
                <label for="student_email">Email</label>
                <input type="email" id="student_email" name="student_email" required>
            </div>
            <div class="form-group">
                <label for="student_name">Name</label>
                <input type="text" id="student_name" name="student_name" required>
            </div>
            <div class="form-group">
                <label for="student_password">Password</label>
                <input type="password" id="student_password" name="student_password" required>
            </div>
            <div class="form-group">
                <input type="submit" value="Register" class="register-button">
            </div>
        </form>
    </div>
    <div class="register-link">
        <p>Registered? <a href="login.php">Login here</a></p>
    </div>
    <script src="register2.js"></script>
</body>
</html>