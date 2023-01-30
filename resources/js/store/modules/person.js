import router from '../../router'

const state = {
    person: {
        ID: '',
        NAME: '',
        SECOND_NAME: '',
        LAST_NAME: '',
    },
    body: '',
    people: '',
    myOptions: [],
    myOptionsOnlyActive: [],
    markedPeopleBody: [],
}

const getters = {
    person: () => state.person,
    people: () => state.people,
    myOptions: () => state.myOptions,
    myOptionsOnlyActive: () => state.myOptionsOnlyActive,
    markedPeopleBody: () => state.markedPeopleBody,

}

const actions = {

    getPerson({state, commit, dispatch}, ID) {
        axios.post(`/api/people/${ID}`)
            .then(res => {
                commit('setPerson', res.data)
                // console.log(res.data)
            })
            .catch(
                (error) => {
                    console.log(error)
                })
    },

    getPeople({state, commit, dispatch}) {
        axios.post('/api/people')
            .then(res => {
                commit('setPeople', res.data.people)
                commit('myOptions', res.data.peopleMultiSelect)
                commit('myOptionsOnlyActive', res.data.activePeopleMultiSelect)
                console.log(res.data)
            })
            .catch(
                (error) => {
                console.log(error)
            })
    },
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

    storePerson({}, data){
        axios.post('/api/people/create', {
            SECOND_NAME: data.SECOND_NAME,
            LAST_NAME: data.LAST_NAME,
            ASSIGNED_BY_ID: data.job,
            LEAD_ID: data.LEAD_ID,
        })
            .then(res => {
                router.push({name:"person.index"})
            })
            .catch(err => {
                console.log(err)
            })
    },
}

const mutations = {
    setPerson:(state, person)=>state.person = person,
    setPeople:(state, people)=>state.people = people,
    myOptions:(state, myOptions)=>state.myOptions = myOptions,
    myOptionsOnlyActive:(state, myOptionsOnlyActive) =>state.myOptionsOnlyActive = myOptionsOnlyActive,
    markedPeopleBody:(state, markedPeopleBody)=>state.markedPeopleBody = markedPeopleBody,
}

export default {
    state,
    mutations,
    getters,
    actions,
}
