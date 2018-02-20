import BaseView from "../../lib/baseview"
import $ from "jquery"

class View extends BaseView {

    constructor (element, options) {
        super(element, options);
        this.setOptions(options);
        this.setCallbacks(options);
        this.bindPublicMethods('load');
        $.ajaxSetup({ cache: false });
        this.bindEvents();
        this.load();
    }

    setOptions(options)
    {
        this.source = options.source;
        this.selectedDate = options.selectedDate;
        this.includeUrl = options.includeUrl || "";
        this.showLoader = options.showLoader || false;
    }

    setCallbacks(options)
    {
        this.onDatePick = options.onDatePick;
        this.onDateToday = options.onDateToday;
        this.onDeleteProcess = options.onDeleteProcess;
        this.onEditProcess = options.onEditProcess;
        this.onNextProcess = options.onNextProcess;
        this.onResetProcess = options.onResetProcess;
        this.onSendCustomMail = options.onSendCustomMail;
        this.onSendCustomNotification = options.onSendCustomNotification;
        this.onSendNotificationReminder = options.onSendNotificationReminder;
        this.onChangeTableView = options.onChangeTableView;
        this.onConfirm = options.onConfirm;
        this.onReloadQueueTable = options.onReloadQueueTable;
    }


    load() {
        const url = `${this.includeUrl}/queueTable/?selecteddate=${this.selectedDate}`
        return this.loadContent(url, 'GET', null, null, this.showLoader).catch(err => this.loadErrorCallback(err));
    }

    bindEvents() {
        this.$main.off('click').on('click', '.queue-table .reload', (ev) => {
            this.onReloadQueueTable(ev);
        }).on('change', '.queue-table .switchcluster select', (ev) => {
            this.onChangeTableView(this.$main, ev, $('.sourceSwitchCluster'));
        }).on('change', '.queue-table .appointmentsOnly input', (ev) => {
            this.onChangeTableView(this.$main, ev, $('.sourceAppointmentsOnly'));
        }).on('click', 'a.process-edit', (ev) => {
            this.onEditProcess(ev)
        }).on('click', 'a.process-reset', (ev) => {
            this.onResetProcess(this.$main, ev);
        }).on('click', 'a.process-delete', (ev) => {
            this.onConfirm(ev, "confirm_delete", () => {this.onDeleteProcess(null, ev)});
        }).on('click', '.queue-table .calendar-navigation .pagedaylink', (ev) => {
            this.onDatePick($(ev.target).attr('data-date'), this);
        }).on('click', '.queue-table .calendar-navigation .today', (ev) => {
            this.onDateToday($(ev.target).attr('data-date'), this)
        }).on('click', '.queue-table .process-notification-send', (ev) => {
            this.onConfirm(ev, "confirm_notification_reminder", () => {this.onSendNotificationReminder(this.$main, ev)});
        }).on('click', '.process-custom-mail-send', (ev) => {
            this.onSendCustomMail(this.$main, ev);
        }).on('click', '.process-custom-notification-send', (ev) => {
            this.onSendCustomNotification(this.$main, ev);
        })
    }
}

export default View;
