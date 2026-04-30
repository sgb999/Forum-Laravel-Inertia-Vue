<template>
    <Head title="Message" />
    <navigation-bar />
    <div class="container w-75">
        <div class="user m-auto text-center">
            <img class="avatar d-block mx-auto" :src="user.profilePicture?.thumb || '/storage/default/avatar.png'" alt="avatar">
            <inertia-link :href="route('user.profile', user.username)">
                {{user.username}}
            </inertia-link>
        </div>
        <hr>
        <div class="chat-area overflow-y-auto" ref="chatArea">
            <div v-for="message in messages" class="msg-row" :class="(message.user.id === $page.props.auth.user.id) ? 'sent' : 'received'">
                <div :class="(message.user.id === $page.props.auth.user.id) ? 'bubble sent' : 'bubble received'">
                    <p>{{ message.message }}</p>
                </div>
            </div>
        </div>
        <hr>
        <form class="message-box" @submit.prevent>
            <div class="form-floating mb-3">
                <textarea class="form-control" placeholder="Content" v-model="form.message" id="formContent"></textarea>
                <label for="formContent">Content</label>
                <div v-if="form.errors.message" class="alert-danger">
                    <ul>
                        <li>{{ form.errors.message }}</li>
                    </ul>
                </div>
            </div>
            <button class="btn button-dark mt-2 float-end" :disabled="form.message === ''" v-on:click="sendMessage">Send</button>
        </form>
    </div>
    <Footer />
</template>

<script>
import { useForm } from "@inertiajs/vue3";
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
        user: {
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
            user: this.user.data,
            messages: this.messages.data,
        }
    },
    methods:{
        scrollToBottom() {
            this.$nextTick(() => {
                const chat = this.$refs.chatArea
                if (chat) chat.scrollTop = chat.scrollHeight
            })
        },
        getChats(){
            axios.get(route('message.index', this.id)).then((response) => {
                this.messages = response.data;
                this.scrollToBottom();
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
        }, 5000);
    }
};
</script>

<style scoped lang="sass">
a
    text-decoration: none
    color: #FFFFFF
.chat-area
    height: 60vh
    padding: 24px 20px
    display: flex
    flex-direction: column
    gap: 12px
    border-bottom: 1px solid #2A2724
.msg-row
    display: flex
.sent
    justify-content: flex-end
.msg-row.received
    justify-content: flex-start
.bubble
    max-width: 65%
    padding: 10px 14px
    font-size: 14px
    line-height: 1.5
.bubble.sent
    background: #2B6FD4
    color: #E8F1FC
    border-radius: 14px 14px 4px 14px
.bubble.received
    background: #242220
    color: #C8C4BC
    border-radius: 14px 14px 14px 4px
    border: 1px solid #2E2B28
.avatar
    height: 150px
    width: 150px
    border-radius: 50%
    border: solid 2px #FFFFFF
    margin-right: 20px
.message-box
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
</style>
