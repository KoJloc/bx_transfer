import Vue from 'vue'
import store from './store'
import router from "./router";
import Index from "./components/Index";
import Select2MultipleControl from 'v-select2-multiple-component';

Vue.config.productionTip = false
require('./bootstrap');

const app = new Vue({
    el: '#app',

    components:{
        Index,
        Select2MultipleControl
    },

    router,
    store
})
