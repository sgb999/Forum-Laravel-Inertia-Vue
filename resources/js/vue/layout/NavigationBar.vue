<template>
    <nav aria-label="Main navigation">
        <ul class="nav-links">
            <li><inertia-link :href="route('post.show')">Home</inertia-link></li>
            <li><inertia-link :href="route('categories.show')" prefetch>View Categories</inertia-link></li>
        </ul>

        <ul class="nav-actions">
            <li v-if="isLoggedIn">
                <a href="#" @click.prevent="logout">
                    <span class="bi bi-box-arrow-right red me-1"></span> Sign out
                </a>
            </li>
            <li v-if="isLoggedIn"><inertia-link :href="route('chat.show')" prefetch><span class="bi bi-envelope-fill yellow me-1"></span>Messages</inertia-link></li>
            <li v-if="isLoggedIn"><inertia-link :href="route('user.profile', username)">Profile</inertia-link></li>
            <li v-if="!isLoggedIn"><inertia-link :href="route('register.index')" prefetch><span class="bi bi-person-fill me-1 green"></span>Sign Up</inertia-link></li>
            <li v-if="!isLoggedIn"><inertia-link :href="route('login.index')" prefetch><span class="bi bi-box-arrow-in-right me-1 green"></span>Login</inertia-link></li>
        </ul>
    </nav>
</template>

<script setup lang="ts">
// Vue
import { computed } from "vue";

// Inertia
import { usePage, router } from "@inertiajs/vue3";

defineOptions({
    name: "NavigationBar"
});

const page = usePage();

const isLoggedIn = computed(() => page.props.auth.login);
const username = computed(() => page.props.auth.user.username ?? undefined);

function logout(): void
{
    router.post(route('logout'));
}
</script>

<style scoped lang="sass">
nav
    display: flex
    flex-wrap: wrap
    align-items: center
    justify-content: space-between
    padding: 0 8px
    background-color: #151210

    ul
        display: flex
        flex-wrap: wrap
        list-style-type: none
        margin: 0
        padding: 0

    li
        display: flex

    a
        display: flex
        align-items: center
        justify-content: center
        padding: 14px 16px
        border-radius: 10px
        color: #9E9A94
        text-align: center
        text-decoration: none
        cursor: pointer

        &:hover
            background-color: #2E2B28

        .green
            color: #008000
        .yellow
            color: #FFFF00
        .red
            color: #FF0000

@media (max-width: 600px)
    nav
        flex-direction: column

        ul
            width: 100%
            justify-content: center

        li
            flex: 0 1 auto

        a
            padding: 12px
            white-space: nowrap
</style>
