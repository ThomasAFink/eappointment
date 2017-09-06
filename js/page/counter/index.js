import BaseView from '../../lib/baseview'
import $ from 'jquery'
import AppointmentView from '../../block/appointment'
import AppointmentTimesView from '../../block/appointment/times'
import QueueView from '../../block/queue'
import QueueInfoView from '../../block/queue/info'
import CalendarView from '../../block/calendar'

import { loadInto } from './utils'
import { lightbox } from '../../lib/utils'

const reloadInterval = 60; //seconds

class View extends BaseView {

    constructor (element, options) {
        super(element);
        this.element = $(element);
        this.includeUrl = options.includeurl;
        this.selectedDate = options['selected-date'];
        this.selectedTime = options['selected-time'];
        this.selectedProcess = options['selected-process'];
        this.reloadTimer;
        this.bindPublicMethods('loadAllPartials', 'selectDateWithOverlay', 'onDatePick', 'onNextProcess', 'onDateToday', 'onGhostWorkstationChange','onDeleteProcess','onEditProcess','onSaveProcess','onQueueProcess');
        this.$.ready(() => {
            this.loadData;
            this.reloadPartials();
        });
        $.ajaxSetup({ cache: false });
        this.loadAllPartials().then(() => this.bindEvents());
        console.log('Component: Counter', this, options);
    }

    bindEvents() {
        window.onfocus = () => {
            console.log("on Focus");
            this.reloadPartials();
        }
        window.onblur = () => {
            console.log("lost Focus");
            clearTimeout(this.reloadTimer);
        }
        this.$main.find('[data-queue-table]').mouseenter(() => {
            console.log("stop Reload on mouse enter queue table");
            clearTimeout(this.reloadTimer);
        });
        this.$main.find('[data-queue-table]').mouseleave(() => {
            console.log("start reload on mouse out queue table");
            this.reloadPartials();
        });
        this.$main.find('[data-queue-info]').mouseenter(() => {
            console.log("stop Reload on mouse enter queue infobox");
            clearTimeout(this.reloadTimer);
        });
        this.$main.find('[data-queue-info]').mouseleave(() => {
            console.log("start reload on mouse out queue infobox");
            this.reloadPartials();
        });

    }

    reloadPartials() {
        clearTimeout(this.reloadTimer);
        this.reloadTimer = setTimeout(() => {
            this.loadQueueTable();
            this.loadQueueInfo(),
            this.reloadPartials();
            this.bindEvents();
        }, reloadInterval * 1000);
    }

    selectDateWithOverlay() {
        return new Promise((resolve, reject) => {
            const destroyCalendar = () => {
                tempCalendar.destroy()
            }

            const { lightboxContentElement, destroyLightbox } = lightbox(this.$main, () => {
                destroyCalendar()
                reject()
            })

            const tempCalendar = new CalendarView(lightboxContentElement, {
                includeUrl: this.includeUrl,
                selectedDate: this.selectedDate,
                onDatePick: (date) => {
                    destroyCalendar()
                    destroyLightbox()
                    //resolve(date);
                },
                onDateToday: (date) => {
                    destroyCalendar()
                    destroyLightbox()
                    //resolve(date);
                }
            })
        });
    }

    onDatePick(date) {
        this.selectedDate = date;
        this.loadAllPartials();
    }

    onDateToday(date) {
        this.selectedDate = date;
        this.loadCalendar(),
        this.loadAppointmentForm(),
        this.loadQueueTable(),
        this.loadAppointmentTimes()
    }

    onNextProcess() {
        this.loadQueueTable();
    }

    onDeleteProcess () {
        this.selectedProcess = null;
        this.loadAppointmentForm();
        this.loadQueueTable();
    };

    onQueueProcess () {
        this.selectedProcess = null;
        this.loadAppointmentForm();
        this.loadQueueTable();
    };

    onEditProcess (processId) {
        this.selectedProcess = processId;
        this.loadAppointmentForm();
    };

    onSaveProcess (processId) {
        if (processId)
            this.selectedProcess = processId;
        this.loadAppointmentForm();
        this.loadQueueTable();
    }

    onGhostWorkstationChange() {
        this.loadAllPartials();
    }

    loadAllPartials() {
        return Promise.all([
            this.loadCalendar(),
            this.loadAppointmentForm(),
            this.loadQueueTable(),
            this.loadQueueInfo(),
            this.loadAppointmentTimes()
        ])
    }

    loadCalendar () {
        return new CalendarView(this.$main.find('[data-calendar]'), {
            selectedDate: this.selectedDate,
            onDatePick: this.onDatePick,
            onDateToday: this.onDateToday,
            includeUrl: this.includeUrl
        })
    }

    loadAppointmentForm() {
        return new AppointmentView(this.$main.find('[data-appointment-form]'), {
            source: 'counter',
            selectedDate: this.selectedDate,
            selectedTime: this.selectedTime,
            selectedProcess: this.selectedProcess,
            includeUrl: this.includeUrl,
            onDeleteProcess: this.onDeleteProcess,
            onQueueProcess: this.onQueueProcess,
            onSaveProcess: this.onSaveProcess
        })
    }

    loadQueueTable () {
        return new QueueView(this.$main.find('[data-queue-table]'), {
            source: 'counter',
            selectedDate: this.selectedDate,
            includeUrl: this.includeUrl,
            onDatePick: this.onDatePick,
            onDateToday: this.onDateToday,
            onDeleteProcess: this.onDeleteProcess,
            onEditProcess: this.onEditProcess,
            onNextProcess: this.onNextProcess
        })
    }

    loadAppointmentTimes () {
        return new AppointmentTimesView(this.$main.find('[data-appointment-times]'), {
            selectedDate: this.selectedDate,
            includeUrl: this.includeUrl
        })
    }

    loadQueueInfo () {
        return new QueueInfoView(this.$main.find('[data-queue-info]'), {
            selectedDate: this.selectedDate,
            includeUrl: this.includeUrl,
            onGhostWorkstationChange: this.onGhostWorkstationChange
        })
    }

}

export default View;
