// Task Manager Logic
function addTask() {
  var taskNameInput = document.getElementById("task-name");
  var dueDateInput = document.getElementById("due-date");
  var taskName = taskNameInput.value.trim();
  var dueDate = dueDateInput.value;
  if (taskName !== "" && dueDate !== "") {
    var taskList = document.getElementById("task-list");
    var newRow = taskList.insertRow();
    var priority = calculatePriority(dueDate);
    newRow.innerHTML = `
      <td>${priority}</td>
      <td>${taskName}</td>
      <td>${dueDate}</td>
      <td><button onclick="deleteTask(this)">Delete</button></td>
    `;
    taskNameInput.value = "";
    dueDateInput.value = "";
    sortTableByPriority();
  }
}

function calculatePriority(dueDate) {
  var currentDate = new Date();
  var due = new Date(dueDate);
  var diffTime = Math.abs(due - currentDate);
  var diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24));
  return diffDays;
}

function sortTableByPriority() {
  var table, rows, switching, i, x, y, shouldSwitch;
  table = document.querySelector(".task-table");
  switching = true;
  while (switching) {
    switching = false;
    rows = table.rows;
    for (i = 1; i < (rows.length - 1); i++) {
      shouldSwitch = false;
      x = parseInt(rows[i].getElementsByTagName("td")[0].textContent);
      y = parseInt(rows[i + 1].getElementsByTagName("td")[0].textContent);
      if (x > y) {
        shouldSwitch = true;
        break;
      }
    }
    if (shouldSwitch) {
      rows[i].parentNode.insertBefore(rows[i + 1], rows[i]);
      switching = true;
    }
  }
}

function deleteTask(button) {
  var row = button.parentNode.parentNode;
  row.parentNode.removeChild(row);
}

// Calendar Logic
function generateCalendar() {
  var today = new Date();
  var year = today.getFullYear();
  var month = today.getMonth();
  var calendarBody = document.getElementById("calendar-body");
  var daysInMonth = new Date(year, month + 1, 0).getDate();
  var firstDay = new Date(year, month, 1).getDay();

  var date = 1;
  for (var i = 0; i < 6; i++) {
    var row = document.createElement("tr");
    for (var j = 0; j < 7; j++) {
      var cell = document.createElement("td");
      if (i === 0 && j < firstDay) {
        cell.textContent = "";
      } else if (date > daysInMonth) {
        break;
      } else {
        cell.textContent = date;
        if (date === today.getDate() && month === today.getMonth() && year === today.getFullYear()) {
          cell.classList.add("today");
        }
        date++;
      }
      row.appendChild(cell);
    }
    calendarBody.appendChild(row);
  }
}

generateCalendar();
