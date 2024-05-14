function validateForm() {
    var studentId = document.getElementById('student-id').value;
    var password = document.getElementById('password').value;

    if (studentId === "" || password === "") {
        document.getElementById('error-message').style.display = "block";
        return false;
    } else {
        document.getElementById('error-message').style.display = "none";
        return true;
    }
}
