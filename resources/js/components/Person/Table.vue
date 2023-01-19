<template>
    <div>
        <table class="table">
            <thead>
            <tr>
                <th scope="col">Name</th>
                <th scope="col">Age</th>
                <th scope="col">Job</th>
                <th scope="col">Delete</th>
            </tr>
            </thead>
            <tbody>
            <tr v-for="person in people">
                <td>
                    <router-link :to="{ name: 'person.show', params: { id:person.id }}">{{ person.name }}</router-link>
                </td>
                <td>{{ person.age }}</td>
                <td>{{ person.job }}</td>
                <td>
                    <a @click.prevent="$store.dispatch('deletePerson', person.id )" href="#"
                       class="btn btn-outline-danger btn-sm">Delete</a>
                </td>
            </tr>
            </tbody>
        </table>
    </div>
</template>

<script>

export default {
    name: "Table",

    mounted() {
        this.$store.dispatch('getPeople')
    },

    methods: {},

    computed: {
        people() {
            return this.$store.getters.people
        }
    }

}
</script>

<style scoped>

</style>
