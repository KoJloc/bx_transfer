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
    markedPeople: [],
}

const getters = {
    person: () => state.person,
    people: () => state.people,
    myOptions: () => state.myOptions,
    myOptionsOnlyActive: () => state.myOptionsOnlyActive,
    markedPeople: () => state.markedPeople,

}

const actions = {

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

    storePeople({state, commit, dispatch}, myOptions) {
        axios.post('/api/people/store',)
            .catch(err => {
                console.log(err)
            })
    },
    //
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
    //
    // getLead({state, commit, dispatch}) {
    //     axios.post('/api/people/lead')
    //         .then(res => {
    //
    //         })
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


}

const mutations = {
    setPerson: (state, person) => state.person = person,
    setPeople: (state, people) => state.people = people,
    myOptions: (state, myOptions) => state.myOptions = myOptions,
    myOptionsOnlyActive: (state, myOptionsOnlyActive) => state.myOptionsOnlyActive = myOptionsOnlyActive,
    markedPeople: (state, markedPeople) => state.markedPeople = markedPeople,
}

export default {
    state,
    mutations,
    getters,
    actions,
}
