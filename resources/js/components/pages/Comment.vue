<template>
    <div class="container">
        <div v-if="$page.props.auth.login" class="d-block">
            <form @submit.prevent class="commentForm">
                <div class="form-floating">
                    <textarea id="new-comment" v-model="form.comment" placeholder="Comment" class="form-control" minlength="4"></textarea>
                    <label for="new-comment">Add a Comment</label>
                    <div v-if="form?.errors?.comment" class="alert alert-danger">
                        <ul v-for="error in form.errors.comment">
                            <li>{{ error }}</li>
                        </ul>
                    </div>
                </div>
                <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                    <button class="btn button-dark float-end" :disabled="Object(form.comment).length < 4 || form.processing" v-on:click="setComment">Submit</button>
                </div>
            </form>
        </div>
        <page-loader v-if="!comments.data"/>
        <div v-if="comments.data.length < 1" class="empty-comments mt-3">
            <h4>There are no comments yet</h4>
        </div>
        <div v-if="comments.data" :key="comment.id"
            v-for="(comment, index) in comments.data">
            <hr>
            <p id="user-comment">{{ comment.comment }}</p>
            <div v-if="$page.props.auth.user.id === comment.user.id" class="commentForm">
               <form @submit.prevent class="edit-form form-floating">
                    <textarea id="edit-comment" class="form-control" rows="4" minlength="4" placeholder="Edit Comment">{{ comment.comment }}</textarea>
                    <label id="editCommentLabel" for="edit-comment">Edit Comment</label>
                    <div class="buttons">
                        <button id="update" class="btn btn-primary" @click="updateComment(index, $event)">Update Comment</button>
                        <button id="cancel" class="btn btn-primary" @click="cancelComment(index, $event)">Cancel</button>
                        <button id="edit" class="btn btn-primary" @click="openForm">Edit Comment</button>
                        <button id="delete" class="btn btn-danger" @click="deleteComment(index)">Delete Comment</button>
                    </div>
               </form>
            </div>
            <inertia-link :href="route('user.profile', comment.user.username)">
                {{ comment.user.username }}
            </inertia-link>
            <p>{{ this.formatDate(comment.createdAt) }}</p>
        </div>
        <div class="container mt-4">
            <pagination v-if="comments.meta.links" :links="comments.meta.links"></pagination>
        </div>
    </div>
</template>

<script>
import pageLoader from "./PageLoader.vue";
import { useForm } from "@inertiajs/vue3"
import Pagination from "../layout/pagination.vue";
import { usePage } from '@inertiajs/vue3'
import { computed } from 'vue';
import moment from "moment";
export default {
    name: "Comment",
    components:{
      pageLoader,
      Pagination
    },
    props:{
        postId: {
            type: Number,
            required: true
        },
        comments: {
            required: false
        }
    },
    data(){
        return {
            form: {
                comment: '',
                errors: {
                    comment: null
                }
            },
            disabled: Boolean
        }
    },
    methods: {
        setComment() {
            this.form.errors.comment = null;
            axios.post(route('comment.store', {post: this.postId}), {
                comment: this.form.comment
            }).then((response) => {
                if (response.status === 201) {
                    this.comments.data.unshift(response.data);
                    this.$swal({
                        title: 'Your comment has been posted!',
                        text: '',
                        icon: 'success',
                        timer: 3000
                    });
                    this.form.comment = '';
                }
            }).catch((error) => {
                if (error.response?.status === 422) {
                    this.form.errors.comment = error.response.data.errors.comment;
                }
            });
        },
        cancelComment(index, event){
            const form = event.target.closest('.edit-form');
            form.querySelector("textarea").value = this.comments.data[index].comment;
            this.closeForm(event);
        },
        updateComment(index, event) {
            axios.put(route('comment.edit', {comment: this.comments.data[index].id}),
                {
                    comment: event.target.parentElement.parentElement.querySelector('textarea').value
                }).then((response) => {
                if (response.status === 200) {
                    this.comments.data[index] = response.data;
                    this.closeForm(event);
                    this.$swal({
                        title: 'Your comment has been updated!',
                        text: '',
                        icon: 'success',
                        timer: 3000
                    });
                }
            });
        },
        deleteComment(index)
        {
            this.$swal({
                title: 'Are you sure you want to delete your comment?',
                text: 'Your comment will be gone forever!',
                icon: 'warning',
                showConfirmButton: true,
                showCancelButton: true,
                dangerMode: true
            }).then((result) => {
                if (!result.isConfirmed) {
                    return false;
                }
                this.$inertia.delete(route('comment.destroy', { comment: this.comments.data[index].id }), {
                    onSuccess: () => {
                        this.comments.data.splice(index, 1);
                        this.$swal({
                            title: 'Your comment has been Deleted!',
                            text: '',
                            icon: 'success',
                            timer: 3000
                        });
                    }
                });
            });
        },
        openForm(event)
        {
            const parent = event.target.parentElement.parentElement.parentElement.parentElement;

            // Shoe edit buttons
            parent.querySelector('#edit-comment').style.display = 'block';
            parent.querySelector('#editCommentLabel').style.display = 'block';
            parent.querySelector('#update').style.display = 'block';
            parent.querySelector('#cancel').style.display = 'block';

            // Hide comment and edit button
            parent.querySelector('#edit').style.display = 'none';
            parent.querySelector('#user-comment').style.display = 'none';
        },
        closeForm(event)
        {
            const parent = event.target.parentElement.parentElement.parentElement.parentElement;

            // Hide buttons
            parent.querySelector('#edit-comment').style.display = 'none';
            parent.querySelector('#editCommentLabel').style.display = 'none';
            parent.querySelector('#update').style.display = 'none';
            parent.querySelector('#cancel').style.display = 'none';

            // Show edit button and comment again
            parent.querySelector('#edit').style.display = 'block';
            parent.querySelector('#user-comment').style.display = 'block';
        }
    }
};
</script>

<style scoped lang="sass">
.pagination
    display: flex
    justify-content: center
    list-style-type: none
    padding: 0
.commentForm
    #new-comment
        height: 100px
    #edit-comment
        display: none
    .form-control
        margin-bottom: 10px
    label
        color: #6B6760
    #editCommentLabel
        display: none
    textarea
        height: 100px
        background: #1b1a1f
        color: #fff
        caret-color: #ffffff
        border-color: #6B6760
        &:focus
            box-shadow: none
            outline: 0
    .form-floating > textarea:focus ~ label::after
        background: transparent
        font-size: 20px
    .form-floating > textarea ~ label::after
        background: transparent
        font-size: 20px
a
    color: #FFFFFF
    text-decoration: none
#update
    display: none
#cancel
    display: none
.empty-comments
    height: 200px
    width: 80%
    background-color: #A9A9A9
    border-radius: 25px
    display: block
    margin: 0 auto
    h4
        display: grid
        text-align: center
        padding-top: 90px
.buttons
    display: flex
    flex: 1
    justify-content: left
    button
        margin: 0 3px 0 0
</style>
