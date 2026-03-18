<template>
    <Head title="Message" />
    <navigation-bar />
    <div class="container">
        <div class="grid">
            <div v-for="message in messages">
                <hr>
                <div class="card">
                    <div v-bind:class="'card-body ' + (message.user.id === $page.props.auth.user.id) ? 'user': 'not-user'" class="message">
                        <p>{{ message.message }}</p>
                        <inertia-link :href="route('user.profile', message.user.username)">{{ message.user.username }}</inertia-link>
                    </div>
                </div>
            </div>
        </div>
        <hr>
        <form @submit.prevent>
            <div class="message">
                <div class="form-floating">
                    <textarea id="messageBox" v-model="form.message" class="form-control" placeholder="Send a Message"></textarea>
                    <label for="messageBox">Send a Message</label>
                    <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                        <button @click="sendMessage" type="submit" class="btn button-dark float-end">Submit</button>
                    </div>
                </div>
            </div>
        </form>
    </div>
    <Footer />
</template>

<script>
import {useForm} from "@inertiajs/vue3";
import NavigationBar from "../layout/NavigationBar.vue";
import Footer from "../layout/Footer.vue";
export default {
    name: "message",
    components: {
        NavigationBar,
        Footer
    },
    props:{
        id:{
          required: true
        },
        messages: {
          required: false
        }
    },
    data(){
        const form = useForm({
            chat_id: this.id,
            message : '',
            _token : this.$page.props.csrf,
        });
        return {
            form,
            messages: []
        }
    },
    methods:{
        getChats(){
            axios.get(route('message.index', this.id)).then((response) => {
                this.messages = response.data;
            }).catch((error) => {
                console.log('Error: ' + error)
            });
        },
        sendMessage(){
            this.form.post(route('message.store'), {
                onSuccess: () => {
                    this.form.message = '';
                }
            })
        }
    },
    mounted() {
        window.setInterval(() => {
            this.getChats()
        }, 1000);
    }
};
</script>

<style scoped lang="sass">
.user
    text-align: right

a
    text-decoration: none
    color: #FFFFFF
.message
    background: #242220
    color: #fff
    label
        color: #6B6760
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
    button
        margin-top: 25px
        height: 40px
        margin-left: 10px
</style>
