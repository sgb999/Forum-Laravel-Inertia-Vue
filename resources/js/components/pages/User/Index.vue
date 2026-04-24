<template>
    <Head title="Profile" />
    <navigation-bar />
    <div id="view-user-page" class="container" v-if="user !== null">
        <img class="banner position-relative d-block mx-auto" :src="user.data.bannerPicture?.url || '/storage/default/banner.jpg'" alt="banner">
        <div class="user position-relative d-flex">
            <div class="justify-content-start">
                <img class="avatar" :src="user.data.profilePicture?.thumb || '/storage/default/avatar.png'" alt="avatar">
                <h1>{{ user.data.username }}</h1>
            </div>
            <div class="justify-content-end">
                <inertia-link v-if="user.data.id === $page.props.auth.user.id" :href="route('user.update-profile', $page.props.auth.user.username)" class="btn button-dark">Edit Profile</inertia-link>
                <inertia-link v-if="user.data.id !== $page.props.auth.user.id && $page.props.auth.login" :href="route('chat.show', user.data.id)" class="btn btn-primary">Message</inertia-link>
            </div>
        </div>
    </div>
    <div class="content">
        <hr class="container">
        <view-topics :topics="posts.data"></view-topics>
        <div v-if="posts.data.length < 1" class="empty-posts d-block mx-auto">
            <h4 class="d-grid text-center">There are no posts yet</h4>
        </div>
    </div>
    <pagination v-if="posts.meta" class="container" :links="posts.meta"></pagination>
    <Footer />
</template>

<script>
import NavigationBar from "../../layout/NavigationBar.vue";
import Footer from "../../layout/Footer.vue";
import ViewTopics from "../Post/ViewTopics.vue";
import Pagination from "../../layout/Pagination.vue";
export default {
    name: "Index",
    props: {
        user: {
            type: Object,
            required: true
        },
        posts: {
            type: Object,
            required: true
        }
    },
    components: {
        NavigationBar,
        Footer,
        ViewTopics,
        Pagination
    }
};
</script>

<style scoped lang="sass">
.banner
  border-radius: 10px
  height: 600px
  width: clamp(400px, 100%, 1296px)
  box-sizing: border-box
.content
    .empty-posts
        width: 80%
        background-color: #A9A9A9
        border-radius: 25px
        h4
            padding-top: 90px
            padding-bottom: 90px
.user
  padding-top: 0
  bottom: 45px
  flex: 1
  h1, .right
    margin-top: 50px
  .right
    margin-left: 50%
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
