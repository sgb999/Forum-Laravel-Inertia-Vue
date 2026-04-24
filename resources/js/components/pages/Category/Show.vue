<template>
    <Head><title>Categories</title></Head>
    <navigation-bar />
    <div class="container">
        <h2>Categories</h2>
        <hr>
        <inertia-link v-for="category in categories.data"
           :href="route('post.show',{ category_id: category.id})">
            <h3>{{ category.name }}</h3>
            <p>{{ category.description }}</p>
        </inertia-link>
    </div>
    <Footer />
</template>

<script>
import NavigationBar from "../../layout/NavigationBar.vue";
import Footer from "../../layout/Footer.vue";
export default {
    name: "Categories",
    components: {
        NavigationBar,
        Footer
    },
    props:{
        categories: {
            required: true
        }
    }
};
</script>

<style scoped>
a, a:hover, a:focus, a:active {
    text-decoration: none;
    color: inherit;
}
h3{
    text-decoration: underline;
}
</style>
