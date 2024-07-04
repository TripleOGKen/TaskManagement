document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('register-form');
    
    form.addEventListener('submit', function(event) {
        event.preventDefault();
        
        const student_id = document.getElementById('student_id').value;
        const student_email = document.getElementById('student_email').value;
        const student_name = document.getElementById('student_name').value;
        const student_password = document.getElementById('student_password').value;
        

        const formData = new FormData(form);
        
        fetch('register2.php', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert(data.message);
                window.location.href = data.redirect;
            } else {
                alert(data.message);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('An error occurred. Please try again.');
        });
    });
});