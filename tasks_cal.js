document.addEventListener('DOMContentLoaded', function() {
  var calendarEl = document.getElementById('full-calendar');

  var calendar = new FullCalendar.Calendar(calendarEl, {
    plugins: [ 'dayGrid' ], // specify plugins
    header: {
      left: 'prev,next today',
      center: 'title',
      right: 'dayGridMonth,timeGridWeek,timeGridDay'
    },
    events: [
      // your events data here
      // { title: 'Event 1', start: '2024-06-01' },
      // { title: 'Event 2', start: '2024-06-05' }
    ]
  });

  calendar.render();

  // Function to add a task (example)
  window.addTask = function() {
    var taskName = document.getElementById('task-name').value;
    var dueDate = document.getElementById('due-date').value;
    
    // Add your task handling logic here (e.g., adding to a list, updating calendar events)
    console.log('Task Name:', taskName);
    console.log('Due Date:', dueDate);
  };
});
