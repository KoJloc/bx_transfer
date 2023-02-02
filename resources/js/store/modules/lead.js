import router from '../../router'

const state = {
    lead: {
        ID: '',
        ASSIGNED_BY_ID: '',
        CONTACT_ID: '',
        LEAD_SUMMARY: '',
        DATE_CREATE: '',
        NAME: '',
        LAST_NAME: '',
        SECOND_NAME: '',
        PHONE: '',
},
    leadsById: [],
    // body: '',
    // people: '',
    // myOptions: [],
    // myOptionsOnlyActive: [],
    // markedPeople: [],
    // loh: [],
    // loh1: [],
}

const getters = {
    leadById: () => state.leadsById,
    // person: () => state.person,
    // people: () => state.people,
    // myOptions: () => state.myOptions,
    // myOptionsOnlyActive: () => state.myOptionsOnlyActive,
    // markedPeople: () => state.markedPeople,

}

const actions = {

    getLeadById({state, commit, dispatch}) {
        axios.post('/api/people/lead')
            .then(res => {
                commit('setLeadById', res.data.leadsById)
            })
            .catch(
                (error) => {
                    console.log(error.message)
                })
    },

    // getPeople({state, commit, dispatch}) {
    //     axios.post('/api/people')
    //         .then(res => {
    //             commit('setPeople', res.data.people)
    //             commit('myOptions', res.data.peopleMultiSelect)
    //             commit('myOptionsOnlyActive', res.data.activePeopleMultiSelect)
    //             console.log(res.data)
    //         })
    //         .catch(
    //             (error) => {
    //                 console.log(error.message)
    //             })
    // },
    // getPerson({state, commit, dispatch}, ID) {
    //     axios.post(`/api/people/${ID}`)
    //         .then(res => {
    //             commit('setPerson', res.data)
    //             // console.log(res.data)
    //         })
    //         .catch(
    //             (error) => {
    //                 console.log(error)
    //             })
    // },
    //
    // deletePerson({state, commit, dispatch}, ID) {
    //     axios.delete(`/api/people/${ID}`,)
    //         .then(res => {
    //             dispatch('getPeople')
    //         })
    //         .catch(err => {
    //             console.log(err)
    //         })
    // },
    //
    // updatePerson({}, data) {
    //     axios.patch(`/api/people/${data.ID}`,
    //         {
    //             SECOND_NAME: data.SECOND_NAME,
    //             LAST_NAME: data.LAST_NAME,
    //             ASSIGNED_BY_ID: data.ASSIGNED_BY_ID,
    //             LEAD_ID: data.LEAD_ID,
    //         })
    //         .then(res => {
    //             router.push({ SECOND_NAME:'person.show', params: {id: data.ID }})
    //         })
    //         .catch(
    //             (error) => {
    //                 console.log(error)
    //             })
    // },
    //
    // storePerson({}, data){
    //     axios.post('/api/people/lead', {
    //         ID: data.id,
    //     })
    //         .then(res => {
    //             console.log(res)
    //         })
    //         .catch(err => {
    //             console.log(err)
    //         })
    // },
}

const mutations = {
    setLeadById:(state, leadById)=>state.leadsById = leadById,

    // setPeople:(state, people)=>state.people = people,
    // myOptions:(state, myOptions)=>state.myOptions = myOptions,
    // myOptionsOnlyActive:(state, myOptionsOnlyActive) =>state.myOptionsOnlyActive = myOptionsOnlyActive,
    // markedPeople:(state, markedPeople)=>state.markedPeople = markedPeople,
}

export default {
    state,
    mutations,
    getters,
    actions,
}
