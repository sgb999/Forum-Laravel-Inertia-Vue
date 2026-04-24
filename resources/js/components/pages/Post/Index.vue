<template>
    <Head title="View Post" />
    <navigation-bar />
    <div class="container">
        <h3>{{post.title}}</h3>
        <p>{{post.content}}</p>
        <div class="user">
            <img class="avatar" :src="post.user.profilePicture?.thumb || '/storage/default/avatar.png'" alt="avatar">
            <inertia-link :href="route('user.index', { username : post.user.username,  user_id: post.user.id })">
                {{post.user.username}}
            </inertia-link>
            <p>{{ this.formatDate(post.createdAt) }}</p>
        </div>
        <div v-if="post.user.id === $page.props.auth.user.id" class="mt-3">
            <inertia-link :href="route('post.index', post.id)" id="edit" class="btn btn-primary col-1 btn-style">Edit</inertia-link>
            <button class="btn btn-danger" @click="deletePost">Delete</button>
        </div>
        <hr>
    </div>
    <div class="container">
        <h3>Comments</h3>
        <Deferred data="comments">
            <template #fallback>
                <page-loader />
            </template>
            <comment :comments="comments"/>
        </Deferred>
    </div>
    <Footer />
</template>

<script>
import { useForm, Deferred } from "@inertiajs/vue3";
import NavigationBar from "../../layout/NavigationBar.vue";
import Footer from "../../layout/Footer.vue";
import Comment from "../Comment.vue";
import moment from "moment/moment";
import PageLoader from "../PageLoader.vue";
export default {
    name: "Index",
    props:{
        post: {
            required: true
        },
        comments: {
            required: true
        }
    },
    components: {
        PageLoader,
        NavigationBar,
        Footer,
        Comment,
        Deferred
    },
    data(){
        let form = useForm({
            _token : this.$page.props.csrf
        });
        return {
            post: this.post.data,
            avatar: '',
            form
        }
    },
    methods: {
      deletePost(){
          this.$swal({
              title: 'Are you sure you want to delete your post?',
              text: 'Your post will be gone forever!',
              icon: 'warning',
              showConfirmButton: true,
              showCancelButton: true,
              dangerMode: true
          }).then((result) => {
              if(result.isConfirmed){
                  this.form.delete(route('post.destroy', this.post.id));
              }
              else{
                  return false;
              }
          });
      },
      formatDate(value)
      {
        return moment(String(value)).format('DD/MM/YYYY H:MM a')
      }
    },
    mounted(){
        /**this.post.user.media.forEach(el => {
            if(el.collection_name === 'avatar'){
                this.avatar = el.original_url;
            }
        })*/
    }
};
</script>

<style scoped lang="sass">
.user
    padding: 10px
    width: fit-content
    border-radius: 10px
    background-color: #2A2724
    a
        text-decoration: underline
        color: #fff
    .avatar
        height: 32px
        width: 32px
        border-radius: 50%
        border: solid 2px #FFFFFF
        margin-right: 20px
        color: rgb(228, 230, 235)
    p
        padding-left: 51px
a
    color: #000000
    text-decoration: none
    margin-right: 3px
hr
    font-weight: bold
.btn-style
    color: #ffffff
#edit
    width: 70px
</style>
