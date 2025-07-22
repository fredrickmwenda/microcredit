window._ = require('lodash');
window.axios = require('axios');

window.axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';
const token = document.head.querySelector('meta[name="csrf-token"]');
if (token) {
    window.axios.defaults.headers.common['X-CSRF-TOKEN'] = token.content;
} else {
    console.error('CSRF token not found');
}

import Vue from 'vue'; // ✅ Correct import
window.Vue = Vue;

window.toastr = require('toastr');

// Vue plugins
import VueFlatPickr from 'vue-flatpickr-component';
import 'flatpickr/dist/flatpickr.css';

Vue.use(VueFlatPickr);

import vSelect from 'vue-select';
Vue.component('v-select', vSelect);

import Editor from '@tinymce/tinymce-vue';
Vue.component('editor', Editor);
import 'overlayscrollbars/overlayscrollbars.css';
import { OverlayScrollbars } from 'overlayscrollbars';
// jQuery & related plugins
// 2. jQuery & global
import $ from 'jquery';
window.$ = window.jQuery = $;

// 3. Select2
import 'select2/dist/js/select2.min.js';
import 'select2/dist/css/select2.min.css';

import 'jquery-ui/ui/widgets/sortable';


require('popper.js');
require('bootstrap');
// require('admin-lte'); // Optional - enable if needed

require('datatables.net-bs4');

const Swal = require('sweetalert2');
window.Swal = Swal;

import '@fortawesome/fontawesome-free/js/all.js';


// 5. Initialize Select2 when DOM is ready
$(document).ready(function () {
    $('select').select2({ width: '100%' });
});