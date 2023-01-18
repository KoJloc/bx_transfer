<template>
    <div class="w-25">
        <div class="form-group mb-1">
            <label for="exampleFormControlInput1">Name</label>
            <input type="text" class="form-control" v-model="name" placeholder="name">
        </div>
        <div class="form-group mb-1">
            <label for="exampleFormControlInput1">Age</label>
            <input type="number" class="form-control" v-model="age" placeholder="age">
        </div>
        <div class="form-group mb-1">
            <label for="exampleFormControlInput1">Job</label>
            <input type="text" class="form-control" v-model="job" placeholder="job">
        </div>
        <div>
            <input @click.prevent="updatePerson" type="submit" class="btn btn-primary mt-3" value="Update ">
        </div>
    </div>
</template>

<script>
import router from "../../router";

export default {
    name: "Edit",

    data() {
        return {
            name: '',
            age: '',
            job: '',
        }
    },

    mounted() {
        this.getPerson()
    },

    methods: {
        getPerson() {
            axios.get('/api/people/edit' + this.$route.params.id)
                .then(res => {
                    this.name = res.data.name
                    this.age = res.data.age
                    this.job = res.data.job
                })
                .catch(
                    (error) => {
                        console.log(error)
                    })
        },
        updatePerson() {
            axios.patch('/api/people/' + this.$route.params.id,
                {
                    name: this.name,
                    age: this.age,
                    job: this.job
                })
                .then(res => {
                    router.push({ name:'person.show', params: {id: this.$route.params.id }})
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
