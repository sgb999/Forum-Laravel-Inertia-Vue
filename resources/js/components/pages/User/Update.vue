<template>
    <navigation-bar />
    <img class="banner" :src="banner ? banner : '/storage/default/banner.jpg'" alt="banner">
    <div id="update-profile-page" class="container">
        <div class="user">
            <img class="avatar" :src="avatar ? avatar : '/storage/default/avatar.png'" alt="avatar">
            <h1 id="name-tag">{{ user.name }}</h1>
            <h1 id="username-tag">{{ user.username }}</h1>
            <h1 id="email-tag">{{ user.email }}</h1>
        </div>
      <hr />
        <form @submit.prevent>
            <div class="row">
                <label for="name">Profile Banner</label>
                <div class="col">
                    <file-pond
                        name="banner"
                        ref="pond"
                        label-idle="Drop image here..."
                        v-bind:allow-multiple="false"
                        accepted-file-types="image/jpeg, image/png"
                        :allowFileSizeValidation="true"
                        maxFileSize="2MB"
                        labelMaxFileSizeExceeded="File is too large"
                        :required="false"
                        :server="{
                           url: '/tmp/image',
                           headers: {
                               'X-CSRF-TOKEN': $page.props.csrf
                           }
                    }"
                        @processfile="profileBanner"
                        @removefile="profileBanner"
                    />
                </div>
                <div class="col">
                    <button class="btn button-dark" :disabled="form.banner === ''" @click="updateProfileBanner">Update Profile Banner</button>
                </div>
            </div>
            <div class="row">
                <label for="name">Profile Picture</label>
                <div class="col">
                    <file-pond
                        name="avatar"
                        ref="pond"
                        label-idle="Drop image here..."
                        v-bind:allow-multiple="false"
                        :required="false"
                        accepted-file-types="image/jpeg, image/png"
                        :allowFileSizeValidation="true"
                        maxFileSize="1MB"
                        labelMaxFileSizeExceeded="File is too large"
                        :server="{
                           url: '/tmp/image',
                           headers: {
                               'X-CSRF-TOKEN': $page.props.csrf
                           }
                    }"
                        @processfile="profilePicture"
                        @removefile="profilePicture"
                    />
                </div>
                <div class="col">
                    <button class="btn button-dark" :disabled="form.avatar === ''" @click="updateProfilePicture">Update Profile Picture</button>
                </div>
            </div>
            <div class="row">
                <div class="col">
                    <div class="form-floating">
                        <input v-model="form.name" id="name" class="form-control col-4 d-flex justify-content-center" type="text"
                               placeholder="John Doe"  maxlength="255"
                               autocomplete="off">
                        <label for="name">Name</label>
                        <div v-if="$page.props.errors.name" class="alert-danger">
                            <ul>
                                <li>{{ $page.props.errors.name }}</li>
                            </ul>
                        </div>
                    </div>
                </div>
                <div class="col">
                    <button class="btn button-dark" :disabled="form.name === ''" @click="updateName">Update Name</button>
                </div>
            </div>
            <div class="row mt-3">
                <div class="col">
                    <div class="form-floating">
                        <input id="username" class="form-control col-4 d-flex justify-content-center" type="text"
                               v-model="form.username" :placeholder="user.name" maxlength="255" autocomplete="off">
                        <label for="username">Username</label>
                        <div v-if="$page.props.errors.username" class="alert-danger">
                            <ul>
                                <li>{{ $page.props.errors.username }}</li>
                            </ul>
                        </div>
                    </div>
                </div>
                <div class="col">
                    <button @click="updateUsername" class="btn button-dark" :disabled="form.username === ''">Update username</button>
                </div>
            </div>
            <div class="row mt-3">
                <div class="col">
                    <div class="form-floating">
                        <input v-model="form.email" id="email" class="form-control col-4 d-flex justify-content-center" type="text"
                               placeholder="example@example.com" maxlength="255" autocomplete="off">
                        <label for="email">Email</label>
                        <div v-if="$page.props.errors.email" class="alert-danger">
                            <ul>
                                <li>{{ $page.props.errors.email }}</li>
                            </ul>
                        </div>
                    </div>
                </div>
                <div class="col">
                    <button class="btn button-dark" :disabled="form.email === ''" @click="updateEmail">Update E-mail Address</button>
                </div>
            </div>
            <div class="row mt-3">
                <div class="col">
                    <div class="form-floating">
                        <input v-model="form.password" id="password" class="form-control col-4 d-flex justify-content-center" type="text"
                               placeholder="Password: Minimum 8 characters" minlength="8" maxlength="255"
                               autocomplete="off">
                        <label for="password">Password</label>
                        <div v-if="$page.props.errors.password" class="alert-danger">
                            <ul>
                                <li>{{ $page.props.errors.password }}</li>
                            </ul>
                        </div>
                    </div>
                </div>
                <div class="col">
                </div>
            </div>
            <div class="row mt-3">
                <div class="col">
                    <div class="form-floating">
                        <input id="password_confirmation" class="form-control col-4 d-flex justify-content-center"
                               type="text" v-model="form.password_confirmation" placeholder="Must match Password"
                               maxlength="255" autocomplete="off">
                        <label for="password_confirmation">Confirm Password</label>
                        <div v-if="$page.props.errors.password_confirmation" class="alert-danger">
                            <ul>
                                <li>{{ $page.props.errors.password_confirmation }}</li>
                            </ul>
                        </div>
                    </div>
                </div>
                <div class="col">
                    <button class="btn button-dark" :disabled="form.password === '' || form.password_confirmation === ''" @click="updatePassword">Update Password</button>
                </div>
            </div>
            <button id="delete-button" class="btn btn-danger" @click="deleteProfile">Delete Profile</button>
        </form>
    </div>
    <Footer />
</template>

<script>
import NavigationBar from "../../layout/NavigationBar.vue";
import Footer from "../../layout/Footer.vue";

import vueFilePond from "vue-filepond";

// Import FilePond styles
import "filepond/dist/filepond.min.css";

// Import FilePond plugins
// Please note that you need to install these plugins separately

// Import image preview plugin styles
import "filepond-plugin-image-preview/dist/filepond-plugin-image-preview.min.css";
import FilePondPluginImageValidateSize from 'filepond-plugin-image-validate-size';
// Import image preview and file type validation plugins
import FilePondPluginFileValidateType from "filepond-plugin-file-validate-type";
import FilePondPluginImagePreview from "filepond-plugin-image-preview";

// Create component
const FilePond = vueFilePond(
    FilePondPluginFileValidateType,
    FilePondPluginImagePreview,
    FilePondPluginImageValidateSize
);
export default {
    name: "Update",
    components: {
        NavigationBar,
        Footer,
        FilePond
    },
    props: {
        user: {
            required: true
        }
    },
    data(){
        return{
            banner: '',
            avatar: '',
            form : {
                banner: '',
                avatar: '',
                name: '',
                username: '',
                email: '',
                password: '',
                password_confirmation: ''
            }
        }
    },
    methods:{
        updateName(){
            const object = {
                name : this.form.name
            }
            this.update(object, 'name')
        },
        updateUsername(){
            const object = {
                username : this.form.username
            }
            this.update(object, 'username')
        },
        updateEmail(){
            const object = {
                email : this.form.email
            }
            this.update(object, 'email')
        },
        updatePassword(){
            const object = {
                password : this.form.password,
                password_confirmation: this.form.password_confirmation,
            }
            this.update(object, 'password')
        },
        update(object, attribute)
        {
            object._token = this.$page.props.csrf;
            this.$inertia.post(route('user.edit', this.user.id), object,{
                onSuccess: () => {
                    switch(attribute){
                        case 'name':
                            this.user.name = this.form.name;
                            this.form.name = '';
                            break;
                        case 'username':
                            this.user.username = this.form.username;
                            this.form.username = '';
                            break;
                        case 'email':
                            this.user.email = this.form.email;
                            this.form.email = '';
                            break;
                        case 'password':
                            this.form.password = '';
                            this.form.password_confirmation = '';
                            break;
                        case 'profile picture':
                            document.getElementsByName("avatar")[0].value = '';
                            this.form.avatar = '';
                            break;
                        case 'profile banner':
                            document.getElementsByName("banner")[0].value = '';
                            this.form.banner = '';
                    }
                  axios.get('/user/' + this.user.id).then(resp => {
                    console.log(resp);
                    resp.data.media.forEach(el => {
                      if(el.collection_name === 'banner'){
                        this.banner = el.original_url;
                      }
                      if(el.collection_name === 'avatar'){
                        this.avatar = el.original_url;
                      }
                    });
                  });
                    this.sweetAlertSuccess(attribute); // fire success message
                },
                onError: () => {
                    this.$swal({
                        title: 'Ooops, something went wrong',
                        text: '',
                        icon: 'error',
                        timer: 3000
                    });
                }
            });
            //this.updateImages();
        },
      getUserData(){
        axios.get('/user/' + this.user.id).then(resp => {
          console.log(resp.data);
          resp.data.media.forEach(el => {
            if(el.collection_name === 'banner'){
              this.banner = el.original_url;
            }
            if(el.collection_name === 'avatar'){
              this.avatar = el.original_url;
            }
          });
        });
      },
        updateProfilePicture() {
            const object = {
                avatar : this.form.avatar,
                _method : 'PUT'
            }
            this.update(object, 'profile picture');
            //this.updateImages();
        },
        updateProfileBanner() {
            const object = {
                banner : this.form.banner,
                _method : 'PUT'
            };
            this.update(object, 'profile banner');
            //this.updateImages();
        },
        updateImages(){
            const id = this.user.id;
            setTimeout(function(){
                axios.get('/user/' + id).then(resp => {
                    resp.data.media.forEach(el => {
                        if(el.collection_name === 'banner'){
                            this.banner = el.original_url;
                        }
                        if(el.collection_name === 'avatar'){
                            this.avatar = el.original_url;
                        }
                    });
                });
            }, 3000);
        },
        profilePicture(){
            this.form.avatar = document.getElementsByName("avatar")[0].value;
        },
        profileBanner(){
            this.form.banner = document.getElementsByName("banner")[0].value;
        },
        deleteProfile() {
            this.$swal({
                title: 'Are you sure you want to delete your profile?',
                text: 'This cannot be undone!',
                icon: 'warning',
                showConfirmButton: true,
                showCancelButton: true,
                dangerMode: true
            }).then((result) => {
                if (result.isConfirmed) {
                    Inertia.delete(route('user.destroy', this.user.id));
                } else {
                    return false;
                }
            });
        },
        sweetAlertSuccess(message)
        {
            this.$swal({
                title: 'Your ' + message + ' has been updated!',
                text: '',
                icon: 'success',
                timer: 3000
            });
        }
    },
    mounted() {
        this.user.media.forEach(el => {
            if(el.collection_name === 'banner'){
                this.banner = el.original_url;
            }
            if(el.collection_name === 'avatar'){
                this.avatar = el.original_url;
            }
        });
    }
};
</script>

<style scoped lang="sass">
#delete-button
    display: block
    margin: 20px auto 0
.banner
  position: relative
  border-radius: 10px
  height: 600px
  width: clamp(400px, 100%, 1296px)
  display: block
  margin: 0 auto
  box-sizing: border-box
.user
  padding-top: 0
  position: relative
  bottom: 45px
  display: grid
  grid-template-columns: 10% 20% 70%
  grid-template-rows: 35px 30px 30px
.avatar
    grid-column: 2/2
    height: 150px
    width: 150px
    border-radius: 50%
    border: solid 2px #FFFFFF
    margin-right: 20px
    color: rgb(228, 230, 235)
#name-tag
    margin-left: 100px
    margin-top: 25px
    margin-bottom: 0
    grid-column: 3/3
    grid-row: 1
#username-tag
    margin-left: 100px
    grid-column: 3/3
    grid-row: 2
    margin-top: 20px
#email-tag
    margin-left: 100px
    grid-column: 3/3
    grid-row: 3
    margin-top: 25px
form
    margin: 10px auto auto
    width: 60%
    border-radius: 10px
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
