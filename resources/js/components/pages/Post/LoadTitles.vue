<template>
    <navigation-bar />
    <div class="container">
        <div class="row">
            <h2 class="col">Topics</h2>
            <inertia-link v-if="$page.props.auth.login" class="btn button-dark col-2" href="/post/">Make a Post</inertia-link>
        </div>
        <hr>
        <view-topics :topics="posts.data"/>
        <pagination v-if="posts.meta" :links="posts.meta"></pagination>
    </div>
    <Footer />
</template>

<script>
import ViewTopics from "./ViewTopics.vue";
import NavigationBar from "../../layout/NavigationBar.vue";
import Footer from "../../layout/Footer.vue";
import Pagination from "../../layout/Pagination.vue";
export default {
    name: "LoadTitles",
    components:{
        NavigationBar,
        Footer,
        ViewTopics,
        Pagination
    },
    props: {
        posts: {
            type: Object,
            required: true
        }
    }
};
</script>
<style lang="sass">
button
    display: block
    margin: 0 auto
.pagination
    list-style-type: none
    display: flex
    justify-content: center
    .page-item
        display: list-item
        padding: 5px 10px
        margin: 0 3px
        .font-bold
            font-weight: bold
</style>
