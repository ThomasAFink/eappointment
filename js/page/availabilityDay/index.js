import React, { Component } from 'react'
import PropTypes from 'prop-types'
import $ from 'jquery'
import moment from 'moment'
import Conflicts from './conflicts'
import TabsBar from './tabsbar'
import GraphView from './timetable/graphview.js'
import TableView from './timetable/tableview.js'
import SaveBar from './saveBar'
import validate from './form/validate'
import AccordionLayout from './layouts/accordion'
import PageLayout from './layouts/page'

import {
    getInitialState,
    getStateFromProps,
    getNewAvailability,
    mergeAvailabilityListIntoState,
    updateAvailabilityInState,
    cleanupAvailabilityForSave,
    deleteAvailabilityInState,
    filterEmptyAvailability
} from "./helpers"

const tempId = (() => {
    let lastId = -1

    return () => {
        lastId += 1
        return `__temp__${lastId}`
    }
})()

const formatTimestampDate = timestamp => moment(timestamp, 'X').format('YYYY-MM-DD')

class AvailabilityPage extends Component {
    constructor(props) {
        super(props)
        this.state = getInitialState(props)
    }

    componentDidMount() {
        this.unloadHandler = ev => {
            const confirmMessage = "Es wurden nicht alle Änderungen gespeichert. Diese gehen beim schließen verloren."
            if (this.state.stateChanged) {
                ev.returnValue = confirmMessage
                return confirmMessage
            }
        }

        window.addEventListener('beforeunload', this.unloadHandler)
    }

    componentDidUnMount() {
        window.removeEventListener('beforeunload', this.unloadHandler)
    }

    onUpdateAvailability(availability) {
        let state = {};
        if (availability.__modified) {
            state = Object.assign(state, updateAvailabilityInState(this.state, availability), {
                selectedAvailability: null
            })
        } else {
            state = { selectedAvailability: null }
        }
        this.setState(state);
        $('body').scrollTop(0);
        return state;
    }

    onPublishAvailability(availability) {
        const state = this.onUpdateAvailability(availability);
        this.onSaveUpdates(state);
    }

    refreshData() {
        const currentDate = formatTimestampDate(this.props.timestamp)
        const url = `${this.props.links.includeurl}/scope/${this.props.scope.id}/availability/day/${currentDate}/conflicts/`
        $.ajax(url, {
            method: 'GET'
        }).done(data => {
            const newProps = {
                conflicts: data.conflicts,
                availabilitylist: data.availabilityList,
                availabilitylistslices: data.availabilityListSlices,
                busyslots: data.busySlotsForAvailabilities,
                maxslots: data.maxSlotsForAvailabilities
            }

            this.setState(Object.assign({}, getStateFromProps(Object.assign({}, this.props, newProps)), {
                stateChanged: false,
                selectedAvailability: null
            }))
        }).fail(err => {
            console.log('refreshData error', err)
        })
    }

    onSaveUpdates(stateParam) {

        const state = stateParam ? stateParam : this.state
        const sendData = state.availabilitylist.map(availability => {
            const sendAvailability = Object.assign({}, availability)
            if (availability.tempId) {
                delete sendAvailability.tempId
            }

            return sendAvailability
        }).map(cleanupAvailabilityForSave)

        console.log('Saving updates', sendData)

        $.ajax(`${this.props.links.includeurl}/availability/`, {
            method: 'POST',
            data: JSON.stringify(sendData)
        }).done((success) => {
            console.log('save success', success)
            this.setState({
                lastSave: new Date().getTime()
            })
            this.refreshData()
        }).fail((err) => {
            if (err.status === 404) {
                console.log('404 error, ignored')
                this.refreshData()
            } else {
                console.log('save error', err)
            }
        })
    }


    onRevertUpdates() {
        this.setState(getInitialState(this.props))
    }

    onDeleteAvailability(availability) {
        console.log('Deleting', availability)
        const ok = confirm('Soll diese Öffnungszeit wirklich gelöscht werden?')
        const id = availability.id
        if (ok) {
            $.ajax(`${this.props.links.includeurl}/availability/${id}`, {
                method: 'DELETE'
            }).done(() => {
                this.setState(Object.assign({}, deleteAvailabilityInState(this.state, availability), {
                    selectedAvailability: null
                }), () => {
                    //after removing the deleted entry, sav the updated list again.
                    this.onSaveUpdates()
                })
            }).fail(err => {
                console.log('delete error', err)
            })
        }
    }

    onCopyAvailability(availability) {
        const start = formatTimestampDate(availability.startDate)
        const end = formatTimestampDate(availability.endDate)

        const copyAvailability = Object.assign({}, availability, {
            tempId: tempId(),
            id: null,
            description: `Kopie von "${start} - ${end}"`
        })
        this.setState(Object.assign(
            {},
            mergeAvailabilityListIntoState(this.state, [copyAvailability]),
            { selectedAvailability: copyAvailability, stateChanged: true }
        ))
    }

    onCreateExceptionForAvailability(availability) {
        const validationResult = validate(availability, this.props)
        if (false === validationResult.valid) {
            this.setState({ errors: validationResult.errors })
            this.handleFocus(this.errorElement);
        }

        const selectedDay = moment(this.props.timestamp, 'X').startOf('day')
        const yesterday = selectedDay.clone().subtract(1, 'days')
        const tomorrow = selectedDay.clone().add(1, 'days')

        const pastAvailability = Object.assign({}, availability, {
            endDate: parseInt(yesterday.format('X'), 10)
        })

        const exceptionAvailability = Object.assign({}, availability, {
            startDate: parseInt(selectedDay.format('X'), 10),
            endDate: parseInt(selectedDay.format('X'), 10),
            tempId: tempId(),
            id: null,
            description: `Ausnahme für ${formatTimestampDate(this.props.timestamp)}`
        })

        const futureAvailability = Object.assign({}, availability, {
            startDate: parseInt(tomorrow.format('X'), 10),
            tempId: tempId(),
            id: null
        })

        this.setState(Object.assign(
            {},
            mergeAvailabilityListIntoState(this.state, [
                pastAvailability,
                exceptionAvailability,
                futureAvailability
            ]),
            { selectedAvailability: exceptionAvailability, stateChanged: true }
        ))
    }

    onEditAvailabilityInFuture(availability) {
        const validationResult = validate(availability, this.props)
        if (false === validationResult.valid) {
            this.setState({ errors: validationResult.errors })
            this.handleFocus(this.errorElement);
        }

        const selectedDay = moment(this.props.timestamp, 'X').startOf('day')
        const yesterday = selectedDay.clone().subtract(1, 'days')

        const pastAvailability = Object.assign({}, availability, {
            endDate: parseInt(yesterday.format('X'), 10)
        })

        const futureAvailability = Object.assign({}, availability, {
            startDate: parseInt(selectedDay.format('X'), 10),
            tempId: tempId(),
            id: null,
            description: `Änderung ab ${formatTimestampDate(this.props.timestamp)}`
        })

        this.setState(Object.assign(
            {},
            mergeAvailabilityListIntoState(this.state, [
                pastAvailability,
                futureAvailability
            ]),
            { selectedAvailability: futureAvailability, stateChanged: true }
        ))
    }

    onNewAvailability() {
        let state = {};
        const newAvailability = getNewAvailability(this.props.timestamp, tempId(), this.props.scope)
        state = Object.assign(
            state, 
            updateAvailabilityInState(this.state, newAvailability), 
            { selectedAvailability: newAvailability, stateChanged: true }
        );
        this.setState(state);
        $('body').scrollTop(0);
    }

    onTabSelect(tab) {
        this.setState({ selectedTab: tab.component });
    }

    onConflictedIdSelect(id) {
        const availability = this.state.availabilitylist.filter(availability => availability.id === id)[0]

        if (availability) {
            this.setState({ selectedAvailability: availability })
        }
    }

    renderTimeTable() {
        const onSelect = data => {
            this.setState({
                selectedAvailability: data,
                formTitle: null
            })
        }

        const onDelete = data => {
            this.onDeleteAvailability(data)
        }

        const selectedDaysAvailabilities = this.state.availabilitylist.filter(availability => {
            const start = moment(availability.startDate, 'X')
            const end = moment(availability.endDate, 'X')
            const selectedDay = moment(this.props.timestamp, 'X').startOf('day')

            return start.isSameOrBefore(selectedDay) && end.isSameOrAfter(selectedDay)
        })

        const ViewComponent = this.state.selectedTab == 'graph' ? GraphView : TableView;

        return <ViewComponent
            timestamp={this.props.timestamp}
            conflicts={this.state.conflicts}
            availabilities={selectedDaysAvailabilities}
            availabilityListSlices={this.state.availabilitylistslices}
            maxWorkstationCount={this.props.maxworkstationcount}
            links={this.props.links}
            onSelect={onSelect}
            onDelete={onDelete}
        />
    }

    handleFocus(element) {
        if (element) {
            element.scrollIntoView()
        }
    }

    handleChange(data) {
        if (data.__modified) {
            this.setState(Object.assign({}, updateAvailabilityInState(this.state, data), {
                selectedAvailability: data
            }));
        }
    }

    renderAvailabilityAccordion() {
        const onSelect = data => {
            this.setState({
                selectedAvailability: data
            })
        }
        const onCopy = data => {
            this.onCopyAvailability(data)
        }

        const onException = data => {
            this.onCreateExceptionForAvailability(data)
        }

        const onEditInFuture = data => {
            this.onEditAvailabilityInFuture(data)
        }

        const onDelete = data => {
            this.onDeleteAvailability(data)
        }

        const onPublish = data => {
            this.onPublishAvailability(data)
        }

        const onNew = data => {
            this.onNewAvailability(data)
        }

        const handleChange = (data) => {
            this.handleChange(data)
        }

        return <AccordionLayout 
            availabilities={this.state.availabilitylist}
            data={this.state.selectedAvailability ? this.state.selectedAvailability : null}
            today={this.state.today}
            timestamp={this.props.timestamp}
            title={this.state.formTitle}
            onSelect={onSelect}
            onPublish={onPublish}
            onDelete={onDelete}
            onNew={onNew}
            onAbort={this.onRevertUpdates.bind(this)}
            onCopy={onCopy}
            onException={onException}
            onEditInFuture={onEditInFuture}
            handleFocus={this.handleFocus.bind(this)}
            handleChange={handleChange}
            stateChanged={this.state.stateChanged}
        />
    }

    renderSaveBar() {
        if (this.state.lastSave) {
            return <SaveBar lastSave={this.state.lastSave} />
        }
    }

    render() {
        return (
            <PageLayout
                tabs={<TabsBar selected={this.state.selectedTab} tabs={this.props.tabs} onSelect={this.onTabSelect.bind(this)} />}
                timeTable={this.renderTimeTable()}
                saveBar={this.renderSaveBar()}
                accordion={this.renderAvailabilityAccordion()}
                conflicts={<Conflicts conflicts={this.state.conflicts} onSelect={this.onConflictedIdSelect.bind(this)} />}
            />
        )
    }
}

AvailabilityPage.propTypes = {
    maxworkstationcount: PropTypes.number,
    timestamp: PropTypes.number,
    scope: PropTypes.object,
    links: PropTypes.object,
    tabs: PropTypes.array
}

export default AvailabilityPage
