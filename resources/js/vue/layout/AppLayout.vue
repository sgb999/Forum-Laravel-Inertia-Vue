<template xmlns="http://www.w3.org/1999/html">
    <navigation-bar />


    <main>
        <slot />
    </main>

    <Footer />
</template>

<script setup>

import NavigationBar from "./NavigationBar.vue";
import Footer from "./Footer.vue";
</script>

<style lang="sass">

::view-transition-old(page-content)
    animation: 300ms ease forwards slide-fade-out

::view-transition-new(page-content)
    animation: 300ms ease forwards slide-fade-in

main
    view-transition-name: page-content

@keyframes slide-fade-out
    to
        opacity: 0
        translate: -40px

@keyframes slide-fade-in
    from
        opacity: 0
        translate: 40px
</style>
