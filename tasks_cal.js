document.addEventListener('DOMContentLoaded', function() {
    const calendarEl = document.getElementById('calendar');
    const taskForm = document.getElementById('task-form');
    const taskInput = document.getElementById('task-input');
    const taskDate = document.getElementById('task-date');
    const taskTime = document.getElementById('task-time');
    const taskDescription = document.getElementById('task-description');
    const prioritySelect = document.getElementById('priority-select');
    const taskList = document.getElementById('task-list');
    const pdfUpload = document.getElementById('pdf-upload');
    const pdfViewer = document.getElementById('pdf-viewer');
    const autoPriorityToggle = document.getElementById('auto-priority-toggle');
    
    // Sidebar button event listeners
    document.getElementById('profile-btn').addEventListener('click', function() {
        window.location.href = 'profile.php';
    });

    document.getElementById('logout-btn').addEventListener('click', function() {
        window.location.href = 'index.html';
    });
    
    let tasks = [];
    let currentRotation = 0;
    let isAutoPriority = false;

    // Initialize Calendar
    const calendar = new FullCalendar.Calendar(calendarEl, {
        initialView: 'dayGridMonth',
        height: '100%',
        selectable: true,
        editable: true,
        events: tasks,
        eventClick: function(info) {
            showTaskDetails(info.event);
        }
    });

    calendar.render();

    // Auto Priority Toggle
    autoPriorityToggle.addEventListener('change', function() {
        isAutoPriority = this.checked;
        if (isAutoPriority) {
            prioritySelect.disabled = true;
        } else {
            prioritySelect.disabled = false;
        }
    });

    // Task Form Submit
    taskForm.addEventListener('submit', function(event) {
        event.preventDefault();
        const taskText = taskInput.value.trim();
        const taskDateValue = taskDate.value;
        const taskTimeValue = taskTime.value;
        const taskDescriptionValue = taskDescription.value.trim();
        let taskPriority = prioritySelect.value;

        if (taskText && taskDateValue && taskTimeValue) {
            const taskDateTime = new Date(`${taskDateValue}T${taskTimeValue}`);
            const now = new Date();

            if (taskDateTime < now) {
                alert("Cannot add a task with a date and time older than the present.");
                return;
            }

            if (isAutoPriority) {
                taskPriority = calculatePriority(taskDateTime);
            }

            const task = { 
                id: Date.now().toString(),
                title: taskText, 
                start: `${taskDateValue}T${taskTimeValue}`,
                description: taskDescriptionValue,
                priority: taskPriority,
                color: getColorForPriority(taskPriority)
            };
            tasks.push(task);
            addTaskToList(task);
            calendar.addEvent(task);
            taskForm.reset();
        }
    });

    function calculatePriority(taskDateTime) {
        const now = new Date();
        const diffDays = Math.ceil((taskDateTime - now) / (1000 * 60 * 60 * 24));

        if (diffDays <= 3) return 'high';
        if (diffDays <= 9) return 'medium';
        return 'low';
    }

    function getColorForPriority(priority) {
        switch (priority) {
            case 'high': return 'red';
            case 'medium': return 'orange';
            case 'low': return 'green';
            default: return 'blue';
        }
    }

    function showTaskDetails(event) {
        const details = `
            Title: ${event.title}
            Date: ${new Date(event.start).toLocaleString()}
            Priority: ${event.extendedProps.priority}
            Description: ${event.extendedProps.description || 'No description provided'}
        `;
        alert(details);
    }

    // Add Task to List
    function addTaskToList(task) {
        const li = document.createElement('li');
        li.className = 'task-item';
        li.dataset.id = task.id;
        li.innerHTML = `
            <span class="task-text">${task.title}</span>
            <span class="task-date">${new Date(task.start).toLocaleString()}</span>
            <span class="task-priority ${task.priority}">${task.priority}</span>
            <div class="task-actions">
                <button class="edit-task">Edit</button>
                <button class="delete-task">Delete</button>
            </div>
        `;
        taskList.appendChild(li);

        li.querySelector('.delete-task').addEventListener('click', function() {
            removeTaskFromList(task.id);
            calendar.getEventById(task.id).remove();
        });

        li.querySelector('.edit-task').addEventListener('click', function() {
            editTask(task);
        });
    }

    function editTask(task) {
        const newTitle = prompt('Edit Task Title:', task.title);
        const newDescription = prompt('Edit Task Description:', task.description);
        if (newTitle !== null && newDescription !== null) {
            updateTaskInList(task.id, newTitle, newDescription);
            const event = calendar.getEventById(task.id);
            event.setProp('title', newTitle);
            event.setExtendedProp('description', newDescription);
        }
    }

    function updateTaskInList(id, newTitle, newDescription) {
        const taskItem = taskList.querySelector(`li[data-id="${id}"]`);
        if (taskItem) {
            taskItem.querySelector('.task-text').textContent = newTitle;
            const taskIndex = tasks.findIndex(t => t.id === id);
            if (taskIndex !== -1) {
                tasks[taskIndex].title = newTitle;
                tasks[taskIndex].description = newDescription;
            }
        }
    }

    function removeTaskFromList(id) {
        const taskItem = taskList.querySelector(`li[data-id="${id}"]`);
        if (taskItem) {
            taskList.removeChild(taskItem);
            tasks = tasks.filter(t => t.id !== id);
        }
    }

    // PDF Viewer
    pdfUpload.addEventListener('change', function(event) {
    const file = event.target.files[0];
    if (file.type !== 'application/pdf') {
        console.error('File is not a PDF');
        return;
    }

    const fileURL = URL.createObjectURL(file);
    pdfViewer.src = fileURL;
    currentRotation = 0;
    pdfViewer.style.transform = `rotate(${currentRotation}deg)`;

    // Upload PDF to server
    const formData = new FormData();
    formData.append('pdf_file', file);

    fetch('upload_pdf.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.text())
    .then(result => {
        console.log(result);
    })
    .catch(error => {
        console.error('Error:', error);
    });
});

});