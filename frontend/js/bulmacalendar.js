var today = new Date();
var dater = today.getDate() + '/' + (today.getMonth() + 1) + '/' + today.getFullYear();


var calendars = bulmaCalendar.attach('[type="date"]', {startDate: dater, color: 'dark', dateFormat: "dd/MM/yyyy"});

// Loop on each calendar initialized
for(var i = 0; i < calendars.length; i++) {
    // Add listener to date:selected event
    calendars[i].on('select', date => {
        console.log(date);
    });
}

// To access to bulmaCalendar instance of an element
var element = document.querySelector('#my-element');
if (element) {
    // bulmaCalendar instance is available as element.bulmaCalendar
    element.bulmaCalendar.on('select', function(datepicker) {
        console.log(datepicker.data.value());
    });
}




imgInp.onchange = evt => {
    const [file] = imgInp.files
    if (file) {
        blah.src = URL.createObjectURL(file)
    }
}