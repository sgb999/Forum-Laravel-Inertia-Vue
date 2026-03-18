<template>
    <div class="container">
        <h3>Comments</h3>
        <div v-if="$page.props.auth.login">
            <form @submit.prevent class="add-comment">
                <div class="form-floating">
                    <textarea id="new-comment" v-model="form.comment" placeholder="Comment" class="form-control" minlength="4"></textarea>
                    <label for="new-comment">Add a Comment</label>
                    <div v-if="form.errors.comment" class="alert-danger">
                        <ul>
                            <li>{{ form.errors.comment }}</li>
                        </ul>
                    </div>
                </div>
                <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                    <button class="btn button-dark float-end" :disabled="Object.keys(form.comment).length < 4 || form.processing" v-on:click="setComment">Submit</button>
                </div>
            </form>
        </div>
        <page-loader v-if="!comments.data"/>
        <div v-if="comments.data < 1" class="empty-comments mt-3">
            <h4>There are no comments yet</h4>
        </div>
        <div v-if="comments.data" :key="comment.id"
            v-for="(comment, index) in comments.data">
            <hr>
            <p id="user-comment">{{ comment.comment }}</p>
            <div v-if="$page.props.auth.user.id === comment.user.id">
               <form @submit.prevent class="edit-form">
                    <textarea id="edit-comment" class="form-control" rows="4" minlength="4">{{ comment.comment }}</textarea>
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
            <p>{{ this.formatDate(comment.created_at) }}</p>
        </div>
        <pagination v-if="comments.links" :links="comments.links" @nextPage="getComments($event)"></pagination>
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
      id: {
          required: true
      }
    },
    data(){
        const form = useForm({
            comment : '',
            _token : computed(() => usePage().props.csrf),
            post_id : this.id
        });
      return {
          comments: [],
          date: 0,
          disabled: Boolean,
          success: {},
          form,
          lastPaginationEvent: ''
      }
    },
    methods: {
        getComments(site) {
            this.lastPaginationEvent = site;
            this.comments = [];
            axios.get(site).then((response) => {
                this.comments = response.data;
            }).catch((error) => {
                console.log('Error: ' + error);
            });
        },
        setComment(){
           this.form.post(route('comment.store'), {
               onSuccess: () => {
                   this.getComments(this.lastPaginationEvent);
                   this.$swal({
                       title: 'Your comment has been posted!',
                       text: '',
                       icon: 'success',
                       timer: 3000
                   });
               }
           });
           this.form.comment = '';
        },
        cancelComment(index, event){
            const form = event.target.closest('.edit-form');
            form.querySelector("textarea").value = this.comments.data[index].comment;
            this.closeForm(event);
        },
        updateComment(index, event){
            const comment = useForm({
                _token : this.$page.props.csrf,
                comment: event.target.parentElement.parentElement.querySelector('textarea').value
            });
            this.$inertia.put(`/comment/${this.comments.data[index].id}`, comment, {
                onSuccess: () => {
                    this.comments.data[index].comment = comment.comment;
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
                    if(result.isConfirmed){
                        this.$inertia.delete(`/comment/${this.comments.data[index].id}`, {
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
                    }
                    else{
                        return false;
                    }

            });
        },
        openForm(event)
        {
            const parent = event.target.parentElement.parentElement.parentElement.parentElement;

            // Shoe edit buttons
            parent.querySelector('#edit-comment').style.display = 'block';
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
            parent.querySelector('#update').style.display = 'none';
            parent.querySelector('#cancel').style.display = 'none';

            // Show edit button and comment again
            parent.querySelector('#edit').style.display = 'block';
            parent.querySelector('#user-comment').style.display = 'block';
        },
      formatDate(value)
      {
        return moment(String(value)).format('DD/MM/YYYY H:MM a')
      }
    },
      mounted() {
        this.getComments('/comment/view/' + this.id);
    }
};
</script>

<style scoped lang="sass">
form
    #new-comment
        height: 100px
    #edit-comment
        display: none
    .form-control
        margin-bottom: 10px
    label
        color: #6B6760
        textarea
            height: 100px
        textarea
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
