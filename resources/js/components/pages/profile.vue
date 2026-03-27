<template>
    <Head title="Profile" />
    <navigation-bar />
    <img class="banner" :src="banner ? banner : '/storage/default/banner.jpg'" alt="banner">
    <div id="view-user-page" class="container" v-if="user !== null">
        <div class="user">
            <img class="avatar" :src="avatar ? avatar : '/storage/default/avatar.png'" alt="avatar">
            <h1>{{ user.username }}</h1>
            <div class="right">
                <inertia-link v-if="user.id === $page.props.auth.user.id" :href="route('user.update-profile', $page.props.auth.user.username)" class="btn button-dark">Edit Profile</inertia-link>
                <inertia-link v-if="user.id !== $page.props.auth.user.id && $page.props.auth.login" :href="route('chat.show', user.id)" class="btn btn-primary">Message</inertia-link>
            </div>
        </div>
    </div>
    <div class="content">
        <hr class="container">
        <view-topics :topics="posts.data"></view-topics>
        <div v-if="posts.data < 1" class="empty-posts">
            <h4>There are no posts yet</h4>
        </div>
    </div>
    <pagination v-if="posts.links" class="container" :links="posts.links" @nextPage="getTopics($event)"></pagination>
    <Footer />
</template>

<script>
import NavigationBar from "../layout/NavigationBar.vue";
import Footer from "../layout/Footer.vue";
import ViewTopics from "./viewTopics.vue";
import Pagination from "../layout/pagination.vue";
export default {
    name: "profile",
    props: {
        user: {
            required: true
        }
    },
    components: {
        NavigationBar,
        Footer,
        ViewTopics,
        Pagination
    },
    data() {
        return {
            banner: '',
            avatar: '',
            posts: [],
        }
    },
    methods: {
        getTopics(site) {
            axios.get(site).then((response) => {
                this.posts = response.data;
            }).catch((error) => {
                console.log('Error: ' + error);
            });
        }
    },
    mounted() {
        this.getTopics('/view-profile-posts/' + this.user.id);
        this.user.media.forEach(el => {
            if(el.collection_name === 'banner'){
                this.banner = el.original_url;
            }
            if(el.collection_name === 'avatar'){
                this.avatar = el.original_url;
            }
        })
    }
};
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
