import Vue from 'vue'
import store from './store'
import router from "./router";
import Index from "./components/Index";
import Select2MultipleControl from 'v-select2-multiple-component';
import Multiselect from 'vue-multiselect';
import DatePicker from 'vue2-datepicker';

Vue.config.productionTip = false
Vue.component('multiselect', Multiselect)
require('./bootstrap');

const app = new Vue({
    el: '#app',

    components:{
        Index,
        Select2MultipleControl,
        Multiselect,
        DatePicker
    },
    router,
    store
})
