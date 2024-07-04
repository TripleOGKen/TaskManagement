<?php
require_once 'databaseTask.php';

session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$conn = new mysqli(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$stmt = $conn->prepare("SELECT student_id, student_email FROM student WHERE student_id = ?");
$stmt->bind_param("s", $_SESSION['user_id']);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();

$stmt->close();
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Profile - INTI Task Management System</title>
    <link rel="stylesheet" href="profile.css">
</head>
<body>
    <div class="sidebar">
        <div class="sidebar-content">
            <a href="tasks_cal.php" class="sidebar-item">Tasks</a>
            <a href="index.php" class="sidebar-item">Logout</a>
        </div>
    </div>
    <div class="main-content">
        <div class="container">
            <h2>User Profile</h2>
            <div id="profilePictureContainer">
                <img id="profilePicture" src="default-profile.png" alt="Profile Picture" class="profile-pic">
            </div>
            <div id="profileInfo">
                <div>
                    <label for="studentId">Student ID:</label>
                    <input type="text" id="studentId" name="studentId" value="<?php echo htmlspecialchars($user['student_id']); ?>" readonly class="greyed-out">
                </div>
                <div>
                    <label for="username">Username:</label>
                    <input type="text" id="username" name="username" readonly>
                </div>
                <div>
                    <label for="email">Email:</label>
                    <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($user['student_email']); ?>" readonly>
                </div>
                <div>
                    <label for="password">Password:</label>
                    <input type="password" id="password" name="password" value="********" readonly>
                </div>
                <button id="editBtn">Edit Profile</button>
            </div>
            <div id="editProfileForm" style="display: none;">
                <form id="profileForm">
                    <div>
                        <label for="editUsername">Username:</label>
                        <input type="text" id="editUsername" name="editUsername">
                    </div>
                    <div>
                        <label for="editEmail">Email:</label>
                        <input type="email" id="editEmail" name="editEmail">
                    </div>
                    <div>
                        <label for="editPassword">Password:</label>
                        <input type="password" id="editPassword" name="editPassword">
                    </div>
                    <div>
                        <label for="editProfilePicture">Profile Picture:</label>
                        <input type="file" id="editProfilePicture" name="editProfilePicture" accept="image/*">
                        <div id="croppingTool" style="display: none;">
                            <img id="croppingImage" src="">
                            <button type="button" id="cropBtn">Crop</button>
                        </div>
                    </div>
                    <button type="submit">Save</button>
                    <button type="button" id="cancelBtn">Cancel</button>
                </form>
            </div>
        </div>
    </div>
    <script src="profile.js"></script>
</body>
</html>