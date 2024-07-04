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
        <h2 class="welcome-message">Hello, TEST123 (User)</h2>
        <form action="#" method="post" onsubmit="return validateForm()">
            <div class="form-group">
                <label for="student-id">Student ID</label>
                <input type="text" id="student-id" name="student-id" required>
            </div>
            <div class="form-group">
                <label for="password">Password</label>
                <input type="password" id="password" name="password" required>
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