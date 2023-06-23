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
    leadStatusList: [],
    leadTypeList: [],
    dealTypeList: [],
    dealFunnelList: [],
    regionsList: [],
    citiesList: [],
    sourcesList: [],
    salesDepartmentsLeadList: [],
    salesDepartmentsDealList: [],
    salesDepartmentsContactList: [],
}

const getters = {
    person: () => state.person,
    people: () => state.people,
    myOptions: () => state.myOptions,
    myOptionsOnlyActive: () => state.myOptionsOnlyActive,
    markedPeople: () => state.markedPeople,
    leadStatusList: () => state.leadStatusList,
    leadTypeList: () => state.leadTypeList,
    dealTypeList: () => state.dealTypeList,
    dealFunnelList: () => state.dealFunnelList,
    regionsList: () => state.regionsList,
    citiesList: () => state.citiesList,
    sourcesList: () => state.sourcesList,
    departmentsList: () => state.departmentsList,
    salesDepartmentsLeadList: () => state.salesDepartmentsLeadList,
    salesDepartmentsDealList: () => state.salesDepartmentsDealList,
    salesDepartmentsContactList: () => state.salesDepartmentsContactList,
}

const actions = {
    getEntities({state, commit, dispatch}) {
        axios.post('/api/entities')
            .then(res => {
                commit('setPeople', res.data.people)
                commit('myOptions', res.data.peopleMultiSelect)
                commit('myOptionsOnlyActive', res.data.activePeopleMultiSelect)
                commit('leadStatusList', res.data.leadStatusList)
                commit('leadTypeList', res.data.leadTypeList)
                commit('dealTypeList', res.data.dealTypeList)
                commit('dealFunnelList', res.data.dealFunnelList)
                commit('regionsList', res.data.regionsList)
                commit('citiesList', res.data.citiesList)
                commit('sourcesList', res.data.sourcesList)
                commit('salesDepartmentsLeadList', res.data.salesDepartmentsLeadList)
                commit('salesDepartmentsDealList', res.data.salesDepartmentsDealList)
                commit('salesDepartmentsContactList', res.data.salesDepartmentsContactList)
                console.log(res.data)
            })
            .catch(e => console.log(e))
    },
}

const mutations = {
    setPerson: (state, person) => state.person = person,
    setPeople: (state, people) => state.people = people,
    myOptions: (state, myOptions) => state.myOptions = myOptions,
    myOptionsOnlyActive: (state, myOptionsOnlyActive) => state.myOptionsOnlyActive = myOptionsOnlyActive,
    markedPeople: (state, markedPeople) => state.markedPeople = markedPeople,
    leadStatusList: (state, leadStatusList) => state.leadStatusList = leadStatusList,
    leadTypeList: (state, leadTypeList) => state.leadTypeList = leadTypeList,
    dealTypeList: (state, dealTypeList) => state.dealTypeList = dealTypeList,
    dealFunnelList: (state, dealFunnelList) => state.dealFunnelList = dealFunnelList,
    regionsList: (state, regionsList) => state.regionsList = regionsList,
    citiesList: (state, citiesList) => state.citiesList = citiesList,
    sourcesList: (state, sourcesList) => state.sourcesList = sourcesList,
    salesDepartmentsLeadList: (state, salesDepartmentsLeadList) => state.salesDepartmentsLeadList = salesDepartmentsLeadList,
    salesDepartmentsDealList: (state, salesDepartmentsDealList) => state.salesDepartmentsDealList = salesDepartmentsDealList,
    salesDepartmentsContactList: (state, salesDepartmentsContactList) => state.salesDepartmentsContactList = salesDepartmentsContactList,
}

export default {
    state,
    mutations,
    getters,
    actions,
}
