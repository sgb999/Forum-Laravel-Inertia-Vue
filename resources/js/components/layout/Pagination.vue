<template>
    <nav v-if="links.links.length > 3" class="mt-2">
        <ul class="pagination desktop">
            <li class="page-item" v-for="link in links.links">
                <button v-if="link.url !== null" @click="paginate(link.url)"
                        :class="{'btn button-dark' :  link.url, 'font-bold' : link.active}">
                    {{ link.label}}
                </button>
            </li>
        </ul>
        <ul class="pagination mobile">
            <li class="page-item">
                <button v-if="links.links[0].url" @click="paginate(links.links[0].url)"
                        :class="{'page-link' :  links.links[0].url, 'font-bold' : links.links[0].active}">
                    {{ links.links[0].label}}
                </button>
            </li>
            <li class="page-item">
                <button v-if="links.links[links.links.length - 1].url" @click="paginate(links.links[links.links.length - 1].url)"
                        :class="{'btn button-dark' :  links.links[links.links.length - 1].url, 'font-bold' : links.links[links.links.length - 1].active}">
                    {{ links.links[links.links.length - 1].label}}
                </button>
            </li>
        </ul>
    </nav>
</template>

<script>

import { router } from '@inertiajs/vue3'
export default {
    name: "Pagination",
    props: {
        links: {
            required: true
        }
    },
    methods: {
        paginate(page) {
            router.get(page);
        }
    }
};
</script>

<style scoped lang="sass">
.desktop
    display: flex
.mobile
    display: none
</style>
