document.addEventListener('DOMContentLoaded', function() {
    const calendarEl = document.getElementById('calendar');
    const taskForm = document.getElementById('task-form');
    const taskNameInput = document.getElementById('task_name');
    const taskDate = document.getElementById('task_date');
    const taskTime = document.getElementById('task_time');
    const taskDescription = document.getElementById('task_description');
    const taskPriority = document.getElementById('task_priority');
    const taskList = document.getElementById('task-list');
    const pdfUpload = document.getElementById('pdf-upload');
    const pdfViewer = document.getElementById('pdf-viewer');
    const autoPriorityToggle = document.getElementById('auto-priority-toggle');
    
    //Sidebar button event listeners
    document.getElementById('profile-btn').addEventListener('click', function() {
        window.location.href = 'profile.php';
    });

    document.getElementById('logout-btn').addEventListener('click', function() {
        window.location.href = 'index.html';
    });
    
    let tasks = initialTasks || [];
    let currentRotation = 0;
    let isAutoPriority = false;

    //Initialize Calendar
    const calendar = new FullCalendar.Calendar(calendarEl, {
        initialView: 'dayGridMonth',
        height: '100%',
        selectable: true,
        editable: true,
        events: tasks.map(task => ({
            id: task.task_id,
            title: task.task_name,
            start: task.task_date,
            description: task.task_description,
            priority: task.task_priority,
            color: getColorForPriority(task.task_priority)
        })),
        eventClick: function(info) {
            showTaskDetails(info.event);
        }
    });

    calendar.render();

    //Auto Priority Toggle
    autoPriorityToggle.addEventListener('change', function() {
        isAutoPriority = this.checked;
        taskPriority.disabled = isAutoPriority;
    });

    //Task Form Submit
    taskForm.addEventListener('submit', function(event) {
        event.preventDefault();
        const taskName = taskNameInput.value.trim();
        const taskDateValue = taskDate.value;
        const taskTimeValue = taskTime.value;
        const taskDescriptionValue = taskDescription.value.trim();
        let taskPriorityValue = taskPriority.value;

        if (taskName && taskDateValue && taskTimeValue) {
            const taskDateTime = new Date(`${taskDateValue}T${taskTimeValue}`);
            const now = new Date();

            if (taskDateTime < now) {
                alert("Cannot add a task with a date and time older than the present.");
                return;
            }

            if (isAutoPriority) {
                taskPriorityValue = calculatePriority(taskDateTime);
            }

            const task = { 
                task_name: taskName,
                task_date: `${taskDateValue}T${taskTimeValue}`,
                task_description: taskDescriptionValue,
                task_priority: taskPriorityValue
            };

            //Send task to server
            fetch('add_task.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify(task),
            })
            .then(response => response.json())
            .then(newTask => {
                tasks.push(newTask);
                addTaskToList(newTask);
                calendar.addEvent({
                    id: newTask.task_id,
                    title: newTask.task_name,
                    start: newTask.task_date,
                    description: newTask.task_description,
                    priority: newTask.task_priority,
                    color: getColorForPriority(newTask.task_priority)
                });
                taskForm.reset();
            })
            .catch(error => console.error('Error:', error));
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

    //Add Task to List
    function addTaskToList(task) {
        const li = document.createElement('li');
        li.className = 'task-item';
        li.dataset.id = task.task_id;
        li.innerHTML = `
            <span class="task-text">${task.task_name}</span>
            <span class="task-date">${new Date(task.task_date).toLocaleString()}</span>
            <span class="task-priority ${task.task_priority}">${task.task_priority}</span>
            <div class="task-actions">
                <button class="edit-task">Edit</button>
                <button class="delete-task">Delete</button>
            </div>
        `;
        taskList.appendChild(li);

        li.querySelector('.delete-task').addEventListener('click', function() {
            removeTaskFromList(task.task_id);
        });

        li.querySelector('.edit-task').addEventListener('click', function() {
            editTask(task);
        });
    }

    function editTask(task) {
        const newTitle = prompt('Edit Task Title:', task.task_name);
        const newDescription = prompt('Edit Task Description:', task.task_description);
        const newDate = prompt('Edit Task Date (YYYY-MM-DD HH:MM):', task.task_date);
        const newPriority = prompt('Edit Task Priority (low/medium/high):', task.task_priority);

        if (newTitle !== null && newDescription !== null && newDate !== null && newPriority !== null) {
            const updatedTask = {
                task_id: task.task_id,
                task_name: newTitle,
                task_description: newDescription,
                task_date: newDate,
                task_priority: newPriority
            };

            fetch('update_task.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify(updatedTask),
            })
            .then(response => response.json())
            .then(updatedTask => {
                updateTaskInList(updatedTask);
                const event = calendar.getEventById(updatedTask.task_id);
                if (event) {
                    event.remove();
                }
                calendar.addEvent({
                    id: updatedTask.task_id,
                    title: updatedTask.task_name,
                    start: updatedTask.task_date,
                    description: updatedTask.task_description,
                    priority: updatedTask.task_priority,
                    color: getColorForPriority(updatedTask.task_priority)
                });
            })
            .catch(error => console.error('Error:', error));
        }
    }

    function updateTaskInList(task) {
        const taskItem = taskList.querySelector(`li[data-id="${task.task_id}"]`);
        if (taskItem) {
            taskItem.querySelector('.task-text').textContent = task.task_name;
            taskItem.querySelector('.task-date').textContent = new Date(task.task_date).toLocaleString();
            taskItem.querySelector('.task-priority').textContent = task.task_priority;
            taskItem.querySelector('.task-priority').className = `task-priority ${task.task_priority}`;
            const taskIndex = tasks.findIndex(t => t.task_id == task.task_id);
            if (taskIndex !== -1) {
                tasks[taskIndex] = task;
            }
        }
    }

    function removeTaskFromList(id) {
        fetch('delete_task.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({ task_id: id }),
        })
        .then(response => response.json())
        .then(result => {
            if (result.success) {
                const taskItem = taskList.querySelector(`li[data-id="${id}"]`);
                if (taskItem) {
                    taskList.removeChild(taskItem);
                    tasks = tasks.filter(t => t.task_id != id);
                    const event = calendar.getEventById(id);
                    if (event) {
                        event.remove();
                    }
                }
            }
        })
        .catch(error => console.error('Error:', error));
    }

    //PDF Viewer
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

        //Upload PDF to server
        const formData = new FormData();
        formData.append('pdf_file', file);

        fetch('upload_pdf.php', {
            method: 'POST',
            body: formData
        })
        .then(response => response.text())
        .then(result => {
            console.log(result);
            if (result === 'PDF uploaded successfully') {
                // Read the file and display it
                const reader = new FileReader();
                reader.onload = function(e) {
                    pdfViewer.src = e.target.result;
                };
                reader.readAsDataURL(file);
            } else {
                console.error('Error uploading PDF:', result);
            }
        })
        .catch(error => {
            console.error('Error:', error);
        });
    });

    //Initialize tasks
    tasks.forEach(addTaskToList);

    //Landscape Mode Detection
    function checkOrientation() {
        if (window.innerHeight > window.innerWidth) {
            alert("Please use landscape mode for better experience.");
        }
    }

    window.addEventListener('resize', checkOrientation);
    checkOrientation();
});
