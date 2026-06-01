<template>
    <Head><title>Categories</title></Head>
    <div class="container">
        <h2>Categories</h2>
        <hr>
        <inertia-link v-for="category in categories"
           :href="route('post.show',{ category_id: category.id})">
            <h3>{{ category.name }}</h3>
            <p>{{ category.description }}</p>
        </inertia-link>
    </div>
</template>

<script>
import appLayout from "../../layout/AppLayout.vue";
export default {
    name: "Show",
    layout: appLayout,
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
