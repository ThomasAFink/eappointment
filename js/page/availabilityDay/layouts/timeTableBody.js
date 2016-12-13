import React, { PropTypes } from 'react'

const hours = (() => {
    const hours = []
    for (let i = 0; i < 24; i = i + 1) {
        hours.push(i)
    }
    return hours
})()

const background = () => {
    return <div className="background-level">
        {hours.map(i => (
            <div key={i} className="time-level_item" style={{ left: `${i}em` }}>
                <div className="time-level_item--top">
                    <span className="text">{i}:00</span>
                </div>
                <div className="time-level_item--bottom">
                    <span className="text">{i}:00</span>
                </div>
            </div>
         ))}
    </div>
}

const conflicts = (showConflicts, conflicts) => {
    if ( showConflicts) {
        return (
            <div className="bars conflict-level">{conflicts}</div>
        )
    }
}

const refScroll = (element) => {
    element.scrollLeft += 470
}

const TimeTableBodyLayout = (props) => {
    return (
        <div className="grid">
            <div className="grid__item one-tenth">
                <div className="availability-timetable_legend">
                  { props.showConflicts ? <div className="legend__item legend_conflict">Konflikte</div> : null }
                    <div className="legend__item legend_numberofappointment">Freie Slots</div>
                    <div className="legend__item legend_appointment">Termin&shy;kunden</div>
                    <div className="legend__item legend_opening">Spontan&shy;kunden</div>
                </div>
            </div>
            <div className="grid__item nine-tenths">
                <div className="availability-timetable_container" ref={refScroll} style={{fontSize: "70px"}} >
                    <div className="inner">
                        {background()}
                        {conflicts(props.showConflicts, props.conflicts)}
                        <div className="bars numberofappointment-level">{props.numberOfAppointments}</div>
                        <div className="bars appointment-level">{props.appointments}</div>
                        <div className="bars opening-level">{props.openings}</div>
                    </div>
                </div>
            </div>
        </div>
    )
}

TimeTableBodyLayout.propTypes = {
    conflicts: PropTypes.node,
    numberOfAppointments: PropTypes.node,
    appointments: PropTypes.node,
    openings: PropTypes.node,
    showConflicts: PropTypes.bool
}

export default TimeTableBodyLayout
