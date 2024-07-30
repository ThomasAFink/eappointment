import BaseView from '../lib/baseview';
import $ from "jquery";

class View extends BaseView {

    constructor (element) {
        super(element);
        this.bindPublicMethods('setInterval', 'reloadPage');
        console.log('Redirect to home url every 30 seconds');

        this.setReloadInterval();
    }

    reloadPage () {
        console.log('reloading')
        window.location.href = this.getUrl('/home/');
    }

    setReloadInterval () {
        var reloadTime = window.bo.zmsticketprinter.reloadInterval;
        setInterval(this.reloadPage, reloadTime * 1000);
    }

    getUrl (relativePath) {
        let includepath = window.bo.zmsticketprinter.includepath;
        return includepath + relativePath;
    }
}

export default View;
