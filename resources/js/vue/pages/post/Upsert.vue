<template>
    <Head><title>Make a post</title></Head>
    <div class="container">
        <div class="card">
            <div class="card-header">
                <h1>Create a Post</h1>
            </div>
            <div class="card-body">
                <form @submit.prevent>
                    <div class="form-floating mb-3">
                        <input id="postTitle" class="form-control" type="text" v-model="form.title" placeholder="Title" maxlength="255" required>
                        <label for="postTitle">Title</label>
                        <div v-if="form.errors.title" class="alert-danger">
                            <ul>
                                <li>{{ form.errors.title }}</li>
                            </ul>
                        </div>
                    </div>
                    <div class="form-floating mb-3">
                        <textarea class="form-control" placeholder="Content" v-model="form.content" id="formContent"></textarea>
                        <label for="formContent">Content</label>
                        <div v-if="form.errors.content" class="alert-danger">
                            <ul>
                                <li>{{ form.errors.content }}</li>
                            </ul>
                        </div>
                    </div>
                    <div class="form-floating mt-3 w-75">
                        <select class="form-select" id="category" aria-label="Floating label select example" v-model="form.category_id">
                            <option hidden value="">Please select one</option>
                            <option v-for="category in categories" :value="category.id">{{ category.name }}</option>
                        </select>
                        <label for="category">Category</label>
                    </div>
                    <button class="btn button-dark mt-2 float-end" :disabled="disableButton()" v-on:click="postForm">Post</button>
                </form>
            </div>
        </div>
    </div>
</template>

<script setup lang="ts">

// Vue
import { defineOptions, defineProps } from "vue";

// Inertia
import { useForm, usePage } from "@inertiajs/vue3";

// Layout
import appLayout from "../../layout/AppLayout.vue";

// Components
import Swal from 'sweetalert2';

// Types
import { Category } from "../../types/Category";
import { Post } from "../../types/Post";
import { Resource } from "../../types/Resource";

defineOptions({
    name: "Upsert",
    layout: appLayout
});

const props = defineProps<{
    categories: Category[],
    post?: Resource<Post | null>
}>();

const page = usePage();

let form = useForm({
    title: props.post?.data?.title ?? '',
    content: props.post?.data?.content ?? '',
    category_id: props.post?.data?.category?.id ?? '',
    _token: page.props.csrf,
});

function postForm(): void
{
    form.put(route('post.upsert', props?.post?.data?.id ? { post: props.post.data.id } : {}), {
        onSuccess: () => {
            Swal.fire({
                title: props?.post?.data?.id ? 'Your post has been updated successfully' : 'Your post has been posted!',
                text: '',
                icon: 'success'
            });
        }
    });
}

function disableButton(): boolean
{
    return form.processing || form.title === '' || form.content === '' || form.category_id === '';
}
</script>

<style scoped lang="sass">
@media (min-device-width: 768px)
    .container
        width: 50%
.card
    background: #242220
    color: #fff
    label
        color: #6B6760
    textarea
        height: 100px
    input, select, textarea
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
</style>
