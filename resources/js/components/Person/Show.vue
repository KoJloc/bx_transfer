<template>
    <div v-if="person">
        <div>
            ID: {{ person.ID }}
        </div>
        <div>
            ASSIGNED_BY_ID: {{ person.ASSIGNED_BY_ID }}
        </div>
        <div>
            SECOND_NAME: {{ person.SECOND_NAME }}
        </div>
        <div>
            LAST_NAME: {{ person.LAST_NAME }}
        </div>
        <div>
            LEAD_ID: {{ person.LEAD_ID }}
        </div>
        <div>
        <router-link :to="{name: 'person.edit', params: { id: person.ID }}">Edit</router-link>
        </div>
    </div>
</template>

<script>

export default {

    name: "Show",

    mounted() {
        this.$store.dispatch('getPerson', this.$route.params.ID)
    },

    computed: {
        person(){
            return this.$store.getters.person
        },
        value() {

        }
    }

}
</script>

<style scoped>

</style>
