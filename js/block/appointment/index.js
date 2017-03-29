import BaseView from "../../lib/baseview"
import $ from "jquery"

class View extends BaseView {

    constructor (element, options) {
        super(element);
        this.selectedDate = options.selectedDate;
        this.includeUrl = options.includeUrl || "";
        this.selectDateWithOverlay = options.selectDateWithOverlay;
        this.selectedProcess = options.selectedProcess
        this.serviceList = [];

        this.load().then(() => {
            this.cleanUpLists();
            this.bindEvents();
        });
    }

    load() {
        const url = `${this.includeUrl}/appointmentForm/?selecteddate=${this.selectedDate}&selectedprocess=${this.selectedProcess}`
        this.loadPromise = this.loadContent(url)
        return this.loadPromise;
    }

    bindEvents() {
        this.$main.on('change', '.checkboxselect input:checkbox', (event) => {
            this.addService($(event.target), this.serviceListSelected);
            this.removeService($(event.target), this.serviceList);
            this.updateList();
        }).on('change', '.checkboxdeselect input:checkbox', (event) => {
            this.removeService($(event.target), this.serviceListSelected);
            this.addService($(event.target), this.serviceList);
            this.updateList();
        }).on('click', '.clear-list', () => {
            this.cleanUpLists();
            this.updateList();
        }).on('click', 'input[name=date]', () => {
            console.log('date click')
            this.selectDateWithOverlay()
                   .then(date => {
                       this.selectedDate = date;
                       this.load();
                   })
                   .catch(() => console.log('no date selected'));
        })
    }

    /**
     * update events after replacing list
     */
    updateList () {
        this.$main.find('.checkboxdeselect input:checkbox').each((index, element) => {
            $(element).prop("checked", false);
            $(element).closest('label').hide();
            if ($.inArray($(element).val(), this.serviceListSelected) !== -1) {
                $(element).prop("checked", true);
                $(element).closest('label').show();
            }
        });

        this.$main.find('.checkboxselect input:checkbox').each((index, element) => {
            $(element).prop("checked", false);
            $(element).closest('label').hide();
            if ($.inArray($(element).val(), this.serviceList) !== -1) {
                $(element).closest('label').show();
            }
        });

        this.calculateSlotCount();
    }

    addService (element, list) {
        return list.push(element.val());
    }

    removeService (element, list) {
        for (var i = 0; i < list.length; i++)
            if (list[i] === element.val()) {
                return list.splice(i,1);
            }
    }

    cleanUpLists ()
    {
        this.serviceList = this.$main.find('.checkboxselect input:checkbox').map(function() {
            return $(this).val();
        }).toArray();
        this.serviceListSelected = [];
    }

    calculateSlotCount () {
        var slotCount = 0;
        var selectedSlots = this.$main.find('.checkboxdeselect label:visible input:checkbox').map(function() {
            return $(this).data('slots');
        }).toArray();
        for (var i = 0; i < selectedSlots.length; i++)
            if (selectedSlots[i] > 0) {
                slotCount += selectedSlots[i];
            }
        console.log(slotCount);
    }
}

export default View;
