import BaseView from '../lib/baseview';
import $ from "jquery";

class View extends BaseView {

    constructor (element) {
        super(element);
        this.bindPublicMethods('setReloadInterval', 'reloadPage');
        console.log('Redirect to home url every 30 seconds.');
        this.setReloadInterval();
    }

    reloadPage () {
        console.log('reload...')

        $.get( this.getUrl('/home/'), function( response ) {
            $("html").html(response);
            alert( "Load was performed." );
        });
    }

    setReloadInterval () {
        console.log('setInterval...');
        var reloadTime = window.bo.zmsticketprinter.reloadInterval;
        setInterval(this.reloadPage, reloadTime * 1000);
    }

    getUrl (relativePath) {
        let includepath = window.bo.zmsticketprinter.includepath;
        return includepath + relativePath;
    }
}

export default View;
