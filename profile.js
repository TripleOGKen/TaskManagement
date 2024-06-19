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
        // Hide profile info, show edit form
        profileInfo.style.display = 'none';
        editProfileForm.style.display = 'block';

        // Populate edit form with current values
        var username = document.getElementById('username').value;
        var email = document.getElementById('email').value;
        document.getElementById('editUsername').value = username;
        document.getElementById('editEmail').value = email;
    });

    // Cancel Button Click Event
    cancelBtn.addEventListener('click', function() {
        // Show profile info, hide edit form
        profileInfo.style.display = 'block';
        editProfileForm.style.display = 'none';
    });

    // Profile Form Submit Event
    profileForm.addEventListener('submit', function(event) {
        event.preventDefault();

        // Retrieve edited values from form
        var newUsername = document.getElementById('editUsername').value;
        var newEmail = document.getElementById('editEmail').value;
        var newPassword = document.getElementById('editPassword').value;

        // Update profile info display with new values
        document.getElementById('username').value = newUsername;
        document.getElementById('email').value = newEmail;
        document.getElementById('password').value = newPassword;

        // Reset form display and hide edit form
        profileInfo.style.display = 'block';
        editProfileForm.style.display = 'none';
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
        croppedImageData = croppingImage.src; // This should be a global variable if needed elsewhere
        croppingTool.style.display = 'none';
        profilePicture.src = croppedImageData; // Update profile picture with cropped image
    });
});
