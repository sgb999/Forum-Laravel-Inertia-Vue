<template>
    <Head title="Chats" />
    <navigation-bar />
    <div class="container">
    <h1>Chats</h1>
    <hr>
    <div v-for="chat in chats">
        <inertia-link :href="route('chat.show', chat.id)">{{ chat.username }}</inertia-link>
        <hr />
    </div>
    </div>
    <Footer />
</template>

<script>
import NavigationBar from "../layout/NavigationBar.vue";
import Footer from "../layout/Footer.vue";
export default {
    name: "chat",
    components: {
        NavigationBar,
        Footer
    },
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
