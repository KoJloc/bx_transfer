<template>
    <div class="w-25" v-if="person">
        <div class="form-group mb-1">
            <label for="exampleFormControlInput1">Name</label>
            <input type="text" class="form-control" v-model="person.name" placeholder="name">
        </div>
        <div class="form-group mb-1">
            <label for="exampleFormControlInput1">Age</label>
            <input type="number" class="form-control" v-model="person.age" placeholder="age">
        </div>
        <div class="form-group mb-1">
            <label for="exampleFormControlInput1">Job</label>
            <input type="text" class="form-control" v-model="person.job" placeholder="job">
        </div>
        <div>
            <input :disabled="isButtonDisabled"
                   @click.prevent="$store.dispatch('updatePerson', {id: person.id, name: person.name, age: person.age, job: person.job})"
                   type="submit" class="btn btn-primary mt-3" value="Update ">
        </div>
        <div>

        </div>
    </div>
</template>

<script>
import {mapGetters} from 'vuex';

export default {
    name: "Edit",

    mounted() {
        this.$store.dispatch('getPerson', this.$route.params.id)
    },

    methods: {

    },

    computed: {
        ...mapGetters({
            person: 'person',
            isButtonDisabled: 'isButtonDisabled',
        })
    },

}

</script>

<style scoped>

</style>
