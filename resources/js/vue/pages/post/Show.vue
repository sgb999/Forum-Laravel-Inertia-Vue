<template>
    <div class="container">
        <div class="row">
            <h2 class="col">Topics</h2>
            <inertia-link
                v-if="$page.props.auth.login"
                  class="btn button-dark col-2"
                  :href="route('post.edit')"
            prefetch>
                Make a Post
            </inertia-link>
        </div>
        <hr>
        <posts :topics="posts.data"/>
        <pagination v-if="posts.meta" :links="posts.meta.links"></pagination>
    </div>
</template>

<script setup lang="ts">

// Vue
import { defineOptions, defineProps} from "vue";

// Layout
import appLayout from "../../layout/AppLayout.vue";

// Components
import Posts from "../../components/Posts.vue";
import Pagination from "../../layout/Pagination.vue";

// Types
import { Paginated } from "../../types/Pagination";
import { Post } from "../../types/Post";

defineOptions({
    name: "Show",
    layout: appLayout
});

defineProps<{
    posts: Paginated<Post>;
}>();

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
