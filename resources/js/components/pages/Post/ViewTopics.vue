<template>
    <Head><title>View Topics</title></Head>
    <div class="container">
        <page-loader  v-if="!topics"/>
        <div v-if="topics"
           v-for="topic in topics"
            class="card">
                <div class="card-header">
                    <inertia-link :href="route('post.index', topic.id)">
                        <h3>{{topic.title}}</h3>
                    </inertia-link>
                </div>
                <div class="card-body">
                    <inertia-link :href="route('user.index', topic.user.username, { user_id: topic.user.id })">
                        <p>{{topic.user.username}}</p>
                    </inertia-link>
                    <inertia-link href="#">
                        <p>{{ topic.category.name }}</p>
                    </inertia-link>
                    <p>{{ this.formatDate(topic.createdAt) }}</p>
                </div>
        </div>
    </div>
</template>

<script>
import PageLoader from "../PageLoader.vue";
import moment from "moment"
export default {
    name: "viewTopics",
    components:{
      PageLoader
    },
    props:{
        topics: {
            type: Array,
            required: false
        }
    },
  methods:{
      formatDate(value)
      {
        return moment(String(value)).format('DD/MM/YYYY H:MM a')
      }
  }
};
</script>

<style lang="sass" scoped>
a, a:hover, a:focus, a:active
    text-decoration: none
    color: inherit
.container
    display: grid
    grid-template-columns: repeat(auto-fit, minmax(300px, 1fr))
    grid-column-gap: 20px
    grid-row-gap: 15px
    height: fit-content
    .card
        background: #242220
        color: #fff
        border-radius: 25px
        animation: posts-load running 500ms
        transition: transform 0.8s, box-shadow 0.8s
        &:hover
            transform: translateY(-5px)
            box-shadow: 0 5px 15px rgba(255, 255, 255, 0.3)
    @keyframes posts-load
        0%
            transform: translateX(-100%)
        100%
            transform: translateX(0)
@media screen and (max-width: 600px)
    .container
        .grid:hover
            transform: none
</style>
