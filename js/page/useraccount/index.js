import BaseView from '../../lib/baseview'
import $ from 'jquery'
import { stopEvent } from '../../lib/utils'

class View extends BaseView {
    constructor (element, options) {
        super(element);
        this.$main = $(element);
        this.includeUrl = options.includeurl;
        this.randomPassword = this.createRandomPassword();
        this.bindPublicMethods();
        $(this.bindEvents());
    }

    bindEvents() {
        this.$main.off('click').on('click', 'a.button-delete', (ev) => {
            this.onConfirm(ev, "confirm_user_delete", () => {this.onDelete(ev)});
        }).on('change', '#useOidcProvider', (event) => {
            this.toggleCredentials(event);
        });
    }

    onConfirm(event, template, callback)
    {
      stopEvent(event);
      const userName  = $(event.currentTarget).data('name');
      this.loadCall(`${this.includeUrl}/dialog/?template=${template}&parameter[name]=${userName}`).then((response) => {
           this.loadDialog(response, callback, null, event.currentTarget);
      });
    }

    onDelete(ev) {
        window.location.href = ev.target.href;
    }

    toggleCredentials(event) {
        this.$main.find('input[type="password"]').each((index, item) => {
            if ($(event.target).val() != "") {
                console.log(this.randomPassword, $(event.target).val())

                $(item).prop('readonly', true);
                $(item).val(this.randomPassword)
            } else {
                $(event.target).prop('checked', false)
                $(item).prop('readonly', false);
                $(item).val('')
            }
        })
    }

    createRandomPassword() {
        var password = '';
            var str = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ' + 
                    'abcdefghijklmnopqrstuvwxyz0123456789@#$';      
            for (let i = 1; i <= 8; i++) {
                var char = Math.floor(Math.random()
                            * str.length + 1);
                  
                password += str.charAt(char)
            }
        return password;
    }
}

export default View;
