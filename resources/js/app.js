import Vue from 'vue'
import router from "./router";
import Index from "./components/Index";

Vue.config.productionTip = false
require('./bootstrap');

const app = new Vue({
    el: '#app',

    components:{
        Index,
    },

    router
})
