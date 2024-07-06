document.addEventListener('DOMContentLoaded', function() {
    var editBtn = document.getElementById('editBtn');
    var cancelBtn = document.getElementById('cancelBtn');
    var profileInfo = document.getElementById('profileInfo');
    var editProfileForm = document.getElementById('editProfileForm');
    var profilePicture = document.getElementById('profilePicture');
    var editProfilePicture = document.getElementById('editProfilePicture');
    var croppingTool = document.getElementById('croppingTool');
    var croppingImage = document.getElementById('croppingImage');
    var cropBtn = document.getElementById('cropBtn');

    //Edit Profile Button Click Event
    editBtn.addEventListener('click', function() {
        profileInfo.style.display = 'none';
        editProfileForm.style.display = 'block';

        //Populate edit form with current values
        document.getElementById('editUsername').value = document.getElementById('student_name').value;
        document.getElementById('editEmail').value = document.getElementById('student_email').value;
    });

    //Cancel Button Click Event
    cancelBtn.addEventListener('click', function() {
        profileInfo.style.display = 'block';
        editProfileForm.style.display = 'none';
    });

    //Edit Profile Picture Change Event
    editProfilePicture.addEventListener('change', function(event) {
        var file = event.target.files[0];
        if (file) {
            var reader = new FileReader();
            reader.onload = function(e) {
                croppingImage.src = e.target.result;
                croppingTool.style.display = 'block';
            };
            reader.readAsDataURL(file);
        }
    });

    //Crop Button Click Event
    cropBtn.addEventListener('click', function() {
        var croppedImageData = croppingImage.src;
        croppingTool.style.display = 'none';
        profilePicture.src = croppedImageData;

        
    });
});   