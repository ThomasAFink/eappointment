import BaseView from "../../lib/baseview"
import $ from "jquery"
const loaderHtml = '<div class="loader-small"></div>'

class View extends BaseView {

    constructor (element, options) {
        super(element, options);
        this.selectedDate = options.selectedDate;
        this.includeUrl = options.includeUrl || "";
        this.bindPublicMethods('load');
        $.ajaxSetup({ cache: false });
        console.log('Component: Appointment Times', this, options);
        this.load();
    }

    load() {
        const url = `${this.includeUrl}/counter/appointmentTimes/?selecteddate=${this.selectedDate}`
        return this.loadContent(url, 'GET', null, 'small').catch(err => this.loadErrorCallback(err.source, err.url));
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
