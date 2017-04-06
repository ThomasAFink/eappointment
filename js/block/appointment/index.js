import BaseView from "../../lib/baseview"
import $ from "jquery"
import freeProcessList from './free-process-list'
import { lightbox } from '../../lib/utils'
import CalendarView from '../calendar'
import FormValidationView from '../form-validation'
import ExceptionHandler from '../../lib/exceptionHandler'
import moment from 'moment'

class View extends BaseView {

    constructor (element, options) {
        super(element);
        this.selectedDate = options.selectedDate;
        this.selectedTime = options.selectedTime;
        this.includeUrl = options.includeUrl || "";
        this.selectedProcess = options.selectedProcess;
        this.slotCount = 0;
        this.slotType = 'intern';
        this.serviceList = [];
        this.serviceListSelected = [];
        $.ajaxSetup({ cache: false });
        this.load().then(() => {
            if (this.selectedProcess)
                this.readSelectedList()
            else
                this.cleanRequestLists();
            this.loadFreeProcessList();
            this.bindEvents();
        });
    }

    load() {
        const url = `${this.includeUrl}/appointmentForm/?selecteddate=${this.selectedDate}&selectedprocess=${this.selectedProcess}`
        this.loadPromise = this.loadContent(url).catch(err => this.loadErrorCallback(err));
        return this.loadPromise;
    }

    loadFreeProcessList () {
        return new freeProcessList(this.$main.find('[data-free-process-list]'), {
            selectedDate: this.selectedDate,
            selectedTime: this.selectedTime,
            includeUrl: this.includeUrl,
            slotsRequired: this.slotCount,
            slotType: this.slotType
        })
    }

    cleanReload () {
        this.selectedProcess = null;
        this.load().then(() => {
            this.cleanRequestLists();
            this.loadFreeProcessList();
            this.bindEvents();
        });
    }

    bindEvents() {
        this.$main.on('change', '.checkboxselect input:checkbox', (event) => {
            this.addServiceToList($(event.target), this.serviceListSelected);
            this.removeServiceFromList($(event.target), this.serviceList);
            this.updateRequestLists();
        }).on('change', '.checkboxdeselect input:checkbox', (event) => {
            this.removeServiceFromList($(event.target), this.serviceListSelected);
            this.addServiceToList($(event.target), this.serviceList);
            this.updateRequestLists();
        }).on('click', '.clear-list', () => {
            this.cleanRequestLists();
            this.updateRequestLists();
        }).on('click', 'input[name=date]', () => {
            this.selectDateWithOverlay()
                   .then(date => {
                       this.selectedDate = date;
                       this.setSelectedDate();
                   })
                   .then(() => {
                       this.calculateSlotCount();
                       this.loadFreeProcessList();
                   })
                   .catch(() => console.log('no date selected'));
        }).on('change', 'select#appointmentForm_slotCount', (event) => {
            console.log('slots changed manualy');
            this.slotCount = this.$main.find('select#appointmentForm_slotCount').val();
            this.loadFreeProcessList();
        }).on('click', '.form-actions button.process-reserve', (event) => {
            event.preventDefault();
            event.stopPropagation();
            console.log('reserve button clicked');
            this.reserveProcess();
        }).on('click', '.form-actions button.process-queue', (event) => {
            event.preventDefault();
            event.stopPropagation();
            console.log('queue button clicked')
        })
    }

    /**
     * reserve process
     */
    reserveProcess () {
        this.selectedDate = moment(this.$main.find('.appointment-form form #process_date').val(), 'DD.MM.YYYY').format('YYYY-MM-DD');
        this.selectedTime = this.$main.find('.appointment-form form #process_time').val();
        const sendData = this.$main.find('.appointment-form form').serialize();
        const url = `/process/${this.selectedDate}/${this.selectedTime}/reserve/`;
        this.loadCall(url, 'POST', sendData).catch(err => this.loadErrorCallback(err)).then((processData) => {
            if (processData) {
                console.log('RESERVE POST successfully', processData);
                if ('confirmed' == processData.status)
                    this.selectedProcess = processData.id;
                    this.load();
            }
        });
    }

    loadErrorCallback(err) {
        let isException = err.message.toLowerCase().includes('exception');
        if (err.status == 428)
            new FormValidationView(this.$main.find('.appointment-form form'), {
                responseJson: err.responseJSON
            });
        else if (isException) {
            let exceptionType = $(err.message).filter('.exception').data('exception');
            if (exceptionType === 'reservation-failed')
                this.loadFreeProcessList();
            if (exceptionType === 'process-not-found')
                this.cleanReload()
            else {
                this.load();
                console.log('EXCEPTION thrown: ' + exceptionType);
            }
        }
        else
            console.log('Ajax error', err);
    }

    /**
     * update events after replacing list
     */
    updateRequestLists () {
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
        this.loadFreeProcessList();
    }

    readSelectedList ()
    {
        this.$main.find('.checkboxselect input:checked').each((index, element) => {
            if ($.inArray($(element).val(), this.serviceListSelected) === -1)
                this.addServiceToList ($(element), this.serviceListSelected)
        });
        this.$main.find('.checkboxdeselect input:not(:checked)').each((index, element) => {
            if ($.inArray($(element).val(), this.serviceList) === -1)
                this.addServiceToList ($(element), this.serviceList)
        });
        this.updateRequestLists();
    }

    addServiceToList (element, list) {
        return list.push(element.val());
    }

    removeServiceFromList (element, list) {
        for (var i = 0; i < list.length; i++)
            if (list[i] === element.val()) {
                return list.splice(i,1);
            }
    }

    cleanRequestLists ()
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
        for (var i = 1; i < selectedSlots.length; i++)
            if (selectedSlots[i] > 0) {
                slotCount += selectedSlots[i];
            }
        this.slotCount = slotCount;
        this.$main.find('#appointmentForm_slotCount option:eq(' + this.slotCount +')').prop('selected', true)
    }

    selectDateWithOverlay () {
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
                    resolve(date);
                },
                onDateToday: (date) => {
                    destroyCalendar()
                    destroyLightbox()
                    resolve(date);
                }
            })
        });
    }

    setSelectedDate () {
        this.$main.find('.add-date-picker input[name="date"]').val(moment(this.selectedDate, 'YYYY-MM-DD').format('L'))
    }
}

export default View;
