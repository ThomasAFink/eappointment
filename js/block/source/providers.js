import React, { Component, PropTypes } from 'react'
import $ from "jquery"
import * as Inputs from '../../lib/inputs'
import { getEntity } from '../../lib/schema'

const renderProvider = (provider, index, onChange, onDeleteClick, labels, descriptions, source) => {
    const formName = `providers[${index}]`

    return (
        <tr className="provider-item">
            <td className="provider-item__id" width="12%">
                <Inputs.Text
                    name={`${formName}[id]`}
                    placeholder={labels.id}
                    value={provider.id}
                    attributes={{ "readOnly": "1" }}
                />
            </td>
            <td className="provider-item__name" width="28%">
                <Inputs.Text
                    name={`${formName}[name]`}
                    placeholder={labels.name}
                    value={provider.name}
                    onChange={onChange}
                />
            </td>
            <td className="provider-item__link">
                <Inputs.FormGroup>
                    <Inputs.Label
                        children={`${labels.url}`}
                    />
                    <Inputs.Controls>
                        <Inputs.Text
                            name={`${formName}[link]`}
                            placeholder={labels.url}
                            value={provider.link}
                            onChange={onChange}
                        />
                    </Inputs.Controls>
                </Inputs.FormGroup>
                <Inputs.FormGroup>
                    <Inputs.Label
                        children={labels.street}
                    />
                    <Inputs.Controls>
                        <Inputs.Text
                            name={`${formName}[contact][street]`}
                            placeholder={labels.street}
                            value={provider.contact.street}
                            onChange={onChange}
                        />
                    </Inputs.Controls>
                </Inputs.FormGroup>
                <Inputs.FormGroup>
                    <Inputs.Label
                        children={labels.streetNumber}
                    />
                    <Inputs.Controls>
                        <Inputs.Text
                            name={`${formName}[contact][streetNumber]`}
                            placeholder={labels.streetNumber}
                            value={provider.contact.streetNumber}
                            onChange={onChange}
                        />
                    </Inputs.Controls>
                </Inputs.FormGroup>
                <Inputs.FormGroup>
                    <Inputs.Label
                        children={labels.postalCode}
                    />
                    <Inputs.Controls>
                        <Inputs.Text
                            name={`${formName}[contact][postalCode]`}
                            placeholder={labels.postalCode}
                            value={provider.contact.postalCode}
                            onChange={onChange}
                        />
                    </Inputs.Controls>
                </Inputs.FormGroup>
                <Inputs.FormGroup>
                    <Inputs.Label
                        children={labels.city}
                    />
                    <Inputs.Controls>
                        <Inputs.Text
                            name={`${formName}[contact][city]`}
                            placeholder={labels.city}
                            value={provider.contact.city}
                            onChange={onChange}
                        />
                    </Inputs.Controls>
                </Inputs.FormGroup>
                <Inputs.FormGroup>
                    <Inputs.Label
                        children={labels.data}
                    />
                    <Inputs.Controls>
                        <Inputs.Textarea
                            name={`${formName}[data]`}
                            value={(provider.data) ? JSON.stringify(provider.data) : ''}
                            placeholder="{}"
                            onChange={onChange}
                        />
                        <Inputs.Description
                            children={descriptions.data}
                        />
                    </Inputs.Controls>
                </Inputs.FormGroup>
                <Inputs.Hidden
                    name={`${formName}[source]`}
                    value={source}
                />
            </td>
            <td className="provider-item__delete">
                <label className="checkboxdeselect provider__delete-button">
                    <input type="checkbox" checked={true} onClick={() => onDeleteClick(index)} /><span></span>
                </label>
            </td>
        </tr >
    )
}

class ProvidersView extends Component {
    constructor(props) {
        super(props)
    }

    getNextId() {
        let nextId = Number(this.props.source.providers.length ? this.props.source.providers[this.props.source.providers.length - 1].id : 0) + 1
        return nextId;
    }

    getProvidersWithLabels(onChange, onDeleteClick) {
        return this.props.source.providers.map((provider, index) => renderProvider(provider, index, onChange, onDeleteClick, this.props.labelsproviders, this.props.descriptions, this.props.source.source))
    }

    hideDeleteButton() {
        $('.provider-item').each((index, item) => {
            if ($(item).find('.provider-item__id input').val()) {
                $(item).find('.provider__delete-button').css("visibility", "hidden");
            }
        })
    }

    componentDidMount() {
        console.log("mounted provider component")
        this.hideDeleteButton()
    }

    componentDidUpdate() {
        //console.log("updated provider component")
    }

    componentWillReceiveProps(nextProps) {
        // You don't have to do this check first, but it can help prevent an unneeded render
        if (nextProps.source.source !== this.props.source) {
            //console.log("props changed", nextProps)
        }
    }

    render() {
        const onNewClick = ev => {
            ev.preventDefault()
            getEntity('provider').then((entity) => {
                entity.id = this.getNextId()
                entity.source = this.props.source.source
                this.props.addNewHandler('providers', [entity])
            })
        }

        const onDeleteClick = index => {
            this.props.deleteHandler('providers', index)
        }

        const onChange = (field, value) => {
            this.props.changeHandler(field, value)
        }

        return (
            <div className="department-providers__list">
                <table className="table--base">
                    <thead>
                        <tr>
                            <th>LfdNr.</th>
                            <th>Bezeichnung</th>
                            <th>Link und weitere Daten</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        {this.getProvidersWithLabels(onChange, onDeleteClick)}
                        <tr>
                            <td colSpan="4">
                                <button className="button-default" onClick={onNewClick}>Neuer Dienstleister</button>
                                <Inputs.Description
                                    children={this.props.descriptions.delete}
                                />
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        )
    }
}

ProvidersView.propTypes = {
    labelsproviders: PropTypes.array.isRequired,
    descriptions: PropTypes.array.isRequired,
    source: PropTypes.array.isRequired,
    changeHandler: PropTypes.changeHandler,
    addNewHandler: PropTypes.addNewHandler,
    deleteHandler: PropTypes.deleteHandler
}

export default ProvidersView
