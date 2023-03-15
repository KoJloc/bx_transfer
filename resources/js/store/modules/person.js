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

    getEntities({state, commit, dispatch}) {
        axios.post('/api/entities')
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
