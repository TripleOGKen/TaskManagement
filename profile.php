<?php
require_once 'databaseTask.php';

session_start();
if (!isset($_SESSION['student_id'])) {
    header("Location: login.php");
    exit();
}

$conn = new mysqli(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$update_message = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST['editUsername'];
    $email = $_POST['editEmail'];
    $password = $_POST['editPassword'];
    
    //Check for profile picture upload
    $profile_picture = null;
    if (isset($_FILES["editProfilePicture"]) && $_FILES["editProfilePicture"]["error"] == 0) {
        $profile_picture = file_get_contents($_FILES["editProfilePicture"]["tmp_name"]);
    }

    //Update profile information
    if ($profile_picture) {
        //For debugging purposes
        $file_size = strlen($profile_picture);
        error_log("Profile picture size: $file_size bytes");

        $stmt = $conn->prepare("UPDATE student SET student_name = ?, student_email = ?, profile_picture = ? WHERE student_id = ?");
        $stmt->bind_param("ssbs", $username, $email, $null, $_SESSION['student_id']);
        $stmt->send_long_data(2, $profile_picture);
    } else {
        $stmt = $conn->prepare("UPDATE student SET student_name = ?, student_email = ? WHERE student_id = ?");
        $stmt->bind_param("sss", $username, $email, $_SESSION['student_id']);
    }

    if ($stmt->execute()) {
        if (!empty($password)) {
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);
            $pwd_stmt = $conn->prepare("UPDATE student SET student_password = ? WHERE student_id = ?");
            $pwd_stmt->bind_param("ss", $hashed_password, $_SESSION['student_id']);
            $pwd_stmt->execute();
            $pwd_stmt->close();
        }
        $update_message = "Profile updated successfully";
    } else {
        $update_message = "Failed to update profile: " . $stmt->error;
    }
    $stmt->close();
}

//Fetch user details
$stmt = $conn->prepare("SELECT student_id, student_email, student_name, profile_picture FROM student WHERE student_id = ?");
$stmt->bind_param("s", $_SESSION['student_id']);
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
            <a href="index.html" class="sidebar-item">Logout</a>
        </div>
    </div>
    <div class="main-content">
        <div class="container">
            <h2>User Profile</h2>
            <?php if (!empty($update_message)): ?>
                <p><?php echo $update_message; ?></p>
            <?php endif; ?>
            <div id="profilePictureContainer">
                <?php if (!empty($user['profile_picture'])): ?>
                    <img id="profilePicture" src="data:image/jpeg;base64,<?php echo base64_encode($user['profile_picture']); ?>" alt="Profile Picture" class="profile-pic">
                <?php else: ?>
                    <img id="profilePicture" src="default-profile.png" alt="Profile Picture" class="profile-pic">
                <?php endif; ?>
            </div>
            <div id="profileInfo">
                <div>
                    <label for="student_id">Student ID:</label>
                    <input type="text" id="student_id" name="student_id" value="<?php echo htmlspecialchars($user['student_id']); ?>" readonly class="greyed-out">
                </div>
                <div>
                    <label for="student_name">Username:</label>
                    <input type="text" id="student_name" name="student_name" value="<?php echo htmlspecialchars($user['student_name']); ?>" readonly>
                </div>
                <div>
                    <label for="student_email">Email:</label>
                    <input type="email" id="student_email" name="student_email" value="<?php echo htmlspecialchars($user['student_email']); ?>" readonly>
                </div>
                <div>
                    <label for="student_password">Password:</label>
                    <input type="password" id="student_password" name="student_password" value="********" readonly>
                </div>
                <button id="editBtn">Edit Profile</button>
            </div>
            <div id="editProfileForm" style="display: none;">
            <form id="profileForm" method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" enctype="multipart/form-data">
                    <div>
                        <label for="editUsername">Username:</label>
                        <input type="text" id="editUsername" name="editUsername" value="<?php echo htmlspecialchars($user['student_name']); ?>">
                    </div>
                    <div>
                        <label for="editEmail">Email:</label>
                        <input type="email" id="editEmail" name="editEmail" value="<?php echo htmlspecialchars($user['student_email']); ?>">
                    </div>
                    <div>
                        <label for="editPassword">New Password (leave blank to keep current):</label>
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