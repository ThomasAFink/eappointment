import React from 'react'
import PropTypes from 'prop-types'

import moment from 'moment/min/moment-with-locales';
moment.locale('de')

import Board from '../layouts/board'
import TableBodyLayout from '../layouts/tableBody'
import calendarNavigation from '../widgets/calendarNavigation'
import * as constants from './index.js'

const TableView = (props) => {
    const { onDelete, onSelect, timestamp, availabilities } = props;
    const titleTime = moment(timestamp, 'X').format('dddd, DD.MM.YYYY')
    const TableBody = <TableBodyLayout
        availabilities={availabilities}
        onDelete={onDelete}
        onSelect={onSelect}
    />
    return (
        <Board className="board--light availability-timetable"
            title={titleTime}
            titleAside={calendarNavigation(props.links)}
            headerRight={constants.headerRight(props.links, props.onNewAvailability)}
            headerMiddle={constants.headerMiddle()}
            body={TableBody}
            footer=""
        />
    )
}

TableView.defaultProps = {

}

TableView.propTypes = {
    timestamp: PropTypes.number,
    links: PropTypes.object,
    availabilities: PropTypes.array,
    onNewAvailability: PropTypes.func,
    onDelete: PropTypes.func.isRequired,
    onSelect: PropTypes.func.isRequired
}

export default TableView

