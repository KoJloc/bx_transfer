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
            <input @click.prevent="store" type="submit" class="btn btn-primary mt-3" value="Add">
        </div>
    </div>
</template>

<script>
import router from "../../router";

export default {
    name: "Create",

    data() {
        return {
            name: '',
            age: '',
            job: '',
        }
    },

    methods:{
        store(){
            axios.post('/api/people', {
                name: this.name,
                age: this.age,
                job: this.job
            })
                .then(res => {
                    router.push({name:"person.index"})
                })
               .catch(err => {
                    console.log(err)
                })
        }
    }

}

</script>

<style scoped>

</style>
