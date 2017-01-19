// --------------------------------------------------------
// ZMS Admin behavior
// --------------------------------------------------------

import 'babel-polyfill';

// Import base libs
import window from "window";
import $ from "jquery";
import moment from 'moment'
import 'moment/locale/de';

// Import Views
import FormView from "./element/form";
import PickupKeyboardHandheldView from "./block/pickup-keyboard-handheld";
import EmergencyView from './block/emergency'
import DepartmentLinksView from './block/department/links'

import AvailabilityDayPage from './page/availabilityDay'
import bindReact from './lib/bindReact.js'
import { getDataAttributes } from './lib/utils'

// Bind jQuery on $ for testing
window.$ = $;

moment.locale('de')

// Init Views
$('form').each(function() { new FormView(this);});
$('.pickup-keyboard-handheld').each(function() { new PickupKeyboardHandheldView(this);});
$('.emergency').each(function() {
    new EmergencyView(this, getDataAttributes(this));
})

$('.department-links').each(function() {
    new DepartmentLinksView(this, getDataAttributes(this));
})

// Say hello
console.log("Welcome to the ZMS admin interface...");


// hook up react components
bindReact('.availabilityDayRoot', AvailabilityDayPage)
