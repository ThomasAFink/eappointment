import React from 'react'
import PropTypes from 'prop-types'
import moment from 'moment'

const FormButtons = (props) => {
    const { data, onCopy, onExclusion, onEditInFuture, onUpdateSingle, onDelete, selectedDate, hasConflicts, hasSlotCountError } = props
    const disabled = ((data && (! data.id || data.__modified === true)) || hasConflicts || hasSlotCountError);
    return (
        <div className="body">
            <div className="form-actions">
                <button onClick={onDelete}
                    title="Ausgewählte Öffnungszeit löschen"
                    className="button button--destructive button-delete" disabled={disabled}>Löschen</button>
                <button onClick={onCopy}
                    title="Öffnungszeit kopieren und bearbeiten"
                    className="button button--diamond" disabled={disabled}>Kopieren</button>
                <button onClick={onExclusion}
                    title="Ausnahme von dieser Öffnungszeit eintragen"
                    className="button button--diamond" disabled={disabled || data.endDate == selectedDate || selectedDate <= moment().startOf('day').unix()}>Ausnahme</button>
                <button onClick={onEditInFuture}
                    title="Öffnungszeit ab diesem Tag ändern"
                    className="button button--diamond" disabled={disabled || data.startDate == selectedDate}>Ab diesem Tag ändern</button> 
                <button onClick={onUpdateSingle}
                    title="Öffnungszeit aktualisieren"
                    className="button button--diamond" disabled={(data && !data.id) || hasConflicts || hasSlotCountError || props.isCreatingExclusion}>Aktualisieren</button>
            </div>
        </div>
    )
}

FormButtons.propTypes = {
    data: PropTypes.object,
    hasConflicts: PropTypes.bool,
    onCopy: PropTypes.func,
    onExclusion: PropTypes.func,
    onEditInFuture: PropTypes.func,
    onDelete: PropTypes.func,
    onUpdateSingle: PropTypes.func,
    selectedDate: PropTypes.number,
    isCreatingExclusion: PropTypes.bool
}

export default FormButtons
