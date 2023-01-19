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
            <input :disabled="isButtonDisabled"
                   @click.prevent="$store.dispatch('storePerson', {name, age, job})"
                   type="submit" class="btn btn-primary mt-3" value="Add">
        </div>
    </div>
</template>

<script>

export default {
    name: "Create",

    data() {
        return {
            name: '',
            age: '',
            job: '',
        }
    },

    methods: {},

    computed: {
        isButtonDisabled() {
            return !(this.name && this.age && this.job)
        }
    },

}

</script>

<style scoped>

</style>
