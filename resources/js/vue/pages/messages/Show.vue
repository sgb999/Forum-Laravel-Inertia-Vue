<template>
    <Head title="Chats" />
    <div class="container">
    <h1>Chats</h1>
    <hr>
    <div v-for="chat in chats">
        <inertia-link :href="route('chat.show', chat.id)">{{ chat.username }}</inertia-link>
        <hr />
    </div>
    </div>
</template>

<script>
import appLayout from "../../layout/AppLayout.vue";

export default {
    name: "Show",
    layout: appLayout,
    props: {
        chats: {
            required: true
        }
    }
};
</script>

<style scoped lang="sass">
a
    color: #FFFFFF
    text-decoration: none
    font-size: 20px

</style>
