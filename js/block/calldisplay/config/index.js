import React, { Component } from 'react'
import PropTypes from 'prop-types'


import * as Inputs from '../../../lib/inputs'
const { FormGroup, Label, Controls, Select } = Inputs

const readPropsCluster = cluster => {
    const { name, id } = cluster

    return {
        type: 'cluster',
        id,
        name
    }
}

const readPropsScope = scope => {
    const { shortName, contact, id } = scope
    return {
        type: 'scope',
        id,
        shortName,
        contact
    }
}

class CallDisplayConfigView extends Component {
    constructor(props) {
        super(props)

        //console.log('CallDisplayConfigView::constructor', props)

        this.state = {
            selectedItems: [],
            departments: props.departments.map(department => {
                const { name, id, scopes = [], clusters = [] } = department
                return {
                    name,
                    id,
                    scopes: scopes.map(readPropsScope),
                    clusters: clusters.map(readPropsCluster)
                }
            }),
            queueStatus: 'all',
            template: 'defaultplatz',
            generatedUrl: ""
        }
    }

    buildUrl() {
        const baseUrl = this.props.config.calldisplay.baseUrl

        const collections = this.state.selectedItems.reduce((carry, current) => {
            if (current.type === "cluster") {
                carry.clusterlist.push(current.id)
            } else if (current.type === "scope") {
                carry.scopelist.push(current.id)
            }

            return carry
        }, {
            scopelist: [],
            clusterlist: []
        })

        let parameters = []

        if (collections.scopelist.length > 0) {
            parameters.push(`collections[scopelist]=${collections.scopelist.join(",")}`)
        }

        if (collections.clusterlist.length > 0) {
            parameters.push(`collections[clusterlist]=${collections.clusterlist.join(",")}`)
        }

        if (this.state.queueStatus !== 'all') {
            parameters.push(`queue[status]=${this.state.queueStatus}`)
        }

        if (this.state.template !== 'default') {
            parameters.push(`template=${this.state.template}`)
        }

        return `${baseUrl}?${parameters.join('&')}`
    }

    renderCheckbox(enabled, onShowChange, label) {
        const onChange = () => onShowChange(!enabled)

        return (
            <Inputs.Checkbox checked={enabled} {...{ onChange, label }} />
        )
    }

    showItem(item) {
        const items = this.state.selectedItems.filter(i => i.id !== item.id || (i.id == item.id && i.type !== item.type))
        const newItem = Object.assign({}, item)
        items.push(newItem)
        this.setState({ selectedItems: items })
    }

    hideItem(item) {
        const items = this.state.selectedItems.filter(i => i.id !== item.id || (i.id == item.id && i.type !== item.type))
        this.setState({ selectedItems: items })
    }

    renderItem(item) {
        const onChange = show => {
            if (show) {
                this.showItem(item)
            } else {
                this.hideItem(item)
            }
        }

        const text = `${item.contact ? item.contact.name : item.name} ${item.shortName ? item.shortName : ""}`
        const prefix = item.type === 'cluster' ? 'Cluster: ' : ''

        const itemEnabled = this.state.selectedItems.reduce((carry, current) => {
            return carry || (current.id === item.id && current.type === item.type)
        }, false)
        return (
            <div key={item.id} className="form-check ticketprinter-config__item">
                {this.renderCheckbox(itemEnabled, onChange, prefix + text)}
            </div>
        )
    }

    renderScopes(scopes) {
        if (scopes.length > 0) {
            return (
                <fieldset>
                    <legend className="label">Standorte</legend>
                    {scopes.map(this.renderItem.bind(this))}
                </fieldset>
            )
        }
    }

    renderClusters(clusters) {
        if (clusters.length > 0) {
            return (
                <fieldset>
                    <legend className="label">Standort­gruppe</legend>
                    {clusters.map(this.renderItem.bind(this))}
                </fieldset>
            )
        }
    }

    renderDepartment(department) {
        return (
            <div key={department.id}>
                <h2 className="block__heading">{department.name}</h2>
                {this.renderScopes(department.scopes)}
                {this.renderClusters(department.clusters)}
            </div>
        )
    }

    render() {
        const generatedUrl = this.buildUrl()

        const onQueueStatusChange = (_, value) => {
            this.setState({
                queueStatus: value
            })
        }

        const onTemplateStatusChange = (_, value) => {
            this.setState({
                template: value
            })
        }

        return (
            <form className="form--base form-group calldisplay-config">
                {this.state.departments.map(this.renderDepartment.bind(this))}
                <FormGroup>
                    <Label 
                        attributes={{ "htmlFor": "visibleCalls" }} 
                        value="Angezeigte Aufrufe">
                    </Label>
                    <Controls>
                        <Select
                            options={[{ name: 'Alle', value: 'all' }, { name: "Nur Abholer", value: 'pickup' }, { name: "Spontan- und Terminkunden", value: 'called' }]}
                            value={this.state.queueStatus}
                            attributes={{ "id": "visibleCalls" }}
                            onChange={onQueueStatusChange} />
                    </Controls>
                </FormGroup>
                <FormGroup>
                    <Label attributes={{ "htmlFor": "calldisplayLayout" }} value="Layout"></Label>
                    <Controls>
                        <Select
                            attributes={{ "id": "calldisplayLayout" }}
                            options={[
                                { name: 'Uhrzeit, 6-12 Aufrufe | Platz', value: 'defaultplatz' },
                                { name: 'Uhrzeit, 6-12 Aufrufe | Raum', value: 'defaultraum' },
                                { name: 'Uhrzeit, 6 Aufrufe | Platz', value: 'clock5platz' },
                                { name: 'Uhrzeit, Anzahl Wartende, 6-12 Aufrufe | Platz', value: 'clocknrplatz' },
                                { name: 'Uhrzeit, Anzahl Wartende, 6-12 Aufrufe | Raum', value: 'clocknrraum' },
                                { name: 'Uhrzeit, Anzahl Wartende, Wartezeit, 6-12 Aufrufe | Platz', value: 'clocknrwaitplatz' },
                                { name: 'Uhrzeit, Anzahl Wartende, Wartezeit, 6-12 Aufrufe | Raum', value: 'clocknrwaitraum' },
                                { name: '6-18 Aufrufe | Platz', value: 'raw18platz' }
                            ]}
                            value={this.state.template}
                            onChange={onTemplateStatusChange} />
                    </Controls>
                </FormGroup>
                <FormGroup>
                    <Label attributes={{ "htmlFor": "calldisplayUrl" }} value="URL"></Label>
                    <Controls>
                        <Inputs.Text
                            value={generatedUrl}
                            attributes={{ readOnly: true, id: "calldisplayUrl" }} />
                    </Controls>
                </FormGroup>
                <div className="form-actions">
                    <a href={generatedUrl} target="_blank" rel="noopener noreferrer" className="button button-submit"><i className="fas fa-external-link-alt" aria-hidden="true"></i> Aktuelle Konfiguration in einem neuen Fenster öffnen</a>
                </div>
            </form>
        )
    }
}

CallDisplayConfigView.propTypes = {
    departments: PropTypes.array,
    organisation: PropTypes.object,
    config: PropTypes.shape({
        calldisplay: PropTypes.object
    })
}

export default CallDisplayConfigView
