<template>
    <div>
        <table class="table">
            <thead>
            <tr>
                <th scope="col">Name</th>
                <th scope="col">Age</th>
                <th scope="col">Job</th>
            </tr>
            </thead>
            <tbody>
            <tr v-for="person in people">
                <td>{{person.name}}</td>
                <td>{{person.age}}</td>
                <td>{{person.job}}</td>
            </tr>
            </tbody>
        </table>
    </div>
</template>

<script>

export default {
    name: "Table",

    data() {
        return {
            people: null
        }
    },

    mounted() {
      this.getPeople()
    },

    methods:{
        getPeople() {
            axios.post('/api/people')
                .then( res => {
                    this.people = res.data
                })
                .catch(err => {
                    console.log(err)
                })
        }
    },

}
</script>

<style scoped>

</style>
