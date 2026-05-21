<template>
    <img class="banner" :src="user.data.bannerPicture.url" alt="banner">
    <div id="update-profile-page" class="container">
        <div class="user">
            <img class="avatar" :src="user.data.profilePicture.url" alt="avatar">
            <h1 id="name-tag">{{ user.data.name }}</h1>
            <h1 id="username-tag">{{ user.data.username }}</h1>
            <h1 id="email-tag">{{ user.data.email }}</h1>
        </div>
      <hr />
        <div>
            <Form :action="route('user.update', $page.props.auth.user.id)" method="put"
                  #default = '{
                    processing,
                    isDirty,
                    errors
                  }'
                  @success="resetBanner && sweetAlertSuccess('banner')"
                  @error="sweetAlertError('banner')">
                <div class="row">
                    <label for="name">Profile Banner</label>
                    <div class="col">
                        <file-pond
                            ref="bannerPond"
                            name="banner"
                            :storeAsFile="true"
                        />
                        <div v-if="errors.banner" class="alert-danger">
                            <ul>
                                <li>{{ errors.banner }}</li>
                            </ul>
                        </div>
                    </div>
                    <div class="col">
                        <button type="submit" :disabled="processing || !isDirty" class="btn button-dark">Update Profile Banner</button>
                    </div>
                </div>
            </Form>
            <Form :action="route('user.update', $page.props.auth.user.id)" method="put"
                  #default = '{
                    processing,
                    isDirty,
                    errors
                  }'
                  @success="$refs.avatarPond.removeFiles() && sweetAlertSuccess('profile picture')"
                  @error="sweetAlertError('profile picture')">
                <div class="row">
                    <label for="name">Profile Picture</label>
                    <div class="col">
                        <file-pond
                            ref="avatarPond"
                            name="avatar"
                            :storeAsFile="true"
                        />
                        <div v-if="errors.avatar" class="alert-danger">
                            <ul>
                                <li>{{ errors.avatar }}</li>
                            </ul>
                        </div>
                    </div>
                    <div class="col">
                        <button type="submit" :disabled="processing || !isDirty" class="btn button-dark">Update Profile Picture</button>
                    </div>
                </div>
            </Form>
            <Form :action="route('user.update', $page.props.auth.user.id)" method="put"
                  #default = '{
                    processing,
                    isDirty,
                    errors
                  }'
                  @success="sweetAlertSuccess('name')"
                  :resetOnSuccess="['name']"
                  @error="sweetAlertError('name')">
                <div class="row">
                    <div class="col">
                        <div class="form-floating">
                            <input id="name" name="name" class="form-control col-4 d-flex justify-content-center" type="text"
                                   placeholder="John Doe"  maxlength="255"
                                   autocomplete="off">
                            <label for="name">Name</label>
                            <div v-if="errors.name" class="alert-danger">
                                <ul>
                                    <li>{{ errors.name }}</li>
                                </ul>
                            </div>
                        </div>
                    </div>
                    <div class="col">
                        <button type="submit" :disabled="processing || !isDirty" class="btn button-dark">Update Name</button>
                    </div>
                </div>
            </Form>
            <Form :action="route('user.update', $page.props.auth.user.id)" method="put"
                  #default = '{
                    processing,
                    isDirty,
                    errors
                  }'
                  @success="sweetAlertSuccess('username')"
                  :resetOnSuccess="['username']"
                  @error="sweetAlertError('username')">
                <div class="row mt-3">
                    <div class="col">
                        <div class="form-floating">
                            <input id="username" class="form-control col-4 d-flex justify-content-center" type="text"
                                   name="username" :placeholder="user.data.name" maxlength="255" autocomplete="off">
                            <label for="username">Username</label>
                            <div v-if="errors.username" class="alert-danger">
                                <ul>
                                    <li>{{ errors.username }}</li>
                                </ul>
                            </div>
                        </div>
                    </div>
                    <div class="col">
                        <button type="submit" :disabled="processing || !isDirty" class="btn button-dark">Update username</button>
                    </div>
                </div>
            </Form>
            <Form :action="route('user.update', $page.props.auth.user.id)" method="put"
                  #default = '{
                    processing,
                    isDirty,
                    errors
                  }'
                  @success="sweetAlertSuccess('email')"
                  :resetOnSuccess="['email']"
                  @error="sweetAlertError('email')">
            <div class="row mt-3">
                <div class="col">
                    <div class="form-floating">
                        <input name="email" id="email" class="form-control col-4 d-flex justify-content-center" type="text"
                               placeholder="example@example.com" maxlength="255" autocomplete="off">
                        <label for="email">Email</label>
                        <div v-if="errors.email" class="alert-danger">
                            <ul>
                                <li>{{ errors.email }}</li>
                            </ul>
                        </div>
                    </div>
                </div>
                <div class="col">
                    <button type="submit" :disabled="processing || !isDirty" class="btn button-dark">Update E-mail Address</button>
                </div>
            </div>
            </Form>
            <Form :action="route('user.update', $page.props.auth.user.id)" method="put"
                  #default = '{
                    processing,
                    isDirty,
                    errors
                  }'
                  @success="sweetAlertSuccess('password')"
                  :resetOnSuccess="['password', 'password_confirmation']"
                  @error="sweetAlertError('password')">
            <div class="row mt-3">
                <div class="col">
                    <div class="form-floating">
                        <input name="password" id="password" class="form-control col-4 d-flex justify-content-center" type="text"
                               placeholder="Password: Minimum 8 characters" minlength="8" maxlength="255"
                               autocomplete="off">
                        <label for="password">Password</label>
                        <div v-if="errors.password">
                            <ul>
                                <li class="alert alert-danger">{{ errors.password }}</li>
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
                               type="text" name="password_confirmation" placeholder="Must match Password"
                               maxlength="255" autocomplete="off">
                        <label for="password_confirmation">Confirm Password</label>
                        <div v-if="errors.password_confirmation" class="alert-danger">
                            <ul>
                                <li>{{ errors.password_confirmation }}</li>
                            </ul>
                        </div>
                    </div>
                </div>
                <div class="col">
                    <button type="submit" :disabled="processing || !isDirty" class="btn button-dark">Update Password</button>
                </div>
            </div>
            </Form>
            <button id="delete-button" class="btn btn-danger" @click="deleteProfile">Delete Profile</button>
        </div>
    </div>
</template>

<script>
import appLayout from "../layout/AppLayout.vue";
import { Form } from "@inertiajs/vue3"


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
    name: "update-profile",
    layout: appLayout,
    components: {
        Form,
        FilePond
    },
    props: {
        user: {
            required: true
        }
    },
    methods:{
        resetBanner() {
            this.$refs.bannerPond.removeFiles();
        },
        resetAvatar() {
            this.$refs.avatarPond.removeFiles();
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
        },
        sweetAlertError(message)
        {
            this.$swal({
                title: 'Ooops, something went wrong updating your ' + message,
                text: '',
                icon: 'error',
                timer: 3000
            });
        }
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
