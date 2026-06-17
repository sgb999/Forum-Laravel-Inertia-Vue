<template>
    <Head title="View Post" />
    <div class="container">
        <h3>{{post.data.title}}</h3>
        <p>{{post.data.content}}</p>
        <div class="user">
            <img class="avatar" :src="post.data.user?.profilePicture?.thumb" alt="avatar">
            <inertia-link :href="route('user.profile', { username : post.data.user.username })">
                {{post.data.user.username}}
            </inertia-link>
            <p>{{ formatDate(post.data.createdAt) }}</p>
        </div>
        <div v-if="post.data.user.id === page.props.auth.user.id" class="mt-3">
            <inertia-link :href="route('post.edit', post.data.id)" id="edit" class="btn btn-primary col-1 btn-style">Edit</inertia-link>
            <button class="btn btn-danger" @click="deletePost">Delete</button>
        </div>
        <hr>
    </div>
    <div class="container">
        <h3>Comments</h3>
        <Deferred data="comments">
            <template #fallback>
                <page-loader />
            </template>
            <comment :postId="post.data.id" :comments="comments"/>
        </Deferred>
    </div>
</template>

<script setup lang="ts">

// Vue
import { defineOptions, defineProps} from "vue";

// Inertia
import { router, Deferred, usePage } from "@inertiajs/vue3";

// Layout
import appLayout from "../../layout/AppLayout.vue";

// Components
import Comment from "../../components/Comment.vue";
import PageLoader from "../../components/PageLoader.vue";

// Types
import { Resource } from "../../types/Resource";
import { Post } from "../../types/Post";
import { Paginated } from "../../types/Pagination";
import { Comment as CommentInterface } from "../../types/Comment";

// Composables
import Swal from 'sweetalert2';
import type { SweetAlertResult } from 'sweetalert2';


defineOptions({
    name: "Index",
    layout: appLayout
});

const props = defineProps<{
    post: Resource<Post>,
    comments?: Paginated<CommentInterface>;
}>();

const page = usePage();

function deletePost(): void {
    Swal.fire({
        title: 'Are you sure you want to delete your post?',
        text: 'Your post will be gone forever!',
        icon: 'warning',
        showConfirmButton: true,
        showCancelButton: true
    }).then((result: SweetAlertResult) => {
        if (result.isConfirmed) {
            router.delete(route('post.destroy', { post: props.post.data?.id }));
        }
    });
}

</script>

<style scoped lang="sass">
.user
    padding: 10px
    width: fit-content
    border-radius: 10px
    background-color: #2A2724
    a
        text-decoration: underline
        color: #fff
    .avatar
        height: 32px
        width: 32px
        border-radius: 50%
        border: solid 2px #FFFFFF
        margin-right: 20px
        color: rgb(228, 230, 235)
    p
        padding-left: 51px
a
    color: #000000
    text-decoration: none
    margin-right: 3px
hr
    font-weight: bold
.btn-style
    color: #ffffff
#edit
    width: 70px
</style>
