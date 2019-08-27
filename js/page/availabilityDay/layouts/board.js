import React, { PropTypes } from 'react'

const titleAside = (title) => {
    if (title) {
        return (<div className="left aside">{title}</div>)
    }
}

const headerRight = (header) => {
    if (header) {
        return (<div className="right header_right">{header}</div>)
    }
}

const Board = (props) => {
    const className = `board ${props.className}`

    return (
        <section className={className}>
            {props.title ?
            <div className="board__header header">
                <h2 className="board__heading title">{props.title}</h2>
            </div> : null }
            {props.titleAside || props.headerRight ?
            <div className="board__actions">
                {titleAside(props.titleAside)}
                {headerRight(props.headerRight)}
            </div> : null }
            <div className="board__body body">
                {props.body}
            </div>
            {props.footer ?
             <div className="board__footer footer">
                {props.footer}
            </div> : null }
        </section>
    )
}

Board.propTypes = {
    className: PropTypes.string,
    title: PropTypes.node.isRequired,
    titleAside: PropTypes.node,
    headerRight: PropTypes.node,
    body: PropTypes.node.isRequired,
    footer: PropTypes.node
}

export default Board
