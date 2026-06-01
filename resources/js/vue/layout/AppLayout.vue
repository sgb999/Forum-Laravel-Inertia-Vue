<template xmlns="http://www.w3.org/1999/html">
    <navigation-bar />

    <slot></slot>

    <Footer />
</template>

<script setup>

import NavigationBar from "./NavigationBar.vue";
import Footer from "./Footer.vue";
</script>

<style scoped lang="sass">

</style>
