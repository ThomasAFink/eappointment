/* global window */
/* global confirm */
/* global alert */
import React, { Component, PropTypes } from 'react'
import $ from 'jquery'
import moment from 'moment'

import AvailabilityForm from './form'
import Conflicts from './conflicts'
import TimeTable from './timetable'
import UpdateBar from './updateBar'

import PageLayout from './layouts/page'

import { getInitialState,
         getStateFromProps,
         getNewAvailability,
         mergeAvailabilityListIntoState,
         updateAvailabilityInState,
         cleanupAvailabilityForSave,
         deleteAvailabilityInState } from "./helpers"

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

    componentWillMount() {
        this.unloadHandler = ev => {
            const confirmMessage = "Es wurden nicht alle Änderungen gespeichert. Diese gehen beim schließen verloren."
                if (this.state.stateChanged) {
                    ev.returnValue = confirmMessage
                    return confirmMessage
                }
        }

        window.addEventListener('beforeunload', this.unloadHandler)
    }

    componentWillUnMount() {
        window.removeEventListener('beforeunload', this.unloadHandler)
    }

    onUpdateAvailability(availability) {
        if (availability.__modified) {
            this.setState(Object.assign({}, updateAvailabilityInState(this.state, availability), {
                selectedAvailability: null
            }))
        } else {
            this.setState({ selectedAvailability: null })
        }
    }

    refreshData() {
        const currentDate = formatTimestampDate(this.props.timestamp)
        const url = `/scope/${this.props.scope.id}/availability/day/${currentDate}/conflicts/`
        $.ajax(url, {
            method:'GET'
        }).done(data => {
            const newProps = {
                conflicts: data.conflicts,
                availabilitylist: data.availabilityList,
                availabilitylistslices: data.availabilityListSlices,
                busyslots: data.busySlotsForAvailabilities,
                maxslots: data.maxSlotsForAvailabilities
            }

            this.setState(Object.assign({}, getStateFromProps(Object.assign({}, this.props, newProps )), {
                stateChanged: false,
                selectedAvailability: null
            }))
        }).fail(err => {
            console.log('refreshData error', err)
        })
    }

    onSaveUpdates() {

        const sendData = this.state.availabilitylist.map(availability => {
            const sendAvailability = Object.assign({}, availability)
            if (availability.tempId) {
                delete sendAvailability.tempId
            }

            return sendAvailability
        }).map(cleanupAvailabilityForSave)

        console.log('Saving updates', sendData)

        $.ajax('/availability/', {
            method: 'POST',
            data: JSON.stringify(sendData)
        }).done((success) => {
            console.log('save success', success)
            this.refreshData()
        }).fail((err) => {
            console.log('save error', err)
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
            $.ajax(`/availability/${id}`, {
                method: 'DELETE'
            }).done(() => {
                this.setState(Object.assign({}, deleteAvailabilityInState(this.state, availability), {
                    selectedAvailability: null
                }))
                this.refreshData()
            }).fail(err => {
                console.log('delete error', err)
            })
        }
    }

    onCopyAvailability(availability) {
        const start = formatTimestampDate(availability.startDate)
        const end = formatTimestampDate(availability.endDate)
        this.setState({
            selectedAvailability: Object.assign({}, availability, {
                tempId: tempId(),
                id: null,
                description: `Kopie von "${start} - ${end}"`
            }),
            formTitle: "Öffnungszeit kopieren"
        })
    }

    onCreateExceptionForAvailability(availability) {
        const today = moment(this.props.timestamp, 'X').startOf('day')

        const yesterday = today.clone().subtract(1, 'days')
        const tomorrow = today.clone().add(1, 'days')

        const start = moment(availability.startDate, 'X')
        const end = moment(availability.endDate, 'X')

        if (start.isAfter(yesterday, 'day')) {
            alert('Beginn der Öffnungszeit muss mind. 1 Tag zurückliegen.')
            return
        }

        if (end.isBefore(tomorrow, 'day')) {
            alert('Ende der Öffnungszeit muss mind. 1 Tag vorausliegen.')
            return
        }

        const pastAvailability = Object.assign({}, availability, {
            endDate: parseInt(yesterday.format('X'), 10)
        })

        const exceptionAvailability = Object.assign({}, availability, {
            startDate: parseInt(today.format('X'), 10),
            endDate: parseInt(today.format('X'), 10),
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
            ] ),
            { selectedAvailability: exceptionAvailability, formTitle: "Ausnahme-Öffnungszeit" }
        ))
    }

    onEditAvailabilityInFuture(availability) {
        const today = moment(this.props.timestamp, 'X').startOf('day')
        const yesterday = today.clone().subtract(1, 'days')

        const start = moment(availability.startDate, 'X')

        if (start.isAfter(yesterday, 'day')) {
            alert('Beginn der Öffnungszeit muss mind. 1 Tag zurückliegen.')
            return
        }

        const pastAvailability = Object.assign({}, availability, {
            endDate: parseInt(yesterday.format('X'), 10)
        })

        const futureAvailability = Object.assign({}, availability, {
            startDate: parseInt(today.format('X'), 10),
            tempId: tempId(),
            id: null,
            description: `Änderung ab ${formatTimestampDate(this.props.timestamp)}`
        })

        this.setState(Object.assign(
            {},
            mergeAvailabilityListIntoState(this.state, [
                pastAvailability,
                futureAvailability
            ] ),
            { selectedAvailability: futureAvailability, formTitle: "Neue Öffnungszeit ab Datum"}
        ))
    }

    onNewAvailability() {
        console.log('new availability')
        const newAvailability = getNewAvailability(this.props.timestamp, tempId(), this.props.scope)

        this.setState(Object.assign({}, {
            selectedAvailability: newAvailability,
            formTitle: "Neue Öffnungszeit" }))
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

        const todaysAvailabilities = this.state.availabilitylist.filter(availability => {
            const start = moment(availability.startDate, 'X')
            const end = moment(availability.endDate, 'X')
            const today = moment(this.props.timestamp, 'X').startOf('day')

            return start.isSameOrBefore(today) && end.isSameOrAfter(today)
        })


        return <TimeTable
                   timestamp={this.props.timestamp}
                   conflicts={this.state.conflicts}
                   availabilities={todaysAvailabilities}
                   availabilityListSlices={this.state.availabilitylistslices}
                   maxWorkstationCount={this.props.maxworkstationcount}
                   links={this.props.links}
                   onSelect={onSelect}
                   onNewAvailability={this.onNewAvailability.bind(this)}
               />
    }

    renderForm() {
        if (this.state.selectedAvailability) {
            return <AvailabilityForm data={this.state.selectedAvailability}
                       title={this.state.formTitle}
                       onSave={this.onUpdateAvailability.bind(this)}
                       onDelete={this.onDeleteAvailability.bind(this)}
                       onCopy={this.onCopyAvailability.bind(this)}
                       onException={this.onCreateExceptionForAvailability.bind(this)}
                       onEditInFuture={this.onEditAvailabilityInFuture.bind(this)}
                   />
        }
    }

    renderUpdateBar() {
        if (this.state.stateChanged) {
            return <UpdateBar onSave={this.onSaveUpdates.bind(this)} onRevert={this.onRevertUpdates.bind(this)}/>
        }
    }

    render() {
        console.log('AvailabilityPage Props', this.props)
        console.log('AvailabilityPage State', this.state)
        return (
            <PageLayout
                timeTable={this.renderTimeTable()}
                updateBar={this.renderUpdateBar()}
                form={this.renderForm()}
                conflicts={<Conflicts conflicts={this.state.conflicts} onSelect={this.onConflictedIdSelect.bind(this)} />}
            />
        )
    }
    }

AvailabilityPage.propTypes = {
    conflicts: PropTypes.array,
    availabilitylist: PropTypes.array,
    availabilitylistslices: PropTypes.array,
    maxworkstationcount: PropTypes.number,
    timestamp: PropTypes.number,
    scope: PropTypes.object,
    links: PropTypes.object
}

export default AvailabilityPage
