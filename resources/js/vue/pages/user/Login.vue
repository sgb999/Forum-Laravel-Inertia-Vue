<template>
    <Head><title>Login</title></Head>
    <div class="container">
        <div class="card">
            <div class="card-header">
                <h1>Login</h1>
            </div>
            <div class="card-body">
                <Form :action="route('login.post')"
                      method="post"
                      disableWhileProcessing
                      @success="successfulLogin"
                      @error="failedLogin"
                      #default = '{
                        processing,
                        errors
                }'>
                    <div class="form-floating mb-3">
                        <input id="email" name="email" v-model="email" class="form-control" type="email" placeholder="example@example.com" minlength="8" maxlength="255" required>
                        <label for="email">E-mail</label>
                        <div v-if="errors.email" class="alert-danger">
                            <ul>
                                <li>{{ errors.email }}</li>
                            </ul>
                        </div>
                    </div>
                    <div class="form-floating">
                        <input id="password" name="password" v-model="password" class="form-control" type="password" placeholder="minimum 8 characters"  maxlength="255" required>
                        <label for="password">Password</label>
                    </div>
                    <button data-testid="login-button" class="btn button-dark mt-2 float-end" :disabled="disableButton() || processing" type="submit">Login</button>
                </Form>
            </div>
        </div>
    </div>
</template>

<script setup lang="ts">

// Vue
import { ref } from "vue";

// Inertia
import { Form } from "@inertiajs/vue3"

// Layout
import appLayout from "../../layout/AppLayout.vue";

// Components
import Swal from 'sweetalert2';

defineOptions({
    name: 'Login',
    layout: appLayout
});

let email = ref('');
let password = ref('');

function disableButton(): boolean
{
    return email.value === '' || password.value === '';
}

function successfulLogin(): void
{
    Swal.fire({
        title: 'You are now logged in!',
        text: '',
        icon: 'success',
        timer: 3000
    });
}

function failedLogin(): void
{
    Swal.fire({
        title: 'The provided credentials do not match our records.',
        text: '',
        icon: 'error',
        timer: 3000
    });
}
</script>

<style scoped lang="sass">
@media (min-device-width: 768px)
    .container
        width: 50%
.card
    background: var(--color-surface-alt)
    color: var(--color-text)
    label
        color: var(--color-text-subtle)
input
    background: var(--color-input)
    color: var(--color-text)
    caret-color: var(--color-text)
    border-color: var(--color-text-subtle)
    &:focus
        background: var(--color-input)
        color: var(--color-text)
        border-color: var(--color-text-subtle)
        box-shadow: none
        outline: 0
</style>
