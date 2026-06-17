<template>
    <div class="container">
        <div v-if="page.props.auth.login" class="d-block">
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
        <div v-if="comments?.data?.length < 1" class="empty-comments mt-3">
            <h4>There are no comments yet</h4>
        </div>
        <div v-if="comments.data" :key="comment.id"
            v-for="(comment, index) in comments.data">
            <hr>
            <p id="user-comment">{{ comment.comment }}</p>
            <div v-if="page.props.auth.user.id === comment.user.id" class="commentForm">
               <form @submit.prevent class="edit-form form-floating">
                    <textarea id="edit-comment" class="form-control" rows="4" minlength="4" placeholder="Edit Comment">{{ comment.comment }}</textarea>
                    <label id="editCommentLabel" for="edit-comment">Edit Comment</label>
                    <div class="buttons">
                        <button id="update" class="btn btn-primary" @click="updateComment(index, $event)">Update Comment</button>
                        <button id="cancel" class="btn btn-primary" @click="cancelComment(index, $event)">Cancel</button>
                        <button id="edit" class="btn btn-primary" @click="toggleForm(FormToggle.Open, $event)">Edit Comment</button>
                        <button id="delete" class="btn btn-danger" @click="deleteComment(index)">Delete Comment</button>
                    </div>
               </form>
            </div>
            <inertia-link :href="route('user.profile', comment.user.username)">
                {{ comment.user.username }}
            </inertia-link>
            <p>{{ formatDate(comment.createdAt) }}</p>
        </div>
        <div class="container mt-4">
            <pagination v-if="comments.meta.links" :links="comments.meta.links"></pagination>
        </div>
    </div>
</template>

<script setup lang="ts">

// Vue
import { defineOptions, defineProps } from 'vue'

// Inertia
import { useHttp, router, usePage } from '@inertiajs/vue3'

// Components
import pageLoader from "./PageLoader.vue";
import Pagination from "../layout/Pagination.vue";
import Swal from 'sweetalert2';

// Types
import { Paginated } from "../types/Pagination";
import { Comment } from "../types/Comment";
import { SweetAlertResult } from 'sweetalert2';

defineOptions({
    name: 'Comment'
});

const props = defineProps<{
    postId: Number,
    comments: Paginated<Comment>
}>();

const page = usePage();

const form = useHttp<{ comment: string }, Comment>({
    comment: ''
}).dontRemember('comment').withAllErrors();
const editForm = useHttp({ comment: '' }).withAllErrors();


enum FormToggle {
    Open = 'open',
    Close = 'close'
}

/**
 * Create a comment
 */
function setComment(): void
{
    form.post(route('comment.store', {post: props.postId}), {
        onSuccess: (response: Comment) => {
            props.comments.data.unshift(response);
            Swal.fire({
                title: 'Your comment has been posted!',
                text: '',
                icon: 'success',
                timer: 3000
            });
            form.comment = '';
        }
    });
}

/**
 * Cancel editing a comment
 *
 * @param index
 * @param event
 */
function cancelComment(index: number, event: MouseEvent): void
{
    const form = (event.target as HTMLElement)?.closest('form');

    if (!form) return;

    (form.querySelector<HTMLTextAreaElement>('textarea')!).value = props.comments.data[index].comment;
    toggleForm(FormToggle.Close, event);
}


/**
 * Update a comment
 *
 * @param index
 * @param event
 */
function updateComment(index: number, event: MouseEvent): void {
    const textarea = (event.target as HTMLElement)
        .closest('form')
        ?.querySelector<HTMLTextAreaElement>('textarea')

    editForm.comment = textarea?.value ?? ''

    editForm.put(route('comment.edit', { comment: props.comments.data[index].id }), {
        onSuccess: () => {
            props.comments.data[index].comment = editForm.comment;
            toggleForm(FormToggle.Close, event)
            Swal.fire({
                title: 'Your comment has been updated!',
                icon: 'success',
                timer: 3000
            })
        },
        onError: () => {
            Swal.fire({
                title: 'Failed to update comment',
                text: editForm.errors.comment?.[0] ?? 'Something went wrong.',
                icon: 'error',
            })
        }
    })
}

/**
 * Delete a comment
 * @param index
 */
function deleteComment(index: number): void
{
    Swal.fire({
        title: 'Are you sure you want to delete your comment?',
        text: 'Your comment will be gone forever!',
        icon: 'warning',
        showConfirmButton: true,
        showCancelButton: true
    }).then((result: SweetAlertResult) => {
        if (!result.isConfirmed) {
            return false;
        }
        router.delete(route('comment.destroy', { comment: props.comments.data[index].id }), {
            onSuccess: () => {
                props.comments.data.splice(index, 1);
                Swal.fire({
                    title: 'Your comment has been Deleted!',
                    text: '',
                    icon: 'success',
                    timer: 3000
                });
            }
        });
    });
}

/**
 * Toggle the form so the user gets different HTML if the form should be opened or closed
 *
 * @param toggle
 * @param event
 */
function toggleForm(toggle: FormToggle, event: MouseEvent): void
{
    const form = (event.target as HTMLElement).closest('form') as HTMLElement;

    if (!form) return;

    const editControl = toggle === FormToggle.Open ? 'block' : 'none';
    const readControl = toggle === FormToggle.Open ? 'none' : 'block';

    const editControlElements: HTMLElement[] = [
        (form.querySelector<HTMLElement>('#edit-comment')!),
        (form.querySelector<HTMLElement>('#editCommentLabel')!),
        (form.querySelector<HTMLElement>('#update')!),
        (form.querySelector<HTMLElement>('#cancel')!)
    ];

    const readControlElements: HTMLElement[] = [
        (form.querySelector<HTMLElement>('#edit')!),
        (form.querySelector<HTMLElement>('#user-comment')!)
    ];

    editControlElements.forEach(editControlElement => {
        if (editControlElement === null) return;

        editControlElement.style.display = editControl
    });
    readControlElements.forEach(readControlElement => {
        if (readControlElement === null) return;

        readControlElement.style.display = readControl
    });
}

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
