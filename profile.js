document.addEventListener('DOMContentLoaded', function() {
    var editBtn = document.getElementById('editBtn');
    var cancelBtn = document.getElementById('cancelBtn');
    var profileInfo = document.getElementById('profileInfo');
    var editProfileForm = document.getElementById('editProfileForm');
    var profileForm = document.getElementById('profileForm');
    var profilePicture = document.getElementById('profilePicture');
    var editProfilePicture = document.getElementById('editProfilePicture');
    var croppingTool = document.getElementById('croppingTool');
    var croppingImage = document.getElementById('croppingImage');
    var cropBtn = document.getElementById('cropBtn');

    // Edit Profile Button Click Event
    editBtn.addEventListener('click', function() {
        profileInfo.style.display = 'none';
        editProfileForm.style.display = 'block';

        // Populate edit form with current values
        document.getElementById('editUsername').value = document.getElementById('username').value;
        document.getElementById('editEmail').value = document.getElementById('email').value;
    });

    // Cancel Button Click Event
    cancelBtn.addEventListener('click', function() {
        profileInfo.style.display = 'block';
        editProfileForm.style.display = 'none';
    });

    // Profile Form Submit Event
    profileForm.addEventListener('submit', function(event) {
        event.preventDefault();

        var formData = new FormData(profileForm);
        
        fetch('update_profile.php', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert(data.message);
                // Update displayed info
                document.getElementById('username').value = data.username;
                document.getElementById('email').value = data.email;
                // Switch back to display view
                profileInfo.style.display = 'block';
                editProfileForm.style.display = 'none';
            } else {
                alert(data.message);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('An error occurred. Please try again.');
        });
    });

    // Edit Profile Picture Change Event
    editProfilePicture.addEventListener('change', function(event) {
        var file = event.target.files[0];
        if (file) {
            var reader = new FileReader();
            reader.onload = function(e) {
                croppingImage.src = e.target.result;
                croppingTool.style.display = 'block';
            }
            reader.readAsDataURL(file);
        }
    });

    // Crop Button Click Event
    cropBtn.addEventListener('click', function() {
        var croppedImageData = croppingImage.src; // This should be a global variable if needed elsewhere
        croppingTool.style.display = 'none';
        profilePicture.src = croppedImageData; // Update profile picture with cropped image

        // Send cropped image to server
        fetch('update_profile_picture.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({ image: croppedImageData }),
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert('Profile picture updated successfully');
            } else {
                alert('Failed to update profile picture');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('An error occurred while updating the profile picture');
        });
    });
});