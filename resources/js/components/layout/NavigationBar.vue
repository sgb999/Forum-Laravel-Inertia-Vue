<template>
    <nav>
        <ul>
            <li><inertia-link :href="route('home')">Home</inertia-link></li>
            <li><inertia-link :href="route('categories.index')" prefetch>View Categories</inertia-link></li>
            <li v-if="user" class="float-end"><inertia-link :href="route('log-out')"><span class="bi bi-box-arrow-right red me-1"></span> Sign out</inertia-link></li>
            <li v-if="user" class="float-end"><inertia-link :href="route('chat.index')" prefetch><span class="bi bi-envelope-fill yellow me-1"></span>Messages</inertia-link></li>
            <li v-if="user" class="float-end"><inertia-link :href="route('user.profile', username)">Profile</inertia-link></li>
            <li v-if="!user" class="float-end"><inertia-link :href="route('register.index')" prefetch ><span class="bi bi-person-fill me-1 green"></span>Sign Up</inertia-link></li>
            <li v-if="!user" class="float-end"><inertia-link :href="route('login.index')" prefetch><span class="bi bi-box-arrow-in-right me-1 green"></span>Login</inertia-link></li>
        </ul>
    </nav>
</template>
<script>
export default {
    name: "NavigationBar",
    computed: {
        user() {
            return this.$page.props.auth.login;
        },
        username(){
            return this.$page.props.auth.user.username;
        }
    }
};
</script>

<style scoped lang="sass">
nav
    ul
        list-style-type: none
        margin: 0
        padding: 0
        overflow: hidden
        background-color: #151210
        li
            float: left
            padding: 14px 16px
            &:hover
                border-radius: 10px
                padding: 6px 10px
                margin: 8px 6px
                background-color: #2E2B28
                cursor: pointer
            a
                display: block
                color: #9E9A94
                text-align: center
                text-decoration: none
                float: none
                &:hover:not(.active)
                    background-color: transparent
                .green
                    color: #008000
                .yellow
                    color: #FFFF00
                .red
                    color: #FF0000
</style>
