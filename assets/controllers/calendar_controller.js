import { Controller } from '@hotwired/stimulus';
import { Calendar } from "https://cdn.skypack.dev/@fullcalendar/core@6.1.17";
import dayGridPlugin from "https://cdn.skypack.dev/@fullcalendar/daygrid@6.1.17";

export default class extends Controller {
    static targets = ['calendarElement']
    calendar = null;

    connect() {
        this.calendar = new Calendar(this.calendarElementTarget, {
            initialView: 'dayGridMonth',
            plugins: [dayGridPlugin],
            headerToolbar: {
                left: 'prev,next today',
                center: 'title',
                right: 'dayGridMonth'
            },
            events: [
                { title: 'Sample Shift A', start: '2025-04-26' },
                { title: 'Sample Shift B', start: '2025-04-27T10:00:00', end: '2025-04-27T14:00:00' }
            ],
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