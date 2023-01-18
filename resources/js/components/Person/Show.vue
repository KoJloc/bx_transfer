<template>
    <div>
        <div>
            Name: {{ this.person.name }}
        </div>
        <div>
            Age: {{ this.person.age }}
        </div>
        <div>
            Job: {{ this.person.job }}
        </div>
    </div>
</template>

<script>
export default {
    name: "Show",

    data() {
        return {
            person: null,
        }
    },

    mounted() {
        this.getPerson()
    },

    methods:{
        getPerson() {
            axios.get('/api/people/' + this.$route.params.id)
                .then(res => {
                    this.person = res.data
                })
                .catch(
                    (error) => {
                        console.log(error)
                    })
        },
    }
}
</script>

<style scoped>

</style>
