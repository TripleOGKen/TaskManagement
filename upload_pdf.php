<?php
session_start();
if (!isset($_SESSION['student_id'])) {
    exit('Not logged in');
}

$student_id = $_SESSION['student_id'];

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['pdf_file'])) {
    $file = $_FILES['pdf_file'];
    
    if ($file['type'] !== 'application/pdf') {
        exit('File is not a PDF');
    }

    $pdf_content = file_get_contents($file['tmp_name']);
    $pdf_content_base64 = base64_encode($pdf_content);

    // Connect to your database (replace with your actual database credentials)
    $db_host = 'localhost';
    $db_user = 'root';
    $db_password = '';
    $db_name = 'taskmanagement';

    $db = new mysqli($db_host, $db_user, $db_password, $db_name);
    
    if ($db->connect_error) {
        exit('Database connection failed: ' . $db->connect_error);
    }

    $stmt = $db->prepare("UPDATE student SET pdf_file = ? WHERE student_id = ?");
    $stmt->bind_param("si", $pdf_content_base64, $student_id);

    if ($stmt->execute()) {
        echo 'PDF uploaded successfully';
    } else {
        echo 'Error uploading PDF: ' . $stmt->error;
    }

    $stmt->close();
    $db->close();
} else {
    echo 'No file uploaded';
}