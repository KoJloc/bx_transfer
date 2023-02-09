<template>
    <div class="container">
        <div class="row">
            <div class="col">
                <select2-multiple-control id="DepartmentSelect" v-model="myOptions.id"
                                          :options="myOptions" @select="DepartmentSelect($event)"/>
            </div>
            <div class="col">
                <select2-multiple-control id="onlyActiveDepartmentSelect" v-model="myOptionsOnlyActive.id"
                                          :options="myOptionsOnlyActive" @select="onlyActiveDepartmentSelect($event)"/>
            </div>
            <div>
                <button @click.prevent="storeSettings" type="submit" class="btn btn-primary mt-3">
                    Подтвердить выбор
                </button>
            </div>
        </div>
    </div>
</template>

<script>
import Select2MultipleControl from 'v-select2-multiple-component';
import {mapGetters} from "vuex";

export default {
    name: "Table",

    data() {
        return {
            myValue: '',
            Departments: [],
            onlyActiveDepartments: [],
        }
    },

    components: {
        Select2MultipleControl
    },

    mounted() {
        this.$store.dispatch('getPeople')
    },

    methods: {
        onlyActiveDepartmentSelect(id) {
            for (let i = 0; i < this.onlyActiveDepartments.length; i++) {
                if (id.id === this.onlyActiveDepartments[i]) {
                    this.onlyActiveDepartments.splice(i, 1)
                    // console.log('Удаляем, если есть совпадение')
                    console.log(this.onlyActiveDepartments)
                    return
                }
            }
            this.onlyActiveDepartments.push(id.id)
            console.log(this.onlyActiveDepartments)
            // console.log('Добавляем пользователя')
        },

        DepartmentSelect(id) {
            for (let i = 0; i < this.Departments.length; i++) {
                if (id.id === this.Departments[i]) {
                    this.Departments.splice(i, 1)
                    // console.log('Удаляем совпадение')
                    console.log(this.Departments)
                    return
                }
            }
            this.Departments.push(id.id)
            console.log(this.Departments)
            // console.log('Добавляем пользователя')
        },

        storeSettings({state, commit, dispatch}) {
            axios.post('/api/people/lead', {'onlyActiveDepartments': this.onlyActiveDepartments, 'Departments': this.Departments} )
                .catch(
                    (error) => {
                        console.log(error)
                    })
        },
    },


    computed: {
        ...
            mapGetters({
                people: 'people',
                myOptions: 'myOptions',
                myOptionsOnlyActive: 'myOptionsOnlyActive',
            }),
    }
    ,

}
</script>

<style scoped>

</style>
