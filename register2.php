<?php

require_once 'databaseTask.php'; // Include the database configuration file

// Create a new connection to the MySQL database
$conn = new mysqli(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);

// Check if the connection was successful
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Initialize response array
$response = array('success' => false, 'message' => '');

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST['email'];
    $password = $_POST['password'];

    // Prepare a statement to select the hashed password, user ID, and username for the provided email
    $stmt = $conn->prepare("SELECT student_id, student_email, student_name, student_password FROM student WHERE student_email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $stmt->store_result();

    // Check if a user with the provided email exists
    if ($stmt->num_rows > 0) {
        $stmt->bind_result($user_id, $hashed_password, $name);
        $stmt->fetch();

        // Verify the provided password against the hashed password
        if (password_verify($password, $hashed_password)) {
            // Start session
            session_start();
            session_regenerate_id(true); // Regenerate session ID for security

            // Store user information in session
            $_SESSION['User_id'] = $user_id;   // Store user's ID
            $_SESSION['User_email'] = $email; // Store user's email
            $_SESSION['User_name'] = $name;   // Store user's name
            

            // Set success response
            $response['success'] = true;
            $response['message'] = 'Login successful!';
            $response['redirect'] = 'index2.php'; // URL to redirect after successful login
        } else {
            $response['message'] = 'Invalid email or password.';
        }
    } else {
        $response['message'] = 'Invalid email or password.';
    }

    // Close the statement
    $stmt->close();
}

// Close the database connection
$conn->close();

// Send JSON response
header('Content-Type: application/json');
echo json_encode($response);
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
                <label for="student-id">Student ID</label>
                <input type="text" id="student-id" name="student-id" required>
            </div>
            <div class="form-group">
                <label for="email">Email</label>
                <input type="email" id="email" name="email" required>
            </div>
            <div class="form-group">
                <label for="password">Password</label>
                <input type="password" id="password" name="password" required>
            </div>
            <div class="form-group">
                <label for="confirm-password">Re-Type Password</label>
                <input type="password" id="confirm-password" name="confirm-password" required>
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
