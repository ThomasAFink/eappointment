import BaseView from "../../lib/baseview"
import $ from "jquery"

class View extends BaseView {

    constructor(element, options) {
        super(element);
        this.$main = $(element);
        this.includeUrl = options.includeUrl || "";
        this.selectedDate = options.selectedDate;
        this.selectedTime = options.selectedTime || "00-00";
        this.selectedProcess = options.selectedProcess || 0;
        this.hasFreeAppointments = options.hasFreeAppointments || 0;
        console.log(this.hasFreeAppointments);
        this.bindPublicMethods('loadButtons');
        $.ajaxSetup({ cache: false });
    }

    loadButtons() {
        const url = `${this.includeUrl}/appointmentForm/buttons/?selecteddate=${this.selectedDate}&selectedprocess=${this.selectedProcess}&selectedTime=${this.selectedTime}`
        return this.loadContent(url, 'GET', null, null, false)
            .then(() => { this.toggleButtonDisabled() })
            .catch(err => this.loadErrorCallback(err.source, err.url)
            );
    }

    toggleButtonDisabled() {
        if (0 == this.hasFreeAppointments) {
            this.$main.find('button').not('.process-abort, .process-queue').prop("disabled", true);
        }
    }

    loadErrorCallback(source, url) {
        if (source == 'button') {
            return this.loadContent(url)
        } else if (source == 'lightbox') {
            console.log('lightbox closed without action call');
        } else {
            const defaultUrl = `${this.includeUrl}/counter/`
            return this.loadContent(defaultUrl)
        }
    }
}

export default View;
