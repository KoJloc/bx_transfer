import router from '../../router'

const state = {
    person: null,
    people: null,
}

const getters = {
    person: () => state.person,
    people: () => state.people,
}

const actions = {

    getPerson({state, commit, dispatch}, id) {
        axios.post(`/api/people/${id}`)
            .then(res => {
                commit('setPerson', res.data.data)
            })
            .catch(
                (error) => {
                    console.log(error)
                })
    },

    getPeople({state, commit, dispatch}) {
        axios.post('/api/people')
            .then(res => {
                commit('setPeople', res.data.data)
            })
            .catch(
                (error) => {
                console.log(error)
            })
    },

    deletePerson({state, commit, dispatch}, id) {
        axios.delete(`/api/people/${id}`,)
            .then(res => {
                dispatch('getPeople')
            })
            .catch(err => {
                console.log(err)
            })
    },

    updatePerson({}, data) {
        axios.patch(`/api/people/${data.id}`,
            {
                name: data.name,
                age: data.age,
                job: data.job
            })
            .then(res => {
                router.push({ name:'person.show', params: {id: data.id }})
            })
            .catch(
                (error) => {
                    console.log(error)
                })
    },

    storePerson({}, data){
        axios.post('/api/people/create', {
            name: data.name,
            age: data.age,
            job: data.job
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
    setPerson(state, person) {
        state.person = person
    },
    setPeople(state, people) {
        state.people = people
    }
}

export default {
    state,
    mutations,
    getters,
    actions,
}
