/* global confirm */
import $ from "jquery";
import BaseView from '../../../lib/baseview';

class View extends BaseView {

    constructor (element, options) {
        super(element);
        console.log("Queue Table");
        this.bindPublicMethods('edit', 'delete');
        this.$.find('a.process-edit').on('click', this.edit);
        this.$.find('a.process-delete').on('click', this.delete);
        this.options = options
    }

    edit () {
        console.log("Edit Button pressed");
        this.$.hide();
        return false;
    }

    delete (ev) {
        ev.preventDefault();
        const id  = $(ev.target).data('id')
        const name  = $(ev.target).data('name')
        const ok = confirm('Wenn Sie den Kunden Nr. '+ id +' '+ name +' löschen wollen, klicken Sie auf OK. Der Kunde wird darüber per eMail und/oder SMS informiert.)')

        if (ok) {
            $.ajax($(ev.target).attr('href'), {
                method: 'DELETE'
            }).done(() => {
                this.$.hide();
            }).fail(err => {
                console.log('ajax error', err)
            })
        }
    }
}

export default View;
