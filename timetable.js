document.getElementById('fileInput').addEventListener('change', function(event) {
    const file = event.target.files[0];
    const reader = new FileReader();

    reader.onload = function(event) {
        const img = document.getElementById('uploadedImage');
        img.src = event.target.result;
    }

    reader.readAsDataURL(file);
});

document.getElementById('zoomInBtn').addEventListener('click', function() {
    const img = document.getElementById('uploadedImage');
    const currentWidth = img.clientWidth;
    const currentHeight = img.clientHeight;
    const newWidth = currentWidth * 1.1;
    const newHeight = currentHeight * 1.1;
    img.style.width = newWidth + 'px';
    img.style.height = newHeight + 'px';
});

document.getElementById('zoomOutBtn').addEventListener('click', function() {
    const img = document.getElementById('uploadedImage');
    const currentWidth = img.clientWidth;
    const currentHeight = img.clientHeight;
    const newWidth = currentWidth / 1.1;
    const newHeight = currentHeight / 1.1;
    img.style.width = newWidth + 'px';
    img.style.height = newHeight + 'px';
});
