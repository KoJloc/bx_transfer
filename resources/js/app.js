import Vue from 'vue'
import store from './store'
import router from "./router";
import Index from "./components/Index";
import Multiselect from 'vue-multiselect';
import DatePicker from 'vue2-datepicker';
import Toast from "vue-toastification";
import "vue-toastification/dist/index.css";

Vue.config.productionTip = false
Vue.component('multiselect', Multiselect)
Vue.use(Toast)
require('./bootstrap');

const app = new Vue({
    el: '#app',

    components:{
        Index,
        Multiselect,
        DatePicker,
        Toast,
    },
    router,
    store
})
