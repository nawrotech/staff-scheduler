import { Controller } from '@hotwired/stimulus';
import { Calendar } from "https://cdn.skypack.dev/@fullcalendar/core@6.1.17";
import dayGridPlugin from "https://cdn.skypack.dev/@fullcalendar/daygrid@6.1.17";
import timeGridPlugin from "https://cdn.skypack.dev/@fullcalendar/timegrid@6.1.17";
import bootstrap5Plugin from "https://cdn.skypack.dev/@fullcalendar/bootstrap5@6.1.17";

export default class extends Controller {
    static targets = ['calendarElement']

    static values = {
        shiftsUrl: String,
        shiftShowUrl: String
    }

    calendar = null;

    connect() {
        console.log(this.shiftShowUrlValue);

        this.calendar = new Calendar(this.calendarElementTarget, {
            plugins: [dayGridPlugin, timeGridPlugin, bootstrap5Plugin],
            headerToolbar: {
                left: '',
                center: 'title',
                right: 'prev,next today',
            },
            themeSystem: 'bootstrap5',
            events: this.shiftsUrlValue,
            eventTimeFormat: {
                hour: '2-digit',
                minute: '2-digit',
                hour12: false
            },
            displayEventTime: true,
            displayEventEnd: true,
            eventDisplay: 'block',
            eventDidMount: (info) => {
                let anchor = info.el;
                const shiftId = info.event.id;
                if (anchor) {
                    anchor.href = `${this.shiftShowUrlValue}/${shiftId}`; 
                }
              },
            editable: true,
            height: 'auto'
        });
        
        this.calendar.render();
    }

    disconnect() {
        if (this.calendar) {
            this.calendar.destroy();
            this.calendar = null;
        }
    }
}