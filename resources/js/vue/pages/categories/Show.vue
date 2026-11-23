<template>
    <Head><title>Categories</title></Head>
    <div class="container">
        <h1 class="h2">Categories</h1>
        <hr>
        <inertia-link v-for="category in categories"
           :href="route('post.show',{ category_id: category.id})">
            <h3>{{ category.name }}</h3>
            <p>{{ category.description }}</p>
        </inertia-link>
    </div>
</template>

<script setup lang="ts">

// Layout
import appLayout from "../../layout/AppLayout.vue";

// Types
import { Category } from "../../types/Category";

defineOptions({
    name: "Show",
    layout: appLayout
});
defineProps<{
    categories: Category[];
}>();
</script>

<style scoped lang="sass">
a, a:hover, a:focus, a:active
    text-decoration: none
    color: inherit
h3
    text-decoration: underline
</style>
