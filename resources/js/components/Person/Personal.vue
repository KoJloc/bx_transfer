<template>
    <div>
        ur page
    </div>
</template>

<script>

export default {
    name: "Personal",

    computed: {
        person(){
            return this.$store.getters.person
        }
    }

}
</script>

<style scoped>

</style>
