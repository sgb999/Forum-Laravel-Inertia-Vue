<template>
    <Head title="Profile" />
    <img class="banner" :src="user.data.bannerPicture.url" alt="banner">
    <div id="view-user-page" class="container">
        <div class="user">
            <img class="avatar" :src="user.data.profilePicture.url" alt="avatar">
            <h1>{{ user.data.username }}</h1>
            <div class="right">
                <inertia-link v-if="user.data.id === $page.props.auth.user.id" :href="route('user.update-profile', $page.props.auth.user.username)" class="btn button-dark">Edit Profile</inertia-link>
                <inertia-link v-if="user.data.id !== $page.props.auth.user.id && $page.props.auth.login" :href="route('chat.index', user.data.id)" class="btn btn-primary">Message</inertia-link>
            </div>
        </div>
    </div>
    <div class="content">
        <hr class="container">
        <Deferred data="posts">
            <template #fallback>
                <page-loader />
            </template>
            <posts :topics="posts.data"></posts>
            <div v-if="posts.data < 1" class="empty-posts">
                <h4>There are no posts yet</h4>
            </div>
            <pagination v-if="posts.meta.links" class="container" :links="posts.meta.links"></pagination>
        </Deferred>
    </div>
</template>

<script setup lang="ts">

// Vue
import { defineOptions, defineProps} from "vue";

// Inertia
import { Deferred } from "@inertiajs/vue3";

// Layout
import appLayout from "../../layout/AppLayout.vue";

// Components
import Posts from "../../components/Posts.vue";
import Pagination from "../../layout/Pagination.vue";
import PageLoader from "../../components/PageLoader.vue";

// Types
import { Resource } from "../../types/Resource";
import { User } from "../../types/User";
import { Paginated } from "../../types/Pagination";
import { Post } from "../../types/Post";

defineOptions({
    name: "Profile",
    layout: appLayout
});

defineProps<{
    user: Resource<User>
    posts?: Paginated<Post>
}>();
</script>

<style scoped lang="sass">
.banner
  position: relative
  border-radius: 10px
  height: 600px
  width: clamp(400px, 100%, 1296px)
  display: block
  margin: 0 auto
  box-sizing: border-box
.content
    .empty-posts
        width: 80%
        background-color: #A9A9A9
        border-radius: 25px
        display: block
        margin: 0 auto
        h4
            display: grid
            text-align: center
            padding-top: 90px
            padding-bottom: 90px
.user
  padding-top: 0
  position: relative
  bottom: 45px
  display: flex
  flex: 1
  h1, .right
    margin-top: 50px
  .right
    margin-left: 50vw
    height: fit-content
  .avatar
    height: 150px
    width: 150px
    border-radius: 50%
    border: solid 2px #FFFFFF
    margin-right: 20px
@media screen and (max-width: 600px)
    .banner
        height: 400px
        top: 240px
    .user
        padding-top: 350px
    .content
        padding-top: 500px
</style>
