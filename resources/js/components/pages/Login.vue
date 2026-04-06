<template>
    <Head><title>Login</title></Head>
    <navigation-bar />
    <div class="container w-50">
        <div class="card">
            <div class="card-header">
                <h1>Login</h1>
            </div>
            <div class="card-body">
                <form @submit.prevent>
                    <div class="form-floating mb-3">
                        <input id="email" name="email" v-model="form.email" class="form-control" type="email" placeholder="example@example.com" minlength="8" maxlength="255" required>
                        <label for="email">E-mail</label>
                        <div v-if="form.errors.email" class="alert-danger">{{ form.errors.email }}</div>
                    </div>
                    <div class="form-floating">
                        <input id="password" name="password" v-model="form.password" class="form-control" type="password" placeholder="minimum 8 characters"  maxlength="255" required>
                        <label for="password">Password</label>
                    </div>
                    <button data-testid="login-button" class="btn button-dark mt-2 float-end" :disabled="form.processing || form.email === '' || form.password === ''" v-on:click="login">Login</button>
                </form>
            </div>
        </div>
    </div>
    <Footer />
</template>

<script>
import NavigationBar from "../layout/NavigationBar.vue";
import Footer from "../layout/Footer.vue";
import { useForm } from "@inertiajs/vue3"
export default {
    name: "Login",
    components: {
        NavigationBar,
        Footer
    },
    data() {
        let form = useForm({
            email : '',
            password : '',
            _token : this.$page.props.csrf,
        });
        return {
            errors: [],
            form
        }
    },
    methods: {
        login(){
            this.form.post(route('login.post'), {
                onSuccess: () => {
                    this.$swal({
                        title: 'You are now logged in!',
                        text: '',
                        icon: 'success',
                        timer: 3000
                    });
                },
                onError: () => {
                    this.$swal({
                        title: 'The provided credentials do not match our records.',
                        text: '',
                        icon: 'error',
                        timer: 3000
                    });
                }
            });
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
