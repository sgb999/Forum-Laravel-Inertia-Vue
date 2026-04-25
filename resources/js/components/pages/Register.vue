<template>
    <Head><title>Register</title></Head>
    <navigation-bar />
    <div class="container">
        <div class="card">
            <div class="card-header">
                <h1>Register an account</h1>
            </div>
            <div class="card-body">
                <form @submit.prevent>
                    <div class="form-floating mb-3">
                        <input class="form-control col-4 d-flex justify-content-center" type="text" v-model="form.name" placeholder="John Doe" maxlength="255" required>
                        <label>Name</label>
                        <div v-if="form.errors.name" class="alert-danger">
                            <ul>
                                <li>{{ form.errors.name }}</li>
                            </ul>
                        </div>
                    </div>
                    <div class="form-floating mb-3">
                        <input class="form-control col-4 d-flex justify-content-center" type="text" v-model="form.username" placeholder="user123" maxlength="255" required>
                        <label>Username</label>
                        <div v-if="form.errors.username" class="alert-danger">
                            <ul>
                                <li>{{ form.errors.username }}</li>
                            </ul>
                        </div>
                    </div>
                    <div class="form-floating mb-3">
                        <input class="form-control" type="email" v-model="form.email" placeholder="example@example.com" minlength="8" maxlength="255" required>
                        <label>E-mail</label>
                        <div v-if="form.errors.email" class="alert-danger">
                            <ul>
                                <li>{{ form.errors.email }}</li>
                            </ul>
                        </div>
                    </div>
                    <div class="form-floating mb-3">
                        <input class="form-control" type="password" v-model="form.password" placeholder="minimum 8 characters" minlength="8" maxlength="255" required>
                        <label>Password</label>
                        <div v-if="form.errors.password" class="alert-danger">
                            <ul>
                                <li>{{ form.errors.password }}</li>
                            </ul>
                        </div>
                    </div>
                    <div class="form-floating mb-3">
                        <input class="form-control" type="password" v-model="form.password_confirmation" placeholder="Must match password" minlength="8" maxlength="255" required>
                        <label>Confirm Password</label>
                        <div v-if="form.errors.password_confirmation" class="alert-danger">
                            <ul>
                                <li>{{ form.errors.password_confirmation }}</li>
                            </ul>
                        </div>
                    </div>
                    <div class="form-label">
                        <label>Profile Picture</label>
                        <file-pond
                            id="avatar"
                            name="avatar"
                            v-model="form.avatar"
                            ref="pond"
                            label-idle="Drop image here..."
                            v-bind:allow-multiple="false"
                            accepted-file-types="image/jpeg, image/png"
                            :server="{
                                   url: '/tmp/image',
                                   headers: {
                                       'X-CSRF-TOKEN': $page.props.csrf
                                   }
                            }"
                            @processfile="addFile"
                            @removefile="addFile"
                        />
                        <div v-if="form.avatar" class="alert-danger">
                            <ul>
                                <li>{{ form.errors.avatar }}</li>
                            </ul>
                        </div>
                    </div>
                    <button class="btn button-dark mt-2 float-end" :disabled="disableButton()" v-on:click="register">Register</button>
                </form>
            </div>
        </div>
    </div>
    <Footer />
</template>

<script>
import NavigationBar from "../layout/NavigationBar.vue";
import Footer from "../layout/Footer.vue";
// Import Vue FilePond
import vueFilePond from "vue-filepond";
// Import FilePond styles
import "filepond/dist/filepond.min.css";
// Import FilePond plugins
// Please note that you need to install these plugins separately
// Import image preview plugin styles
import "filepond-plugin-image-preview/dist/filepond-plugin-image-preview.min.css";
// Import image preview and file type validation plugins
import FilePondPluginFileValidateType from "filepond-plugin-file-validate-type";
import FilePondPluginImagePreview from "filepond-plugin-image-preview";
// Create component
const FilePond = vueFilePond(
    FilePondPluginFileValidateType,
    FilePondPluginImagePreview,
);
import { useForm } from "@inertiajs/vue3"
export default {
    name: "Register",
    components: {
        NavigationBar,
        Footer,
        FilePond
    },
    data() {
        let form = useForm({
            name : '',
            username : '',
            email : '',
            password : '',
            password_confirmation : '',
            avatar : '',
            _token : this.$page.props.csrf,
        });
        return {
            form
        }
    },
    methods: {
        register() {
            this.form.post(route('register.post'));
        },
        addFile(){
            this.form.avatar = document.getElementsByName("avatar")[0].value;
        },
        disableButton() {
            return this.form.processing
                || this.form.name === ''
                || this.form.username === ''
                || this.form.email === ''
                || this.form.password === ''
                || this.form.password_confirmation === ''
        }
    }
};
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
    input
        background: #1b1a1f
        color: #fff
        caret-color: #ffffff
        border-color: #6B6760
        &:focus
            background: #1b1a1f
            color: #fff
            border-color: #6B6760
            box-shadow: none
            outline: 0
</style>
