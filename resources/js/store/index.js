import Vue from 'vue'
import Vuex from 'vuex'
import Person from './modules/person'
import Lead from './modules/lead'

Vue.use(Vuex)

export default new Vuex.Store({
    modules:{
        Person,
        Lead,
    }
})
