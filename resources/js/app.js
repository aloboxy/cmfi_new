import Alpine from 'alpinejs';
import collapse from '@alpinejs/collapse';
import Chart from 'chart.js/auto';
import { Calendar } from '@fullcalendar/core';
import dayGridPlugin from '@fullcalendar/daygrid';
import timeGridPlugin from '@fullcalendar/timegrid';
import listPlugin from '@fullcalendar/list';
import interactionPlugin from '@fullcalendar/interaction';

window.Alpine = Alpine;
Alpine.plugin(collapse);
window.Chart = Chart;

document.addEventListener('DOMContentLoaded', () => {
    document.querySelectorAll('[data-chart]').forEach((el) => {
        try {
            new Chart(el, JSON.parse(el.dataset.chart));
        } catch (error) {
            console.error('Invalid chart config', error);
        }
    });

    const calEl = document.getElementById('event_calendar');
    if (calEl && window.dashboardEvents) {
        new Calendar(calEl, {
            plugins: [dayGridPlugin, timeGridPlugin, listPlugin, interactionPlugin],
            initialView: 'dayGridMonth',
            headerToolbar: {
                left: 'prev,next today',
                center: 'title',
                right: 'dayGridMonth,timeGridWeek,timeGridDay,listWeek'
            },
            firstDay: 1,
            height: 580,
            events: window.dashboardEvents,
            eventClick: function (info) {
                if (info.event.url) {
                    window.open(info.event.url, '_blank');
                    info.jsEvent.preventDefault();
                }
            },
            buttonText: {
                today: 'Today',
                month: 'Month',
                week: 'Week',
                day: 'Day',
                list: 'List'
            }
        }).render();
    }
});

Alpine.start();