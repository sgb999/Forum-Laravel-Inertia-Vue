<template>
    <Head><title>Make a post</title></Head>
    <navigation-bar />
    <div class="container w-50">
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
                    <button class="btn button-dark mt-2 float-end" :disabled="disableButton()" v-on:click="post">Post</button>
                </form>
            </div>
        </div>
    </div>
    <Footer />
</template>

<script>

import NavigationBar from "../layout/NavigationBar.vue";
import Footer from "../layout/Footer.vue";
import { useForm } from "@inertiajs/vue3";
export default {
    name: "MakePost",
    components: {
      NavigationBar,
      Footer
    },
    props: {
      categories: {
          required: true
      }
    },
    data() {
        let form = useForm({
            title : '',
            content : '',
            category_id : '',
            _token : this.$page.props.csrf,
        });
        return {
            form
        }
    },
    methods: {
        post() {
            this.form.post(route('post.store'), {
                onSuccess: () => {
                    this.$swal({
                        title: 'Your post has been posted!',
                        text: '',
                        icon: 'success'
                    });
                }
            });
        },
        disableButton() {
            return this.form.processing === true
                || this.form.title === ''
                || this.form.content === ''
                || this.form.category_id === ''
        }
    }
};
</script>

<style scoped lang="sass">
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
