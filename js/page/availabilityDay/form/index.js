import React, { Component } from 'react'
import PropTypes from 'prop-types'
import HeaderButtons from './headerButtons'
import FormContent from './content'
import { repeat } from '../helpers'

const getFirstLevelValues = data => {
    const {
        __modified,
        scope,
        description,
        startTime,
        endTime,
        startDate,
        endDate,
        multipleSlotsAllowed,
        id,
        tempId,
        type,
        slotTimeInMinutes
    } = data

    return {
        __modified,
        scope,
        description,
        startTime,
        endTime,
        startDate,
        endDate,
        multipleSlotsAllowed,
        id,
        tempId,
        type,
        slotTimeInMinutes
    }
}

const getFormValuesFromData = data => {
    const workstations = Object.assign({}, data.workstationCount)

    if (parseInt(workstations.callcenter, 10) > parseInt(workstations.intern, 10)) {
        workstations.callcenter = workstations.intern
    }

    if (parseInt(workstations.public, 10) > parseInt(workstations.intern, 10)) {
        workstations.public = workstations.intern
    }

    const openFrom = data.bookable.startInDays
    const openFromDefault = data.scope.preferences.appointment.startInDaysDefault
    const openTo = data.bookable.endInDays
    const openToDefault = data.scope.preferences.appointment.endInDaysDefault
    const repeatSeries = repeat(data.repeat);

    return cleanupFormData(Object.assign({}, getFirstLevelValues(data), {
        open_from: openFrom,
        open_to: openTo,
        openFromDefault,
        openToDefault,
        repeat: repeatSeries,
        workstationCount_intern: workstations.intern,
        workstationCount_callcenter: workstations.callcenter,
        workstationCount_public: workstations.public,
        weekday: Object.keys(data.weekday).filter(key => parseInt(data.weekday[key], 10) > 0)
    }))
}

const getDataValuesFromForm = (form, scope) => {
    return Object.assign({}, getFirstLevelValues(form), {
        bookable: {
            startInDays: form.open_from === "" ? scope.preferences.appointment.startInDaysDefault : form.open_from,
            endInDays: form.open_to === "" ? scope.preferences.appointment.endInDaysDefault : form.open_to
        },
        workstationCount: {
            intern: form.workstationCount_intern,
            callcenter: form.workstationCount_callcenter,
            "public": form.workstationCount_public
        },
        weekday: form.weekday.reduce((carry, current) => {
            return Object.assign({}, carry, { [current]: 1 })
        }, {}),
        repeat: {
            weekOfMonth: form.repeat > 0 ? form.repeat : 0,
            afterWeeks: form.repeat < 0 ? -form.repeat : 0
        }
    })
}

const cleanupFormData = data => {
    let internCount = parseInt(data.workstationCount_intern, 10);
    let callcenterCount = parseInt(data.workstationCount_callcenter, 10);
    callcenterCount = (callcenterCount > internCount) ? internCount : callcenterCount;
    let publicCount = parseInt(data.workstationCount_public, 10);
    publicCount = (publicCount > internCount) ? internCount : publicCount;
    return Object.assign({}, data, {
        workstationCount_callcenter: callcenterCount,
        workstationCount_public: publicCount,
        open_from: (data.open_from === "0" || data.open_from === data.openFromDefault) ? "" : data.open_from,
        open_to: (data.open_to === "0" || data.open_to === data.openToDefault) ? "" : data.open_to
    })
}

class AvailabilityForm extends Component {
    constructor(props) {
        super(props);
        
        this.state = {
            data: getFormValuesFromData(this.props.data),
            errors: {}
        };
        this.handleFocus = props.handleFocus
    }

    componentDidUpdate(prevProps) {
        if (this.props.data !== prevProps.data) {
            this.setState({
                data: getFormValuesFromData(this.props.data)
            })
        }
    }

    handleChange(name, value) {
        this.setState({
            data: cleanupFormData(Object.assign({}, this.state.data, {
                [name]: value,
                __modified: true
            }))
        }, () => {
            this.props.onChange(getDataValuesFromForm(this.state.data, this.props.data.scope))
        })
    }

    render() {
        const { data, errors } = this.state
        const onChange = (name, value) => {
            this.handleChange(name, value)
        }

        const onCopy = ev => {
            ev.preventDefault()
            this.props.onCopy(getDataValuesFromForm(data, this.props.data.scope))
        }

        const onException = ev => {
            ev.preventDefault()
            this.props.onException(getDataValuesFromForm(data, this.props.data.scope))
        }

        const onEditInFuture = ev => {
            ev.preventDefault()
            this.props.onEditInFuture(getDataValuesFromForm(data, this.props.data.scope))
        }

        return (
            <div>
                {<HeaderButtons {...{ onCopy, onException, onEditInFuture }} />}
                {<FormContent {... { data, errors, onChange }} />}
            </div>
        )   
    }
}

AvailabilityForm.defaultProps = {
    data: {},
    onChange: () => { },
    onCopy: () => { },
    onException: () => { },
    onEditInFuture: () => { }
}

AvailabilityForm.propTypes = {
    data: PropTypes.object,
    onChange: PropTypes.func,
    onCopy: PropTypes.func,
    onException: PropTypes.func,
    onEditInFuture: PropTypes.func,
    handleFocus: PropTypes.func
}

export default AvailabilityForm
