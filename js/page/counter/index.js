import BaseView from '../../lib/baseview'
import $ from 'jquery'
import AppointmentView from '../../block/appointment'
import QueueView from '../../block/queue'
import CalendarView from './calendar'

import { loadInto } from './utils'


class View extends BaseView {

    constructor (element, options) {
        super(element);
        this.element = $(element);
        this.includeUrl = options.includeurl;
        this.selectedDate = options['selected-date'];
        this.bindPublicMethods('loadAllPartials', 'selectDateWithOverlay', 'onDatePick');
        this.$.ready(this.loadData);
        $.ajaxSetup({ cache: false });
        this.loadAllPartials().then(() => this.bindEvents());
        console.log('Component: Counter', this, options);
    }

    bindEvents() {
        
    }

    selectDateWithOverlay() {
        return new Promise((resolve, reject) => {
            const overlay = this.$main.find('[data-calendar-overlay]');
            overlay.off('click');
            this.$main.attr('data-show-calendar-overlay', true);

            const close = () => {
                this.$main.removeAttr('data-show-calendar-overlay');
                tempCalendar.destroy()
            }

            const tempCalendar = new CalendarView(overlay, {
                includeUrl: this.includeUrl,
                selectedDate: this.selectedDate,
                onDatePick: (date) => {
                    close()
                    resolve(date);
                }
            })

            overlay.on('click', () => {
                close()
                reject()
            })
        });
    }

    onDatePick(date) {
        this.selectedDate = date;
        this.loadAllPartials();
    }

    loadAllPartials() {
        return Promise.all([
            this.loadCalendar(),
            this.loadAllExceptCalendar()
        ])
    }

    loadAllExceptCalendar() {
        return Promise.all([
            this.loadAppointmentForm(),
            this.loadQueueTable(),
            this.loadQueueInfo()
        ]);
    }

    loadCalendar () {
        return new CalendarView(this.$main.find('[data-calendar]'), {
            selectedDate: this.selectedDate,
            onDatePick: this.onDatePick,
            includeUrl: this.includeUrl
        })
    }

    loadAppointmentForm() {
        return new AppointmentView(this.$main.find('[data-appointment-form]'), {
            selectedDate: this.selectedDate,
            includeUrl: this.includeUrl,
            selectDateWithOverlay: this.selectDateWithOverlay,
        })
    }

    loadQueueInfo () {
        const url = `${this.includeUrl}/counter/queueInfo/?selecteddate=${this.selectedDate}`
        this.loadQueueInfoPromise = loadInto(url, this.element.find('[data-queue-info]'))
        return this.loadQueueInfoPromise;
    }

    loadQueueTable () {
        return new QueueView(this.$main.find('[data-queue-table]'), {
            selectedDate: this.selectedDate,
            includeUrl: this.includeUrl,
            onDatePick: this.onDatePick
        })
    }

}

export default View;
